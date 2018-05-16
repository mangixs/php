	<?php
class wx extends \core\Controller {
    private $taken = "hetaibanjia";
    public function __construct() {
        parent::__construct();
    }
    public function valid() {
        $echostr = $_GET['echostr'];
        if ($this->checkSignature()) {
            ob_clean();
            echo $echostr;
            exit;
        }
    }
    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];
        $token     = $this->taken;
        $array     = array($token, $timestamp, $nonce);
        sort($array, SORT_STRING);
        $str    = implode($array);
        $tmpStr = sha1($str);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
    public function index() {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $data    = $postObj->MsgType;
            switch ($data) {
            case 'text':
                $this->responseMsg($postObj);
                break;
            case 'event':
                $this->responseEvent($postObj);
                break;
            case 'image':
                $this->responseImage($postObj);
                break;
            case 'voice':
                $this->responseVoice($postObj);
                break;
            case 'video':
                $this->responseVideo($postObj);
                break;
            case 'shortvideo':
                $this->responseShortVideo($postObj);
                break;
            case 'location':
                $this->responseLocation($postObj);
                break;
            default:
                $msg = '暂不支持该消息回复';
                $this->responseText($postObj, $msg);
                break;
            }
        } else {
            $msg = '暂不支持该消息回复';
            $this->responseText($postObj, $msg);
        }
    }
    public function responseEvent(&$postObj) {
        $event = $postObj->Event;
        switch ($event) {
        case 'CLICK':
            $msg = "菜单点击事件";
            $this->responseText($postObj, $msg);
            break;
        case 'VIEW':
            $this->responseView($postObj);
            break;
        case 'scancode_push':
            $msg = "扫码事件推送";
            $this->responseText($postObj, $msg);
            break;
        case 'SCAN':
            $msg = "你已经关注柳州和泰搬家";
            // $user_id=$postObj->EventKey;
            // $ticket=md5($postObj->Ticket);
            // $user=$postObj->FromUserName;
            // $res=$this->user_scan($user,$ticket);
            // if ($res) {
            //     $tmp=$this->add_integral($user_id);
            //     if ( $tmp ) {
            //         $msg="谢谢关注柳州和泰搬家，你的好友已获得积分";
            //     }
            // }else{
            //     $msg='每个用户扫二维码只能获取积分一次！';
            // }
            $this->responseText($postObj, $msg);
            break;
        case 'subscribe':
            $msg     = "谢谢关注柳州和泰搬家";
            $qrscene = $postObj->EventKey;
            $user_id = str_replace('qrscene_', '', $qrscene);
            $user    = $postObj->FromUserName;
            $ticket  = md5($postObj->Ticket);
            $res     = $this->insert_scan($user, $ticket);
            if ($res) {
                $tmp = $this->add_integral($user_id);
                if ($tmp) {
                    $msg = "谢谢关注柳州和泰搬家，你的好友已获得积分!";
                }
            } else {
                $msg = "你已关注过该公众号！";
            }
            $this->responseText($postObj, $msg);
            break;
        case 'LOCATION':
            $msg = "谢谢关注柳州和泰搬家";
            $this->responseText($postObj, $msg);
            break;
        case 'scancode_waitmsg':
            $msg = "谢谢关注柳州和泰搬家";
            $this->responseText($postObj, $msg);
            break;
        default:
            $msg = "事件推送失败";
            $this->responseText($postObj, $msg);
            break;
        }

    }
    public function responseMsg(&$postObj) {
        $keyword = trim($postObj->Content);
        switch ($keyword) {
        case '时间':
            $msg = date('Y-m-d H:i:s', time());
            $this->responseText($postObj, $msg);
            break;
        case '电话':
            $msg = "搬家热线：13737264076";
            $this->responseText($postObj, $msg);
            break;
        case '手机号':
            $msg = "搬家热线：13737264076";
            $this->responseText($postObj, $msg);
            break;
        case '手机':
            $msg = "搬家热线：13737264076";
            $this->responseText($postObj, $msg);
            break;
        case '地址':
            $msg = "公司地址：柳州是北雀路67号56栋二单元2-1号";
            $this->responseText($postObj, $msg);
            break;
        case '地点':
            $msg = "公司地址：柳州是北雀路67号56栋二单元2-1号";
            $this->responseText($postObj, $msg);
            break;
        default:
            $msg = '暂不支持该消息回复';
            $this->responseText($postObj, $msg);
            break;
        }
    }
    public function responseText(&$postObj, $msg) {
        $fromUsername = $postObj->FromUserName;
        $toUsername   = $postObj->ToUserName;
        $time         = time();
        $textTpl      = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0</FuncFlag>
            </xml>";
        $msgType    = "text";
        $contentStr = $msg;
        $resultStr  = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
        echo $resultStr;
    }
    public function responseImage(&$postObj) {
        $toUsername   = $postObj->ToUserName;
        $fromUsername = $postObj->FromUserName;
        $time         = time();
        $textTpl      = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Image>
            <MediaId><![CDATA[%s]]></MediaId>
            </Image>
            </xml>";
        $msgType   = "image";
        $mediaId   = $postObj->MediaId;
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $mediaId);
        echo $resultStr;
    }
    public function responseVoice(&$postObj) {
        $toUsername   = $postObj->ToUserName;
        $fromUsername = $postObj->FromUserName;
        $time         = time();
        $textTpl      = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Image>
            <MediaId><![CDATA[%s]]></MediaId>
            </Image>
            </xml>";
        $msgType   = "image";
        $mediaId   = $postObj->MediaId;
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $mediaId);
        echo $resultStr;
    }
    protected function add_integral($user_id) {
        if (file_exists('static/html/integral.php')) {
            include_once INDEXPATH . '/static/html/integral.php';
            $this->model('wx')->add_integral($user_id, $integral);
            return true;
        } else {
            return false;
        }

    }
    protected function insert_scan($user, $ticket) {
        $data['scan_user']   = $user;
        $data['scan_ticket'] = $ticket;
        $data['scan_time']   = date('Y-m-d');
        $m                   = $this->model('wx');
        $res                 = $m->check_scan($data);
        if (empty($res)) {
            $m->insert_scan($data);
            return true;
        } else {
            return false;
        }
    }
    protected function user_scan($user, $ticket) {
        $data['scan_user']   = $user;
        $data['scan_ticket'] = $ticket;
        $data['scan_time']   = date('Y-m-d');
        $m                   = $this->model('wx');
        $checkResult         = $m->check_scan($data);
        if (empty($checkResult)) {
            $m->insert_scan($data);
            return true;
        } else {
            return false;
        }
    }
    public function responseView(&$postObj) {
        echo '';
    }
    public function responseVideo(&$postObj) {
        echo "";
    }
    public function responseShortVideo(&$postObj) {
        echo '';
    }
    public function responseLocation(&$postObj) {
        echo '';
    }

    public function wxlogin() {
        header("Cache-Control: no-cache, must-revalidate");
        $config = $this->load_class('WxConfig');
        $wx     = $this->load_class('Wxapi');
        $wx->setApp($config->appId, $config->appSecret);
        $openId = $wx->get_openid();
        $m      = $this->model('user');
        $user   = $m->user_by_openid($openId);
        if (empty($user)) {
            redirect(base_url('home/user/wx_reg'));
        }
        $this->update_user($user);
        $next = $this->input->get_post('url');
        if (!$next) {
            $next = base_url('home/user');
        }
        redirect($next);
    }
    public function wx_reg() {
        header("Cache-Control: no-cache, must-revalidate");
        $config = $this->load_class('WxConfig');
        $wx     = $this->load_class('Wxapi');
        $wx->setApp($config->appId, $config->appSecret);
        $userinfo   = $wx->get_userinfo();
        $view       = $this->default_view(['title' => '用户注册', 'FOOT' => 'user']);
        $view->data = $userinfo;
        return $view;
    }
}