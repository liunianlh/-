<?php
namespace Libs;
class Email{
    public static function send_emali($address='',$from_name='Tonetron',$subject='激活邮件',$content='',$test=array()){
       Vendor("Mailer.class#phpmailer");
	   $mail=new \PHPMailer();
	   
	   //加载邮件配置
	    if(!empty($test)){
		   $email_config=$test;
	    }else{
		   $email_config=M("Email_setting")
						->order("email_setting_id desc")
						->limit(1)
						->find();
	    }
	   
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

		//$mail->AddAttachment("/var/tmp/file.tar.gz"); // 添加附件
		$mail->IsHTML(true); // set email format to HTML //是否使用HTML格式

		$mail->Subject = $subject; //邮件标题
		$mail->Body = $content; //邮件内容
		//$mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略
		
		if($mail->Send()){
			return true;
		}else{
			return $mail->ErrorInfo;
		}
	}
}