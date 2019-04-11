<?php
namespace Admin\Controller;
use Think\Controller;
use Libs\Upload;

class AdController extends BaseController {
    public function index(){
		$adInfo1=M("Ad")->where(array('ad_type'=>1))->select();
		$adInfo2=M("Ad")->where(array('ad_type'=>2))->select();
		$this->assign("ad_picinfo",$adInfo1);
		$this->assign("ad_speedinfo",$adInfo2);
		$this->display();
	}
	public function saveAd(){
		if(!IS_POST){
			exit(json_encode(array("code"=>5,"msg"=>"非法操作")));
		}else{
			$modeList=I("post.modeList");
			foreach($modeList as $key=>$value){
				$adId=0;
				$data=array(
					"ad_img"=>$value['picPath'],
					"ad_link"=>$value['picLink'],
					"ad_type"=>$value['type']
				);
				$adId=$value['id'];
				if(empty($adId)){
					$data["ad_time"]=time();
					$adId=M("Ad")->add($data);
				}else{
					$adInfo=M("Ad")->where(array("ad_id"=>$adId))->find();
					if($adInfo['ad_img']!=$data['ad_img']){
						$documentRoot=$_SERVER['DOCUMENT_ROOT'];
						$filePath1=$documentRoot.'/Public/'.$adInfo['ad_img'];
						$filePath2=$documentRoot.'/Public/'.str_replace("mini_",'',$adInfo['ad_img']);
						if(file_exists($filePath1)){
							@unlink($filePath1);
							@unlink($filePath2);
						}
					}
					$res=M("Ad")->where(array("ad_id"=>$adId))->save($data);
				}
			}
			exit(json_encode(array("code"=>6,"msg"=>"保存数据成功")));
		}
	}
	public function delAd(){
		if(!IS_POST){
			exit(json_encode(array("code"=>5,"msg"=>"非法操作")));
		}else{
			$adId=I("post.adId");
			$res=M("Ad")->where(array("ad_id"=>$adId))->delete();
			if($res!==false){
				exit(json_encode(array("code"=>6,"msg"=>"删除成功")));
			}else{
				exit(json_encode(array("code"=>5,"msg"=>"删除失败，请稍后重试...")));
			}
		}
	}
	public function upload(){
		$type=I("get.type");
		$upload=new Upload();
		$picInfo=$upload->upload($_FILES['file'],"Ad",array('jpg',"png","jpeg","gif"),5);
		if($picInfo=="上传文件后缀不允许"){
			exit(json_encode(array("code"=>2,"msg"=>"请上传jpg或者jpeg图片")));
		}
		if(trim($picInfo)=="上传文件大小不符！"){
			exit(json_encode(array("code"=>3,"msg"=>"请上传文件大小不超过5M")));
		}
		if($type==1){
			$miniPic=$upload->create_miniimage($picInfo,1078,206);
		}
		if($type==2){
			$miniPic=$upload->create_miniimage($picInfo,304,538);
		}
		if(empty($miniPic)){
			exit(json_encode(array("code"=>4,"msg"=>"上传失败")));
		}else{
			exit(json_encode(array("code"=>0,"msg"=>"上传成功","picPath"=>$miniPic)));
		}
		
	}
}