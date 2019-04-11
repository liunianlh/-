<?php
namespace Admin\Controller;
use Think\Controller;
class CountryController extends BaseController {
    public function index(){
		$countryInfo=M("Country")->select();
		$this->assign("country_info",$countryInfo);
		$this->display();
	}
	public function edit(){
		if(!IS_POST){
			$id=I("get.id");
			$where=array();
			$where["country_id"]=$id;
			$countryInfo=M("Country")->where($where)->find();;
			$this->assign("country_info",$countryInfo);
			$this->display();
		}else{
			$id=I("post.id");
			$chineseName=I("post.chineseName");
			$englishName=I("post.englishName");
			$chineseName=trim($chineseName);
			$englishName=trim($englishName);
			if(empty($chineseName)){
				exit(json_encode(array("code"=>10120,'msg'=>"中文名称不能为空")));
			}
			if(empty($englishName)){
				exit(json_encode(array("code"=>10121,'msg'=>"英文名称不能为空")));
			}
			$data=array(
				"country_name"=>$chineseName,
				"country_name2"=>$englishName
			);
			$res=M("Country")->where(array('country_id'=>$id))->save($data);
			if($res!==false){
				exit(json_encode(array("code"=>10125,'msg'=>"更新成功","url"=>U('Country/index'))));
			}else{
				exit(json_encode(array("code"=>10126,'msg'=>"更新失败,请稍后重试...")));
			}
		}
	}
}