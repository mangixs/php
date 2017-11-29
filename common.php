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