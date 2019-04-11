<?php
namespace Admin\Controller;
use Think\Controller;
class RateController extends BaseController {
	public function index(){
		if(!IS_POST){
			$rmb=M("Config")
						->where(array("config_name"=>"rate_rmb"))
						->getField("config_value");
			$usd=M("Config")
						->where(array("config_name"=>"rate_usd"))
						->getField("config_value");
			$rateInfo=array(
				"rmb"=>empty($rmb)?'':$rmb,
				"usd"=>empty($usd)?'':$usd
			);
			$this->assign("rate_info",$rateInfo);
			$this->display();
		}else{
			$rmb=I("post.rmb");
			$usd=I("post.usd");
			
			if(!is_numeric($rmb)){
				exit(json_encode(array("code"=>10911,"msg"=>"人民币汇率应该是数字")));
			}
			if(!is_numeric($usd)){
				exit(json_encode(array("code"=>10911,"msg"=>"美金汇率应该是数字")));
			}
			
			if($rmb<=0){
				exit(json_encode(array("code"=>10911,"msg"=>"人民币汇率应该大于0")));
			}
			if($usd<=0){
				exit(json_encode(array("code"=>10911,"msg"=>"美金汇率应该大于0")));
			}
			
			$where1=array(
				"config_name"=>"rate_rmb"
			);
			$count1=M("Config")->where($where1)->count();
			if($count1>0){
				M("Config")->where($where1)->save(array("config_value"=>$rmb));
			}else{
				M("Config")->add(array("config_name"=>"rate_rmb","config_value"=>$rmb));
			}
			
			$where2=array(
				"config_name"=>"rate_usd"
			);
			$count2=M("Config")->where($where2)->count();
			if($count2>0){
				M("Config")->where($where2)->save(array("config_value"=>$usd));
			}else{
				M("Config")->add(array("config_name"=>"rate_usd","config_value"=>$usd));
			}
			exit(json_encode(array("code"=>10123,'msg'=>"保存成功")));
		}
	}
}