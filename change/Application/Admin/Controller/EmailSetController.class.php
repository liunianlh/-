<?php
namespace Admin\Controller;
use Think\Controller;
use Libs\Email;
class EmailSetController extends BaseController {
	public function index(){
		if(!IS_POST){
			$emailInfo=M("Email_setting")
						->order("email_setting_id desc")
						->limit(1)
						->find();
			$this->assign("email_info",$emailInfo);
			$this->display();
		}else{
			$es=I("post.es");
			$host=$es['host'];
			$port=$es['port'];
			$from=$es['from'];
			$user=$es['user'];
			$pwd=$es['pwd'];
			
			if(empty($host)){
				exit(json_encode(array("code"=>10911,"msg"=>"SMTP服务器不能为空")));
			}
			if(empty($port)){
				exit(json_encode(array("code"=>10911,"msg"=>"SMTP服务器端口不能为空")));
			}
			if(!preg_match("/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/",$from)){
				exit(json_encode(array("code"=>10021,"info"=>"发信人邮箱格式不正确")));
			}
			if(empty($user)){
				exit(json_encode(array("code"=>10911,"msg"=>"SMTP身份验证用户名不能为空")));
			}
			if(empty($pwd)){
				exit(json_encode(array("code"=>10911,"msg"=>"SMTP身份验证密码")));
			}
			
			$data=array(
				"email_host"=>$host,
				"email_user"=>$user,
				"email_pwd"=>$pwd,
				"email_port"=>$port,
				"email_address"=>$from
			);
			$where=array();
			$where['email_setting_id']=1;
			$res=M("Email_setting")->where($where)->save($data);
			if($res!==false){
				exit(json_encode(array("code"=>10123,'msg'=>"保存成功")));
			}else{
				exit(json_encode(array("code"=>10124,'msg'=>"保存失败")));
			}
		}
	}
	public function send(){
		if(!IS_POST){
			$this->display();
		}else{
			$es=I("post.es");
			$host=$es['host'];
			$port=$es['port'];
			$from=$es['from'];
			$user=$es['user'];
			$pwd=$es['pwd'];
			$to=$es['to'];
			
			if(empty($host)){
				exit(json_encode(array("code"=>10911,"msg"=>"SMTP服务器不能为空")));
			}
			if(empty($port)){
				exit(json_encode(array("code"=>10911,"msg"=>"SMTP服务器端口不能为空")));
			}
			if(!preg_match("/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/",$from)){
				exit(json_encode(array("code"=>10021,"msg"=>"发信人邮箱格式不正确")));
			}
			if(!preg_match("/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/",$to)){
				exit(json_encode(array("code"=>10021,"msg"=>"测试邮箱格式不正确")));
			}
			if(empty($user)){
				exit(json_encode(array("code"=>10911,"msg"=>"SMTP身份验证用户名不能为空")));
			}
			if(empty($pwd)){
				exit(json_encode(array("code"=>10911,"msg"=>"SMTP身份验证密码")));
			}
			
			$test=array(
				"email_host"=>$host,
				"email_user"=>$user,
				"email_pwd"=>$pwd,
				"email_port"=>$port,
				"email_address"=>$from
			);
			
			$res=Email::send_emali($to,"Tonetron","测试邮件","测试邮件",$test);
			if($res===true) {
				exit(json_encode(array("code"=>10123,'msg'=>"发送成功")));
			}else{
				ob_end_clean();
				exit(json_encode(array("code"=>10124,'msg'=>$res)));
			}
		}
	}
}