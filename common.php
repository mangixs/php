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
