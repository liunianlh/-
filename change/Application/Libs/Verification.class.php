<?php
namespace Libs;
use Think\Verify;
class Verification{
	//生成验证码函数
	public function createVerifyCode(){
		$config =array(
			'seKey'     =>  C('SEKEY'),
			'expire'    =>  C('EXPIRE'),            // 验证码过期时间（s）
			'fontSize'=>16,
			'length'    =>  4,               // 验证码位数
			'useCurve'  =>  true,            // 是否画混淆曲线
			'useNoise'  =>  false,            // 是否添加杂点
			'imageH'    =>  40,               // 验证码图片高度
			'imageW'    =>  128,               // 验证码图片宽度
			'fontttf'   =>  '5.ttf',           // 验证码字体，不设置随机获取
		);
		$Verify =new Verify($config);
		$Verify->entry();
	}
	//校验验证码
	public function checkVerify($code){
		$config =	array(
			'seKey'     =>  C('SEKEY'),
			'expire'    =>  C('EXPIRE'),      // 验证码过期时间（s）
        );
		$Verify =     new Verify($config);
		return $Verify->check($code);
	}
}