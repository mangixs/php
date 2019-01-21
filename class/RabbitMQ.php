<?php
class RabbitMQ
{
    //类实例
    private static $_instance = null;
    //MQ连接对象
    private static $rabbitClient = null;

    /**
     * 构造方法私有化
     */
    private function __construct()
    {
        $conn_args = ['host' => MQ_HOST, 'port' => MQ_PORT, 'login' => MQ_LOGIN, 'password' => MQ_PASSWORD];
        
        $conn = new AMQPConnection($conn_args);
        if ($conn->connect()) {
            self::$rabbitClient = $conn;                           //mq连接对象
        } else {
            throw new Exception("连接MQ失败");
        }
    }

    /**
     * 初始化链接
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     *  发送消息
     * @param string $message 要发送的信息
     * @param string $queue_name 队列名称
     * @param string $key 路由KEY
     * @param string $exchange_name 交换机名称
     * @return mixed
     */
    public function sendMessage($data)
    {
        if (is_null(self::$rabbitClient)) {
            throw new Exception("连接MQ失败");
        }
        //重连机制
        if (self::$rabbitClient->isConnected() == false) {
            if (!self::$rabbitClient->reconnect()) {
                throw new Exception("重新连接MQ失败");
            }
        }
        if (empty($data['queue_name'])) {
            throw new Exception("队列名不能为空");
        }
        $message = empty($data['message']) ? '' : $data['message'];
        $queue_name = $data['queue_name'];
        $key = empty($data['key']) ? [] : $data['key'];
        $exchange_name = empty($data['exchange_name']) ? '' : $data['exchange_name'];
        $exchange_type = empty($data['exchange_type']) ? AMQP_EX_TYPE_DIRECT : $data['exchange_type'];

        try {
            $channel = new AMQPChannel(self::$rabbitClient);              //创建信号通道
            $exchange = new AMQPExchange($channel);         //创建交换机
            $exchange->setName($exchange_name);             //交换机名称
            $exchange->setType($exchange_type);        //交换机类型-指定key
            $exchange->setFlags(AMQP_DURABLE);             //持久化
            $exchange->declareExchange();

            if ($exchange_type == AMQP_EX_TYPE_DIRECT) {
                $queue = new AMQPQueue($channel);               //创建队列
                $queue->setName($queue_name);                   //队列名称
                $queue->setFlags(AMQP_DURABLE);                 //持久化
                $queue->declareQueue();
                $queue->bind($exchange_name, $key);              //交换机绑定key
            } elseif ($exchange_type == AMQP_EX_TYPE_FANOUT) {
                foreach ($queue_name as $val) {
                    $queue = new AMQPQueue($channel);               //创建队列
                    $queue->setName($val);                   //队列名称
                    $queue->setFlags(AMQP_DURABLE);                 //持久化
                    $queue->declareQueue();
                    $queue->bind($exchange_name, $val);              //交换机绑定key
                }
                $key = '';
            }
            //存入的数据一定是字符串
            if (is_array($message) || is_object($message)) {
                $message = json_encode($message);
            }

            $result = $exchange->publish($message,$key);              //发送数据
            if(!$result){
                throw new Exception('发送MQ消息失败');
            }
            return $result;
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 接收消息 demo
     * @param funtion $callback 消息回调方法
     * @return [type] [description]
     */
    public function receiveMessage($callback, $queue_name)
    {
        if (is_null(self::$rabbitClient)) {
            throw new Exception("连接MQ失败");
        }
        //重连机制
        if (self::$rabbitClient->isConnected() == false) {
            if (!self::$rabbitClient->reconnect()) {
                throw new Exception("重新连接MQ失败");
            }
        }
        if (empty($callback) || !is_callable($callback)) {
            throw new Exception("callback 必须是回调函数");
        }

        try {
            $channel = new AMQPChannel(self::$rabbitClient);
            $queue = new AMQPQueue($channel);
            $queue->setName($queue_name);
     
            $queue->consume(function ($envelope, $queue) use ($callback) {
                $msg = $envelope->getBody();               //拿出来的一定是字符串
                $result = call_user_func($callback, $msg);
                if ($result) {
                    $queue->ack($envelope->getDeliveryTag());                //处理成功，移除队列信息
                } else {
                    //nack调用测试后，发现还是删除数据了，如果什么也不返回也不处理，数据会塞回队列,
                    //(但是会塞回队列的前面，不会放到后面，如果当前消费者线程没有关闭，那么那些未处理的数据谁也拿不到，包过当前消费者)
                    $queue->nack($envelope->getDeliveryTag());
                }
            });
        } catch (Exception $e) {
        	throw new Exception($e->getMessage());
        }
    }

    public function getMsg($data)
    {
        if (is_null(self::$rabbitClient)) {
            throw new Exception("连接MQ失败");
        }
        //重连机制
        if (self::$rabbitClient->isConnected() == false) {
            if (!self::$rabbitClient->reconnect()) {
                throw new Exception("重新连接MQ失败");
            }
        }
        try {
            $channel = new AMQPChannel(self::$rabbitClient);
            $i = 0;
            while (true) {
                foreach ($data as $queue_name => $callback) {
                    $queue = new AMQPQueue($channel);
                    $queue->setName($queue_name);
                    $envelope = $queue->get();           //拿出来的一定是字符串
                    if (!$envelope) {
                        continue;
                    }
                    $msg = $envelope->getBody();               //拿出来的一定是字符串
                    $result = call_user_func($callback, $msg);
                    if ($result) {
                        $queue->ack($envelope->getDeliveryTag());                //处理成功，移除队列信息
                    } else {
                        //nack调用测试后，发现还是删除数据了，如果什么也不返回也不处理，数据会塞回队列,
                        //(但是会塞回队列的前面，不会放到后面，如果当前消费者线程没有关闭，那么那些未处理的数据谁也拿不到，包过当前消费者)
                        $queue->nack($envelope->getDeliveryTag());
                    }
                }
                sleep(1);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function __destruct(){
        if(self::$rabbitClient){
            self::$rabbitClient->disconnect();
        }
        self::$rabbitClient = null;
    }
}
