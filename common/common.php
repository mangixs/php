<?php
function p($str){
	if ( is_bool($str) ) {
		var_export($str);
		exit;
	}else{
		echo '<pre>';
		print_r($str);
		echo '</pre>';
		exit;
	}
}
function uuid(){
	$str = md5(uniqid(mt_rand(), true));   
    return $str;    	
}
function curlPostData($url,$post){
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($post));
    $res=curl_exec($ch);
    curl_close($ch);
    return $res;
}
function curlGetData($url){
	$curl=curl_init();
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
	curl_setopt($curl, CURLOPT_HEADER, FALSE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_NOBODY, FALSE);	
	$res=curl_exec($curl);
	curl_close($curl);	
	return $res;
}
function is_wei(){
	if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
		return true;
	}  
    return false;		
}
function randStr($len=10){
	$str=array('1','2','3','4','5','6','7','8','9','0','q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m');
	$last=count($str)-1;
	$rand='';
	for( $i=0;$i<$len;$i++ ){
		$rand.=$str[ rand(0,$last) ];
	}
	return $rand;
}
function toXML($data){
	if( !is_array( $data ) or count( $data )<=0 ){
		return null;
	}		
	$xml='<xml>';
	foreach( $data as $k=>$v ){
		if( is_numeric($v) ){
			$xml.='<'.$k.'>'.$v.'</'.$k.'>';
		}
		else{
			$xml.='<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
		}
	}
	$xml.='</xml>';
	return $xml;		
}
function XMLtoArray($xml){
	libxml_disable_entity_loader(true);
	$ret=json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)),true);
	return $ret;
}
/**
*获取客户端ip地址  http://rainmeter.cn/cms/
*/
function ip() {
    //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $res =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    return $res;
}
/** 删除目录及目录下所有文件或删除指定文件
 * @param str $path   待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
 * @return bool 返回删除状态 
 */
function delDirAndFile($path, $delDir = FALSE) {
	if ( !file_exists($path) ) {
		return;
	}
    $handle = opendir($path);
    if ($handle) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != ".."){
                is_dir("$path/$item") ? $this->delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
            }
        }
        closedir($handle);
        if ($delDir){
            return rmdir($path);
        }
    }else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
}
/**
 * [get_current_url 获取当前访问的完整url地址 ]
 * @return [type] [description]
 */
function get_current_url(){ 
    $current_url='http://'; 
    if(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on'){ 
        $current_url='https://'; 
    } 
    if($_SERVER['SERVER_PORT']!='80'){ 
        $current_url.=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI']; 
    }else{ 
        $current_url.=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; 
    } 
    return $current_url; 
}
/**
 * 页面地址跳转
 * @param type $url 目标地址
 * @param type $name 倒计时
 * @return type
 */
function redirect($url, $time = 0) {
    if (!headers_sent()) {
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            header("refresh:{$time};url={$url}");
        }
        exit();
    } else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        exit($str);
    }
}

/**
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function remove_xss($string) {
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);
    $parm1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $parm = array_merge($parm1, $parm2);

    for ($i = 0; $i < sizeof($parm); $i++) {
        $pattern = '/';
        for ($j = 0; $j < strlen($parm[$i]); $j++) {
            if ($j > 0) {
                $pattern .= '(';
                $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                $pattern .= '|(&#0([9][10][13]);?)?';
                $pattern .= ')?';
            }
            $pattern .= $parm[$i][$j];
        }
        $pattern .= '/i';
        $string = preg_replace($pattern, '', $string);
    }
    return $string;
}
/**
 * 判断是否SSL协议
 * @return boolean
 */
function is_ssl() {
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    }
    return false;
}
/**
 * 随机字符串
 * @param int $length 长度
 * @param int $numeric 类型(0：混合；1：纯数字)
 * @return string
 */
function random($length, $numeric = 0) {
     $seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
     $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
     if($numeric) {
          $hash = '';
     } else {
          $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
          $length--;
     }
     $max = strlen($seed) - 1;
     for($i = 0; $i < $length; $i++) {
          $hash .= $seed{mt_rand(0, $max)};
     }
     return $hash;
}
/**
 * 格式化金额
 * @param type $money
 * @return type
 */
function money($money, $str = ',') {
    return number_format($money, 2, '.', $str);
}
/**
 * 数组转XML
 * @param array $arr
 * @param boolean $htmlon
 * @param boolean $isnormal
 * @param intval $level
 * @return type
 */
function array2xml($arr, $htmlon = TRUE, $isnormal = FALSE, $level = 1) {
    $s = $level == 1 ? "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n<root>\r\n" : '';
    $space = str_repeat("\t", $level);
    foreach($arr as $k => $v) {
        if(!is_array($v)) {
            $s .= $space."<item id=\"$k\">".($htmlon ? '<![CDATA[' : '').$v.($htmlon ? ']]>' : '')."</item>\r\n";
        } else {
            $s .= $space."<item id=\"$k\">\r\n".array2xml($v, $htmlon, $isnormal, $level + 1).$space."</item>\r\n";
        }
    }
    $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
    return $level == 1 ? $s."</root>" : $s;
}

