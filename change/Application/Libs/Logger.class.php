<?php
namespace Libs;
class Logger{
	public static function error($msg){
		self::clear();
		$fileDir=dirname(dirname(__FILE__))."/Logs/";
		$fileName=date("Y-m-d",time()).".log";
		if(!is_dir($fileDir)){
			mkdir($fileDir);
		}
		$file=$fileDir.$fileName;
		$ip=get_client_ip();
		if(!file_exists($file)){
			$data=array();
			$data[$ip]=$msg;
			file_put_contents($file,json_encode($data));
		}else{
			$fread=fopen($file,"r");
			$jsonData=fread($fread,filesize($file));
			fclose($fread);
			$data=json_decode($jsonData,true);
			$data[$ip]=$msg;
			file_put_contents($file,json_encode($data));
		}
	}
	public function read(){
		$fileDir=dirname(dirname(__FILE__))."/Logs/";
		$fileName=date("Y-m-d",time()).".log";
		if(!is_dir($fileDir)||!file_exists($fileDir.$fileName)){
			return;
		}
		$fread=fopen($fileDir.$fileName,"r");
		$jsonData=fread($fread,filesize($fileDir.$fileName));
		fclose($fread);
		$data=json_decode($jsonData,true);
		return $data;
	}
	public static function clear(){
		$fileDir=dirname(dirname(__FILE__))."/Logs/";
		if(!is_dir($fileDir)){
			return;
		}
		$dir=dir($fileDir);
		while(false != ($item = $dir->read())) {
			if($item == '.' || $item == '..') {
				continue;
			}
			$fileArr=pathinfo($item);
			if(is_array($fileArr)&&isset($fileArr['filename'])){
				if(date("Y-m-d",time())!=$fileArr['filename']){
					@unlink($dir->path.'/'.$item);
				}
			}
		}
	}
	public static function loginError(){
		$counter=self::loginErrorNumber();
		self::error(++$counter);
		return $counter;
	}
	public static function loginErrorNumber(){
		$counter=0;
		$data=self::read();
		if(!empty($data)){
			$ip=get_client_ip();
			if(isset($data[$ip])){
				$counter=$data[$ip];
			}
		}
		return $counter;
	}
}