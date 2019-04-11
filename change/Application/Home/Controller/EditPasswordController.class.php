<?php
namespace Home\Controller;
class EditPasswordController extends BaseController {
    public function index(){
		if(!IS_POST){
			$this->display();
		}else{
			$pwd1=I("post.pwd1");
			$pwd2=I("post.pwd2");
			$pwd3=I("post.pwd3");
			if(strlen($pwd2)<6||strlen($pwd2)>12||preg_match("/^\d+$/",$pwd2)||preg_match("/^[a-zA-Z]+$/",$pwd2)){
				exit(json_encode(array("code"=>10040,"info"=>"密码密码必须是6-12位数字和英文混合")));
			}
			if($pwd2!=$pwd3){
				exit(json_encode(array("code"=>10041,"info"=>"两次密码不一致")));
			}
			if(!(D("Admin/User")->checkUserPwd($_SESSION['userid']))){
				exit(json_encode(array("code"=>10042,"info"=>"原有密码输入错误")));
			}
			if(D("Admin/User")->checkNewPwdAndOldPwd($_SESSION['userid'],$pwd2)){
				exit(json_encode(array("code"=>10043,"info"=>"新密码不能与旧密码相同")));
			}
			if(D("Admin/User")->setUserPwd($_SESSION['userid'],$pwd2)){
				exit(json_encode(array("code"=>10044,"info"=>"密码修改成功")));
			}else{
				exit(json_encode(array("code"=>10045,"info"=>"密码修改失败，请稍后重试")));
			}
		}
	}
}