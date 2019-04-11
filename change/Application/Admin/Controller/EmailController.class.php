<?php
namespace Admin\Controller;
use Think\Controller;
class EmailController extends BaseController {
	public function index(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10127,'msg'=>"非法操作")));
		}else{
			$to=I("post.to");
			$title=I("post.title");
			$content=I("post.content",'','');
			$isAttach=I("post.isAttach");
			$files=I("post.paths");
			if(empty($to)){
				exit(json_encode(array("code"=>10124,'msg'=>"收件人不能为空")));
			}
			if(empty($title)){
				exit(json_encode(array("code"=>10124,'msg'=>"主旨不能为空")));
			}
			if(empty($content)){
				exit(json_encode(array("code"=>10124,'msg'=>"内容不能为空")));
			}
			
			$msg_to=D("User")->getUserInfosByUserUID($to);
			if(empty($msg_to)){
				exit(json_encode(array("code"=>10124,'msg'=>"发送失败,不存在的用户")));
			}
			
			foreach($msg_to as $key=>$value){
				if(empty($value['user_id'])){
					continue;
				}
				if($isAttach=="yes"){
					$idStr=I("post.ids");
					$ids=explode(",",$idStr);
					$orderInfo=M("Order")->where(array("order_id"=>$ids[0]))->find();
				}
				$this->send_emali($value['user_email'],"Tonetron",$title,$content,$orderInfo['order_serial_number'],$files);
			}
			exit(json_encode(array("code"=>10123,'msg'=>"发送成功")));
		}
	}
    public function send_emali($address='',$from_name='Tonetron',$subject='激活邮件',$content='',$attache='',$file=''){
       Vendor("Mailer.class#phpmailer");
	   $mail=new \PHPMailer();
	   
	   //加载邮件配置
	   $email_config=M("Email_setting")
						->order("email_setting_id desc")
						->limit(1)
						->find();
		$mail->IsSMTP(); // 使用SMTP方式发送
		$mail->CharSet ="UTF-8";//设置编码，否则发送中文乱码
		$mail->Host = $email_config['email_host']; // 您的企业邮局域名
		$mail->SMTPAuth = true; // 启用SMTP验证功能
		$mail->Username = $email_config['email_user']; // 邮局用户名(请填写完整的email地址)
		$mail->Password = $email_config['email_pwd']; // 邮局密码
		$mail->SMTPSecure = 'ssl';
		$mail->Port = $email_config['email_port'];
		$mail->From = $email_config['email_address']; //邮件发送者email地址
		$mail->FromName = $from_name;
		$mail->AddAddress($address, "注册用户");//收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
		//$mail->AddReplyTo("", "");
		
		if(!empty($attache)){
			$fileRoot=$_SERVER["DOCUMENT_ROOT"];
			$filename=$fileRoot."/tonetron/Public/Uploads";
			$mail->AddAttachment($filename."/Excell/".$attache.".xls"); // 添加附件
			$mail->AddAttachment($filename."/PDF/".$attache.".pdf"); // 添加附件
		}
		if(!empty($file)){
			$fileRoot=$_SERVER["DOCUMENT_ROOT"];
			$filename=$fileRoot."/tonetron/Public/";
			
			foreach($file as $kk=>$vv){
				$mail->AddAttachment($filename.$vv[0],$vv[1]); // 添加附件
			}
		}
		$mail->IsHTML(true); // set email format to HTML //是否使用HTML格式

		$mail->Subject = $subject; //邮件标题
		$mail->Body = $content; //邮件内容
		//$mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略

		return $mail->Send();
	}
}