<?php
namespace Home\Controller;
use Think\Controller;
class ResetController extends Controller {
    public function index(){
		if(!IS_POST){
			$rnd=I("get.rnd");
			$time=I("get.t");
			$verify=I("get.v");
			$code=base64_decode($rnd);
			
			$where=array();
			
			$where['code']=$code;
			$where['verify']=$verify;
			
			$info=M("Find_password")->where($where)->find();
			if(empty($info)){
				header("Content-type: text/html; charset=utf-8");
				exit("链接已失效");
			}
			if(($info['time']<strtotime("-7 day"))||($info['is_use']==2)){
				header("Content-type: text/html; charset=utf-8");
				exit("链接已失效");
			}
			session("findpwd",$info['user_id']);
			session("findid",$info['id']);
			$this->display();
		}else{
			$password=I("post.password");
			$password_sure=I("post.password_sure");
			if(trim($password)!=trim($password_sure)){
				exit(json_encode(array("code"=>"10811","msg"=>"两次密码不相等")));
			}
			if(strlen($password)<6||strlen($password)>12||preg_match("/^\d+$/",$password)||preg_match("/^[a-zA-Z]+$/",$password)){
				exit(json_encode(array("code"=>"10812","msg"=>"密码密码必须是6-12位数字和英文混合")));
			}
			$where=array();
			$where['user_id']=$_SESSION['findpwd'];
			
			$userInfo=M("User")->where($where)->find();
			
			$strKey=substr(md5($userInfo['user_verify']),6,6);
			$pwd=md5($strKey.$userInfo['user_uid'].$password);
			
			$data=array(
				"user_password"=>$pwd
			);
			$res=M("User")->where($where)->save($data);
			if($res!==false){
				$where2=array();
				$where2["id"]=$_SESSION['findid'];
				$data2=array(
					"is_use"=>2
				);
				M("Find_password")->where($where2)->save($data2);
				session("findpwd",null);
				session("findid",null);
				exit(json_encode(array("code"=>"10813","msg"=>"密码重置成功","url"=>U('Login/index'))));
			}else{
				exit(json_encode(array("code"=>"10814","msg"=>"密码重置失败，请稍后重试...")));
			}
		}
	}
}