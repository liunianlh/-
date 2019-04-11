<?php
namespace Libs;
class Upload{
	/*
	*   文件上传
	*
	*	$file   文件数组
	*   $subdir 子目录名字
	*/
	public static function upload($file,$subdir="xiyuan",$exts=array('jpg', 'gif', 'png', 'jpeg'),$maxSize=5){
		$upload = new \Think\Upload();
		$upload->maxSize=$maxSize*1024*1024;
		$upload->exts= $exts;
		$upload->savePath ='Uploads/'.$subdir."/";
		$upload->autoSub=true;
		$upload->subName=array("date","Ymd");
		$upload->rootPath="./Public/";
		$info=$upload->uploadOne($file);
		if(!$info) {
			return $upload->getError();
		}else{
			return $info;
		}
	}
	//生成大图
	public function create_maximage($info){
		return $info['savepath'].$info['savename'];
	}
	
	//生成小图
	public function create_miniimage($info,$thumbWidth="200",$thumbHeight="200"){
		$image = new \Think\Image();
		$thumb_file="./Public/".$info['savepath'].$info['savename'];
		$save_path ="./Public/".$info['savepath'].'mini_'.$info['savename'];
		
		$image->open( $thumb_file )->thumb( $thumbWidth, $thumbHeight )->save( $save_path );
		
		return $info['savepath'].'mini_'.$info['savename'];
	}
}