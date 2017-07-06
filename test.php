<?php
class write_data{
	public function __construct(){
	}
	
	public function php_data($file,$name,$data){
		$file=$this->create_path($file);
		$fp=fopen($file.'/'.$name.'.php','w+');
		$content="<?php \n\r $".$name.'='.var_export($data,true)." \n\r ?>";
		fwrite($fp,$content);
		fclose($fp);
	}
	
	public function json_data($file,$name,$data,$unie=true){
		$file=$this->create_path($file);
		$fp=fopen($file.'/'.$name.'.json','w+');
		if ( $unie ) {
			$content=json_encode($data);
		}else{
			$content=json_encode($data,JSON_UNESCAPED_UNICODE);
		}
		fwrite($fp,$content);
		fclose($fp);
	}
	public function js_var($file,$name,$data){
		$file=$this->create_path($file);
		$fp=fopen($file.'/'.$name.'.js','w+');
		$content='var '.$name.'='.json_encode($data);
		fwrite($fp,$content);
		fclose($fp);
	}
	
	public function html_data($file,$name,$data){
		$file=$this->create_path($file);
		$fp=fopen($file.'/'.$name.'.php','w+');
		fwrite($fp,$data);
		fclose($fp);
	}
	public function html_page($file,$name,$data){
		$file=$this->create_path($file);
		$fp=fopen($file.'/'.$name.'.html','w+');
		fwrite($fp,$data);
		fclose($fp);
	}
	public function write_file($file,$name,$data,$ext=null){
		$file=$this->create_path($file);
		$dir=$file.'/'.$name;
		if( $ext ){
			$dir.=('.'.$ext);
		}
		$fp=fopen($dir,'w+');
		fwrite($fp,$data);
		fclose($fp);
		return '/'.$dir;
	}
	public function recode_file($file,$name,$data,$ext=null){
		$file=$this->create_path($file);
		$dir=$file.'/'.$name;
		if( $ext ){
			$dir.=('.'.$ext);
		}
		$fp=fopen($dir,'a+');
		fwrite($fp,$data);
		fclose($fp);
		return '/'.$dir;
	}
	
	private function create_path($file){
		$paths=explode('/',$file);
		$allPath=INDEXPATH.'/';
		foreach($paths as $path){
			$allPath.=$path;
			if( !file_exists($allPath) ){
				mkdir( $allPath);
			}
			$allPath.='/';
		}
		return $allPath;
	}
}
?>