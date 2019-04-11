<?php
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller {
    public function index(){
		if(!IS_POST){
			$this->display();
		}else{
			$account=I("post.account");
			$pwd=I("post.pwd");
			$result=D("Admin")->checkLogin($account,$pwd);
			if(!empty($result)){
				session("adminid",$result['admin_id']);
				$this->success("登陆成功！",U("Index/index","",'',true));
			}else{
				$this->error("用户名或密码错误！");
			}
		}
	}

	// 登出
	public function layout(){
		session("adminid",null);
		$this->redirect('Login/index');
	}
}