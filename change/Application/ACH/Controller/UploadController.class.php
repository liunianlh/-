<?php
namespace ACH\Controller;
use Think\Controller;
use Libs\Upload;
class UploadController extends Controller {
    public function upload(){
		$upload=new Upload();
		$picInfo=$upload->upload($_FILES['file'],"invoiceAddon",array("pdf","PDF"),5);
		if($picInfo=="上传文件后缀不允许"){
			exit(json_encode(array("code"=>2,"msg"=>"请上传PDF文件")));
		}
		if(trim($picInfo)=="上传文件大小不符！"){
			exit(json_encode(array("code"=>3,"msg"=>"请上传文件大小不超过5M")));
		}
		$maxPic=$upload->create_maximage($picInfo);
		if($res!==false){
			exit(json_encode(array("code"=>0,"msg"=>"上传成功","picPath"=>$maxPic,"fileName"=>$_FILES['file']['name'])));
		}else{
			exit(json_encode(array("code"=>1,"msg"=>"上传失败")));
		}
	}
}