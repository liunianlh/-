<?php
namespace Admin\Controller;
class OtherController extends BaseController {
    public function index(){
		if(!IS_POST){
			$where=array(
				"config_name"=>"warn_set",
			);
			$configInfo=M("Config")->where($where)->find();
			$this->assign("config_info",$configInfo);
			$this->display();
		}else{
			$content=I("post.content",'','');
			$where=array(
				"config_name"=>"warn_set",
			);
			$data=array(
				"config_value"=>$content,
			);
			$config=M("Config");
			$configInfo=$config->where($where)->find();
			if(!empty($configInfo)){
				$config->where($where)->save($data);
				exit(json_encode(array("code"=>50001,"msg"=>"保存成功")));
			}else{
				$data['config_name']="warn_set";
				$config->add($data);
				exit(json_encode(array("code"=>50001,"msg"=>"保存成功")));
			}
		}
	}
}