<?php
namespace libraries\classes;
class Wxpay {
    private $values = [];
    private $appId;
    private $mchId;
    private $mchKey;
    private $xml;
    private $mode;
    private $url = [
        'jsapi' => 'https://api.mch.weixin.qq.com/pay/unifiedorder',
    ];
    public function init($appId, $mchId, $mchKey) {
        $this->appId  = $appId;
        $this->mchId  = $mchId;
        $this->mchKey = $mchKey;
    }
    public function __set($key, $val) {
        if (!is_array($val)) {
            $this->values[$key] = $val;
        }
    }
    public function __get($key) {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }
    public function jsapi() {
        if (empty($this->values['out_trade_no'])) {
            return ['result' => 'ERROR', 'msg' => '缺少参数out_trade_no'];
        }
        if (empty($this->values['body'])) {
            return ['result' => 'ERROR', 'msg' => '缺少参数body'];
        }
        if (empty($this->values['total_fee'])) {
            return ['result' => 'ERROR', 'msg' => '缺少参数total_fee'];
        }
        if (empty($this->values['trade_type'])) {
            return ['result' => 'ERROR', 'msg' => '缺少参数trade_type'];
        }
        if ($this->values['trade_type'] == 'JSAPI' and empty($this->values['openid'])) {
            return ['result' => 'ERROR', 'msg' => '在trade_type为JSAPI，缺少参数out_trade_no'];
        }
        if ($this->values['trade_type'] == 'NATIVE' and empty($this->values['product_id'])) {
            return ['result' => 'ERROR', 'msg' => '在trade_type为NATIVE，缺少参数out_trade_no'];
        }
        $this->values['appid']            = $this->appId;
        $this->values['mch_id']           = $this->mchId;
        $this->values['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        $this->values['nonce_str']        = $this->randStr(32);
        $this->mode                       = 'jsapi';
        return ['result' => 'SUCCESS'];
    }
    public function sign($key = 'sign') {
        ksort($this->values);
        $str = $this->toUrlParem();
        $str .= "&key={$this->mchKey}";
        $this->values[$key] = strtoupper(MD5($str));
    }
    private function toUrlParem() {
        foreach ($this->values as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $tmp[] = "{$k}={$v}";
            }
        }
        return implode('&', $tmp);
    }
    public function toXML() {
        if (!is_array($this->values) or count($this->values) <= 0) {
            return ['result' => 'ERROR', 'msg' => '数据异常!'];
        }
        $xml = '<xml>';
        foreach ($this->values as $k => $v) {
            if (is_numeric($v)) {
                $xml .= '<' . $k . '>' . $v . '</' . $k . '>';
            } else {
                $xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
            }
        }
        $xml .= '</xml>';
        $this->xml = $xml;
        return ['result' => 'SUCCESS'];
    }
    public function postXML($config, $useCert = false, $second = 30) {
        $url = $this->url[$this->mode];
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        if ($config->CURL_PROXY_HOST != '0.0.0.0' and !empty($config->CURL_PROXY_HOST)) {
            curl_setopt($ch, CURLOPT_PROXY, $config->CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, $config->CURL_PROXY_HOST);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if ($useCert == true) {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $config->certPEM);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $config->keyPEM);
        }
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xml);
        $res = curl_exec($ch);
        if ($res) {
            ;
            curl_close($ch);
            $data = $this->XMLtoArray($res);
            if ($data['return_code'] === 'FAIL') {
                return ['result' => 'ERROR', 'msg' => '签名信息错误！', 'return' => $data];
            }
        } else {
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);
            $ret = ['result' => 'error', 'msg' => "curl出现错误{$errno},{$error}"];
        }
        $this->values              = [];
        $this->values['appId']     = $data['appid'];
        $this->values['timeStamp'] = time();
        $this->values['nonceStr']  = $this->randStr(32);
        $this->values['package']   = 'prepay_id=' . $data['prepay_id'];
        $this->values['signType']  = 'MD5';
        $this->sign('paySign');
        return ['result' => 'SUCCESS',
            'data'           => $this->values,
        ];
    }
    protected function randStr($len = 10) {
        $str  = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm');
        $last = count($str) - 1;
        $rand = '';
        for ($i = 0; $i < $len; $i++) {
            $rand .= $str[rand(0, $last)];
        }
        return $rand;
    }
    protected function XMLtoArray($xml) {
        libxml_disable_entity_loader(true);
        $ret = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $ret;
    }
}
class WxNotify {
    private $values = [];
    private $return = [];
    public function init() {
        $this->getResult();
        return $this->values['return_code'];
    }
    private function getResult() {
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
    public function setCode($code = 'SUCCESS') {
        $this->return['return_code'] = $code;
    }
    public function setMsg($msg = 'OK') {
        $this->return['return_msg'] = $msg;
    }
    public function getValues($key) {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }
    public function retToXml() {
        return $this->toXML($this->return);
    }
    private function toXML($data) {
        if (!is_array($data) or count($data) <= 0) {
            return null;
        }
        $xml = '<xml>';
        foreach ($data as $k => $v) {
            if (is_numeric($v)) {
                $xml .= '<' . $k . '>' . $v . '</' . $k . '>';
            } else {
                $xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
            }
        }
        $xml .= '</xml>';
        return $xml;
    }
    private function WxJsApiPay($key, $openid, $fee) {
        $ct     = time();
        $config = $this->load_class('Wxconfig');
        $pay    = $this->load_class('Wxpay');
        $pay->init($config->appId, $config->mchid, $config->mchkey);
        $pay->body         = '鸡皇-结算支付';
        $pay->attach       = '结算支付';
        $pay->out_trade_no = $key;
        $pay->total_fee    = $fee;
        $pay->time_start   = date('YmdHis', $ct);
        $pay->time_expire  = date('YmdHis', $ct + 600);
        $pay->goods_tag    = '鸡皇结算';
        $pay->notify_url   = $config->notify_url;
        $pay->trade_type   = 'JSAPI';
        $pay->openid       = $openid;
        $checkRet          = $pay->jsapi();
        if ($checkRet['result'] !== 'SUCCESS') {
            return $this->json($checkRet);
        }
        $pay->sign();
        $ret = $pay->toXML();
        if ($ret['result'] !== 'SUCCESS') {
            return $this->json($ret);
        }
        $ret = $pay->postXML($config, false, 6);
        return $this->json($ret);
    }
  		//   wx.chooseWXPay({
		//     timestamp: set.timeStamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
		//     nonceStr: set.nonceStr, // 支付签名随机串，不长于 32 位
		//     package: set.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
		//     signType: set.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
		//     paySign: set.paySign, // 支付签名
		//     success: function (res) {
		//     	this.submiting=true;
		//         $.suc('付款成功',function(){
		// 			window.location=__base_url+'home/home/order';
		// 		})
		//     }
		// });
		// <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script> 
		/* $jsapi_signature=$wx->get_jsapi_signature();
		<script type="text/javascript">
		wx.config({
			debug:false,
			appId:'<?=$signature['appId']?>',
			timestamp:'<?=$signature['timestamp']?>',
			nonceStr:'<?=$signature['noncestr']?>',
			signature:'<?=$signature['signature']?>',
			jsApiList:['chooseWXPay'],
		})
		wx.error(function(res){
			console.log(res);
		})
    
        <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
        <?php $this->end(); ?>
        <script type="text/javascript">
        var curUploadImg=null;
        var imgid=[];
        var submiting=false;
        wx.config({debug:false,appId:'<?=$sign['appId']?>',timestamp:'<?=$sign['timestamp']?>',nonceStr:'<?=$sign['noncestr']?>',signature:'<?=$sign['signature']?>',jsApiList:['chooseImage','previewImage','uploadImage']});
        $(function(){
            var txt=$(".ap_text_input");
            var data=txt.val();
            txt.focus(function(){
                if(data="请填写评价，1-300字之间"){
                    txt.val('');
                }
            })
        })
        function choose(){
            if(submiting==true){
                return;
            }
            wx.chooseImage({
                count:4,
                sizeType: ['compressed'], 
                sourceType:['album', 'camera'],
                success:function(res){
                    var localIds=res.localIds;
                    var html="";
                    $.each(localIds,function(k,v){
                        html +='<img src="'+v+'" width="100px" height="80px">';
                    })
                    $(".uploadImg").html(html);
                    curUploadImg= localIds;
                    $.each(curUploadImg,function(){
                        wx.uploadImage({
                        localId:this.toString(),
                        success:function(res){
                            var serverId=res.serverId;
                            imgid.push(serverId);
                            },
                        })
                    })
                    submiting=true;
                },
                fail:function(res){
                    submiting=false;
                }
            });
        }

        */
        public function appraise($id){
        $config=$this->load_class('WxConfig');
        $wx=$this->load_class('Wxapi');
        $wx->setApp( $config->appId,$config->appSecret );
        $sign=$wx->get_jsapi_signature();
        $m=$this->model('order');
        $data=$m->appraise($id);
        $view=$this->default_view(['title'=>'订单评价','FOOT'=>'user']);
        $view->RB='<a href="javascript:;" data-ajax="false" onclick="saveUpload()" class="save_head">保存</a>';
        $view->data=$data;
        $view->sign=$sign;
        return $view;
    }
    public function appraise_submit(){
        $file=array();
        $imgid=$this->input->post('img_id');
        $saleid=$this->input->post('sale_id');
        $orderid=$this->input->post('order_id');
        $config=$this->load_class('WxConfig');
        $wx=$this->load_class('Wxapi');
        $wx->setApp( $config->appId,$config->appSecret );
        $json=json_decode($imgid,true);
        if( count($json) > 0){
            foreach ($json as $v) {
                $img=$wx->get_media($v);
                preg_match('/(\w+)\/(\w+)/i', $img['header']['content_type'],$match);
                if( $match[1]=='image'){
                    $write=$this->load_class('write_data');
                    $ext=$match[2];
                    $ct=mt_rand(0,9999).time();
                    $fileName="{$saleid}-{$ct}";
                    $files="{$fileName}.{$ext}";
                    $savePath='/resources/'.'appraise_image/'.$files;
                    $path=$write->write_file('resources/'.'appraise_image',$fileName,$img['body'],$ext);
                    array_push($file,$savePath);
                }else{
                    return $this->json(['result'=>'ERROR','msg'=>'上传图片失败！']);
                }
            }
            $data['ap_img']=json_encode($file);
        }else{
            $data['ap_img']=1;
        }
        $m=$this->model('order');
        $data['sale_id']=$saleid;
        $data['ap_text']=$this->input->post('text');
        $data['insert_time']=time();
        $data['user_id']=$this->user->id;
        $m->insert_ap($data);
        $m->update_ap($orderid,$saleid);
        $next=base_url('user/order/wait_appraise');
        return $this->json(['result'=>'SUCCESS','msg'=>'保存成功！','next'=>$next ]);
    }
}
