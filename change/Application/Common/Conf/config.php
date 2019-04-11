<?php
return array(
	//'配置项'=>'配置值'
	"LOAD_EXT_CONFIG"=>"db",
	"LOAD_EXT_FILE"=>"common",
	
	//验证码配置
	"SEKEY"=>"tonetron",
	"EXPIRE"=>time()+1800,//30分钟
	
	//配置可访问模块
	"MODULE_ALLOW_LIST"=>array("Home","Admin","ACH"),
	//加密key
	"ENC_KEY"=>"tonetron",
	
	//分页大小
	"PAGESIZE"=>20,
	
	//url模式
	"URL_MODEL"=>2,
	
	//多语言支持
	"LANG_SWITCH_ON"=>true,
	"LANG_AUTO_DETECT"=>true,
	"LANG_LIST"=>"zh-cn,en-us",
	"VAR_LANGUAGE"=>"hl",
);