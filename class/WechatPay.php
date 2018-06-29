<?php

class Wxpay
{
    private $values = [];
    private $appId;
    private $mchId;
    private $mchKey;
    private $xml;
    private $mode;
    private $url = [
        'jsapi' => 'https://api.mch.weixin.qq.com/pay/unifiedorder',
    ];
    public function init($appId, $mchId, $mchKey)
    {
        $this->appId = $appId;
        $this->mchId = $mchId;
        $this->mchKey = $mchKey;
    }
    public function __set($key, $val)
    {
        if (!is_array($val)) {
            $this->values[$key] = $val;
        }
    }
    public function __get($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }
    public function jsapi()
    {
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
        $this->values['appid'] = $this->appId;
        $this->values['mch_id'] = $this->mchId;
        $this->values['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        $this->values['nonce_str'] = $this->randStr(32);
        $this->mode = 'jsapi';
        return ['result' => 'SUCCESS'];
    }
    public function sign($key = 'sign')
    {
        ksort($this->values);
        $str = $this->toUrlParem();
        $str .= "&key={$this->mchKey}";
        $this->values[$key] = strtoupper(MD5($str));
    }
    private function toUrlParem()
    {
        foreach ($this->values as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $tmp[] = "{$k}={$v}";
            }
        }
        return implode('&', $tmp);
    }
    public function toXML()
    {
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
    public function postXML($config, $useCert = false, $second = 30)
    {
        $url = $this->url[$this->mode];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        if ($config->CURL_PROXY_HOST != '0.0.0.0' and !empty($config->CURL_PROXY_HOST)) {
            curl_setopt($ch, CURLOPT_PROXY, $config->CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, $config->CURL_PROXY_HOST);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($useCert == true) {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $config->certPEM);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $config->keyPEM);
        }
        curl_setopt($ch, CURLOPT_POST, true);
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
        $this->values = [];
        $this->values['appId'] = $data['appid'];
        $this->values['timeStamp'] = time();
        $this->values['nonceStr'] = $this->randStr(32);
        $this->values['package'] = 'prepay_id=' . $data['prepay_id'];
        $this->values['signType'] = 'MD5';
        $this->sign('paySign');
        return ['result' => 'SUCCESS',
            'data' => $this->values,
        ];
    }
    protected function randStr($len = 10)
    {
        $str = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm');
        $last = count($str) - 1;
        $rand = '';
        for ($i = 0; $i < $len; $i++) {
            $rand .= $str[rand(0, $last)];
        }
        return $rand;
    }
    protected function XMLtoArray($xml)
    {
        libxml_disable_entity_loader(true);
        $ret = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $ret;
    }
}
class WxNotify
{
    private $values = [];
    private $return = [];
    public function init()
    {
        $this->getResult();
        return $this->values['return_code'];
    }
    private function getResult()
    {
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
    public function setCode($code = 'SUCCESS')
    {
        $this->return['return_code'] = $code;
    }
    public function setMsg($msg = 'OK')
    {
        $this->return['return_msg'] = $msg;
    }
    public function getValues($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }
    public function retToXml()
    {
        return $this->toXML($this->return);
    }
    private function toXML($data)
    {
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
}
