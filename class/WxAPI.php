<?php
namespace libraries\classes;

class Wxapi
{
    public function setApp($appId, $appSecret)
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
        $this->saveDir   = $_SERVER['DOCUMENT_ROOT'] . '/wx_json';
    }
    public function set_dir()
    {
        if (!file_exists($this->saveDir)) {
            mkdir($this->saveDir);
        }
    }
    public function __SET($name, $value)
    {
        $this->$name = $value;
    }
    public function get_openid()
    {
        $token = $this->get_info_oauth();
        return $token['openid'];
    }
    public function get_userinfo()
    {
        $token = $this->get_info_oauth('snsapi_userinfo');
        if ($token['scope'] != 'snsapi_userinfo') {
            return $token['openid'];
        }
        $url  = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $token['access_token'] . "&openid=" . $token['openid'] . "&lang=zh_CN";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($curl);
        curl_close($curl);
        return json_decode($res, true);
    }
    protected function get_info_oauth($scope = 'snsapi_base')
    {
        if (!isset($_GET['state']) or $_GET['state'] != 'STATE') {
            $redirect = urlencode('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
            $url      = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appId&redirect_uri=$redirect&response_type=code&scope=$scope&state=STATE#wechat_redirect";
            header("Location:$url");
            exit();
        }
        if (!isset($_GET['code']) or (isset($_GET['code']) and $_GET['code'] == 'authdeny')) {
            echo '未获得授权!';
            exit();
        }
        $code = $_GET['code'];
        $url  = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appId&secret=$this->appSecret&code=$code&grant_type=authorization_code";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($curl);
        curl_close($curl);
        $token = json_decode($res, true);
        if (isset($token['errcode'])) {
            echo '<div>获取微信信息错误,错误:' . $token['errcode'] . '原因:' . $token['errmsg'] . '<br /><a href="http://' . $_SERVER['SERVER_NAME'] . '" >回到首页</a></div>';
            exit();
        }
        return $token;
    }
    public function get_jsapi_signature()
    {
        $ticket    = $this->get_jsapi_ticket();
        $noncestr  = $this->randStr(24);
        $timestamp = time();
        $url       = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $string    = "jsapi_ticket=$ticket&noncestr=$noncestr&timestamp=$timestamp&url=$url";
        return array('appId' => $this->appId, 'noncestr' => $noncestr, 'timestamp' => $timestamp, 'signature' => sha1($string));
    }
    protected function get_jsapi_ticket()
    {
        $create_json = function () {
            $token = $this->get_token();
            $url   = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$token&type=jsapi";
            $curl  = curl_init();
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $res = curl_exec($curl);
            curl_close($curl);
            $json                 = json_decode($res, true);
            $json['expires_time'] = time() + $json['expires_in'] - 100;
            $fp                   = fopen($this->saveDir . '/jsapi_ticket.json', 'w+');
            fwrite($fp, json_encode($json));
            fclose($fp);
            return $json['ticket'];
        };
        if (file_exists($this->saveDir . '/jsapi_ticket.json')) {
            $ticket = file_get_contents($this->saveDir . '/jsapi_ticket.json');
            $ticket = json_decode($ticket);
            if (time() > $ticket->expires_time) {
                return $create_json();
            } else {
                return $ticket->ticket;
            }
        } else {
            return $create_json();
        }
    }
    public function get_media($id)
    {
        $token = $this->get_token();
        $url   = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$token&media_id=$id";
        $curl  = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_NOBODY, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $res  = curl_exec($curl);
        $http = curl_getinfo($curl);
        curl_close($curl);
        return array_merge(array('header' => $http), array('body' => $res));
    }
    protected function get_token()
    {
        $create_json = function () {
            $url  = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $res = curl_exec($curl);
            curl_close($curl);
            $json                 = json_decode($res, true);
            $json['expires_time'] = time() + $json['expires_in'] - 100;
            $fp                   = fopen($this->saveDir . '/token.json', 'w+');
            fwrite($fp, json_encode($json));
            fclose($fp);
            return $json['access_token'];
        };
        if (file_exists($this->saveDir . '/token.json')) {
            $data  = file_get_contents($this->saveDir . '/token.json');
            $token = json_decode($data);
            if (time() > $token->expires_time) {
                return $create_json();
            } else {
                return $token->access_token;
            }
        } else {
            return $create_json();
        }
    }
    public function get_info_unionID($openid)
    {
        $access_token = $this->get_token();
        $url          = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
        $curl         = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($res, true);
        if ($data['subscribe'] == 0) {
            return array();
        }
        return $data;
    }
    public function get_qrcode($scene, $action = 'QR_SCENE', $second = 2592000, $dataType = 'img')
    {
        $access_token       = $this->get_token();
        $url                = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
        $tmp                = array();
        $tmp['action_name'] = $action;
        if ($tmp['action_name'] == 'QR_SCENE' and $second > 0) {
            $tmp['expire_seconds'] = $second;
        }
        $tmp['action_info']          = array();
        $tmp['action_info']['scene'] = array();
        if ($tmp['action_name'] == 'QR_SCENE' && is_numeric($scene)) {
            $tmp['action_info']['scene']['scene_id'] = $scene;
        }
        if ($tmp['action_name'] == 'QR_LIMIT_SCENE' && is_string($scene)) {
            $tmp['action_info']['scene']['scene_str'] = $scene;
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($tmp));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($res);
        if ($dataType == 'url') {
            return $data->ticket;
        }
        $ticket = urlencode($data->ticket);
        $url    = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket={$ticket}";
        $curl   = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, false);
        $res  = curl_exec($curl);
        $http = curl_getinfo($curl);
        curl_close($curl);
        $ret = array_merge(array('header' => $http), array('body' => $res));
        return $ret;

    }
    public function is_wei()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }
    protected function randStr($len = 10)
    {
        $str  = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm');
        $last = count($str) - 1;
        $rand = '';
        for ($i = 0; $i < $len; $i++) {
            $rand .= $str[rand(0, $last)];
        }
        return $rand;
    }
    public function create_menu($data)
    {
        $access_token = $this->get_token();
        $url          = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
        $curl         = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res    = curl_exec($curl);
        $result = json_decode($res, true);
        curl_close($curl);
        return $result;
    }
    public function get_menu()
    {
        $access_token = $this->get_token();
        $url          = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token={$access_token}";
        $curl         = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($res, true);
        return $data;
    }
    public function del_menu()
    {
        $access_token = $this->get_token();
        $url          = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$access_token}";
        $curl         = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($res, true);
        return $data;
    }
    public function get_all_userinfo()
    {
        $access_token = $this->get_token();
        $url          = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$access_token}";
        $curl         = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($res, true);
        return $data;
    }
}