/**
 * 电子邮箱格式判断
 * @param  string $email 字符串
 * @return boolean
 */
function is_email($email) {
     if (!empty($email)) {
          return preg_match('/^[a-z0-9]+([\+_\-\.]?[a-z0-9]+)*@([a-z0-9]+[\-]?[a-z0-9]+\.)+[a-z]{2,6}$/i', $email);
     }
     return FALSE;
}

/**
 * 手机号码格式判断
 * @param string $string
 * @return boolean
 */
function is_mobile($string){
     if (!empty($string)) {
          return preg_match('/^1[3|4|5|7|8][0-9]\d{8}$/', $string);
     }
     return FALSE;
}

/**
 * 邮政编码格式判断
 * @param string $string
 * @return boolean
 */
function is_zipcode($string){
     if (!empty($string)) {
          return preg_match('/^[0-9][0-9]{5}$/', $string);
     }
     return FALSE;
}

/**
 * 缩略图生成
 * @param sting $src
 * @param intval $width
 * @param intval $height
 * @param boolean $replace
 * @return string
 */
function thumb($src = '', $width = 500, $height = 500, $replace = false) {
    if(is_file($src) && file_exists($src)) {
        $ext = pathinfo($src, PATHINFO_EXTENSION);
        $name = basename($src, '.'.$ext);
        $dir = dirname($src);
        $setting = model('admin/setting','service')->get();
        if(in_array($ext, array('gif','jpg','jpeg','bmp','png'))) {
            $name = $name.'_thumb_'.$width.'_'.$height.'.'.$ext;
            $file = $dir.'/'.$name;
            if(!file_exists($file) || $replace == TRUE) {
                $image = new image($src);
                $image->thumb($width, $height, isset($setting['attach_thumb'])?$setting['attach_thumb']:1);
                $image->save($file);
            }
            return $file;
        }
    }
    return $src;
}

/**
 * 多维数组合并（支持多数组）
 * @return array
 */
function array_merge_multi () {
    $args = func_get_args();
    $array = array();
    foreach ( $args as $arg ) {
        if ( is_array($arg) ) {
            foreach ( $arg as $k => $v ) {
                if ( is_array($v) ) {
                    $array[$k] = isset($array[$k]) ? $array[$k] : array();
                    $array[$k] = array_merge_multi($array[$k], $v);
                } else {
                    $array[$k] = $v;
                }
            }
        }
    }
    return $array;
}
/**
 * 生成订单号
 */
