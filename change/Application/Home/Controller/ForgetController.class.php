<?php
namespace Home\Controller;
use Think\Controller;
use Libs\Email;
class ForgetController extends Controller {
    public function index(){
		if(!IS_POST){
			$this->display();
		}else{
			$UID=I("post.UID");
			$email=I("post.email");
			
			$where=array();
			$where['user_uid']=$UID;
			$userInfo=M("User")->where($where)->find();
			if($userInfo['user_email']==trim($email)){
				$time=time();
				$verify=generateRandCode("all",20);
				$data=array(
					"code"=>md5($userInfo['user_id']),
					"user_id"=>$userInfo['user_id'],
					"user_uid"=>$userInfo['user_uid'],
					"verify"=>$verify,
					"time"=>$time,
					"is_use"=>1
				);
				M("Find_password")->add($data);
				$content="<font color='red'>".$UID."</font>,您正在通过邮件找回密码<br/>";
				$content.=U("Reset/index",array("rnd"=>str_replace("=",'',base64_encode(md5($userInfo['user_id']))),"t"=>$time,'v'=>$verify),'',true);
				$res=Email::send_emali($email,'Tonetron','找回密码',$content);
				if($res!==false){
					exit(json_encode(array("code"=>"10711","msg"=>"我们将发一封密码重置的邮件到您的邮箱，请及时重置，谢谢。")));
				}else{
					exit(json_encode(array("code"=>"10713","msg"=>"服务器繁忙，请稍后重试...")));
				}
			}else{
				exit(json_encode(array("code"=>"10712","msg"=>"UID或邮箱错误")));
			}
		}
	}
}