function build_order_no($suffix = 'o') {
    return $suffix.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/**
 * 对多位数组进行排序
 * @param $multi_array 数组
 * @param $sort_key需要传入的键名
 * @param $sort排序类型
 */
function multi_array_sort($multi_array, $sort_key, $sort = SORT_DESC) {
    if (is_array($multi_array)) {
        foreach ($multi_array as $row_array) {
            if (is_array($row_array)) {
                $key_array[] = $row_array[$sort_key];
            } else {
                return FALSE;
            }
        }
    } else {
        return FALSE;
    }
        array_multisort($key_array, $sort, $multi_array);
        return $multi_array;
}
/**
 * 加密字符串
 * @param string $str 字符串
 * @param string $key 加密key
 * @return string
 */
function encrypt($data,$key = '',$expire = 0) {
    $expire = sprintf('%010d', $expire ? $expire + time():0);
    $key = md5($key != '' ? $key : config('authkey'));
    $data = base64_encode($expire.$data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = $str    =   '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 解密字符串
 * @param string $str 字符串
 * @param string $key 加密key
 * @return string
 */
function decrypt($data,$key = '') {
    $key = md5($key != '' ? $key : config('authkey'));
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
       $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);

    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    $data   = base64_decode($str);
    $expire = substr($data,0,10);
    if($expire > 0 && $expire < time()) {
        return '';
    }
    $data   = substr($data,10);
    return $data;
}
/**
 * 生成目录
 * @param  string  $path 目录
 * @param  integer $mode 权限
 * @return boolean
 */
function create($path, $mode = 0777) {
    if(is_dir($path)) return TRUE;
    $path = str_replace("\\", "/", $path);
    if(substr($path, -1) != '/') $path = $path.'/';
    $temp = explode('/', $path);
    $cur_dir = '';
    $max = count($temp) - 1;
    for($i=0; $i<$max; $i++) {
        $cur_dir .= $temp[$i].'/';
        if (@is_dir($cur_dir)) continue;
        @mkdir($cur_dir, 0777,true);
        @chmod($cur_dir, 0777);
    }
    return is_dir($path);
}
/**
  +----------------------------------------------------------
 * 取得目录下面的文件信息
  +----------------------------------------------------------
 * @access public
  +----------------------------------------------------------
 * @param mixed $pathname 路径
  +----------------------------------------------------------
 */
function listFile($pathname, $pattern = '*') {
    static $_listDirs = array();
    $guid = md5($pathname . $pattern);
    if (!isset($_listDirs[$guid])) {
        $dir = array();
        $list = glob($pathname . $pattern);
        foreach ($list as $i => $file) {
            //$dir[$i]['filename']    = basename($file);
            //basename取中文名出问题.改用此方法
            //编码转换.把中文的调整一下.
            $dir[$i]['filename'] = preg_replace('/^.+[\\\\\\/]/', '', $file);
            $dir[$i]['pathname'] = realpath($file);
            $dir[$i]['owner'] = fileowner($file);
            $dir[$i]['perms'] = fileperms($file);
            $dir[$i]['inode'] = fileinode($file);
            $dir[$i]['group'] = filegroup($file);
            $dir[$i]['path'] = dirname($file);
            $dir[$i]['atime'] = fileatime($file);
            $dir[$i]['ctime'] = filectime($file);
            $dir[$i]['size'] = filesize($file);
            $dir[$i]['type'] = filetype($file);
            $dir[$i]['ext'] = is_file($file) ? strtolower(substr(strrchr(basename($file), '.'), 1)) : '';
            $dir[$i]['mtime'] = filemtime($file);
            $dir[$i]['isDir'] = is_dir($file);
            $dir[$i]['isFile'] = is_file($file);
            $dir[$i]['isLink'] = is_link($file);
            //$dir[$i]['isExecutable']= function_exists('is_executable')?is_executable($file):'';
            $dir[$i]['isReadable'] = is_readable($file);
            $dir[$i]['isWritable'] = is_writable($file);
        }
        $cmp_func = create_function('$a,$b', '
        $k  =  "isDir";
        if($a[$k]  ==  $b[$k])  return  0;
        return  $a[$k]>$b[$k]?-1:1;
        ');
        // 对结果排序 保证目录在前面
        usort($dir, $cmp_func);
        $this->_values = $dir;
        $_listDirs[$guid] = $dir;
    } else {
        $this->_values = $_listDirs[$guid];
    }
}
/**
 * 加密方法
 * @param string $str
 * @return string
 */
 function encrypt($str,$screct_key){
	//AES, 128 模式加密数据 CBC
	$screct_key = base64_decode($screct_key);
	$str = trim($str);
	$str = addPKCS7Padding($str);
	$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
	$encrypt_str =  mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
	return base64_encode($encrypt_str);
}

/**
 * 解密方法
 * @param string $str
 * @return string
 */
 function decrypt($str,$screct_key){
	//AES, 128 模式加密数据 CBC
	$str = base64_decode($str);
	$screct_key = base64_decode($screct_key);
	$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
	$encrypt_str =  mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
	$encrypt_str = trim($encrypt_str);

	$encrypt_str = stripPKSC7Padding($encrypt_str);
	return $encrypt_str;

}

/**
 * 填充算法
 * @param string $source
 * @return string
 */
function addPKCS7Padding($source){
	$source = trim($source);
	$block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

	$pad = $block - (strlen($source) % $block);
	if ($pad <= $block) {
		$char = chr($pad);
		$source .= str_repeat($char, $pad);
	}
	return $source;
}
/**
 * 移去填充算法
 * @param string $source
 * @return string
 */
function stripPKSC7Padding($source){
	$source = trim($source);
	$char = substr($source, -1);
	$num = ord($char);
	if($num==62)return $source;
	$source = substr($source,0,-$num);
	return $source;
}
/**
 * 删除目录及目录下所有文件或删除指定文件
 * @param str $path   待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
 * @return bool 返回删除状态
 */
function delDirAndFile($path, $delDir = FALSE) {
    if ( !file_exists($path) ) {
        return;
    }
    $handle = opendir($path);
    if ($handle) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != ".."){
                is_dir("$path/$item") ? $this->delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
            }
        }
        closedir($handle);
        if ($delDir){
            return rmdir($path);
        }
    }else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
}
/**
*获取客户端ip地址  http://rainmeter.cn/cms/
*/
function ip() {
    //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $res =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    return $res;
}
/**
 * 将字符串参数变为数组
 * @param $query
 * @return array 
 * 
 */
function convertUrlQuery($query){
    $queryParts = explode('&', $query);
    $params = array();
    foreach ($queryParts as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}
/**
 * 将参数变为字符串
 * @param $array_query
 * @return string string 'm=content&c=index&a=lists&catid=6&area=0&author=0&h=0®ion=0&s=1&page=1'
 */
function getUrlQuery($array_query){
    $tmp = array();
    foreach($array_query as $k=>$param){
        $tmp[] = $k.'='.$param;
    }
    $params = implode('&',$tmp);
    return $params;
}