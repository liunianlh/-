<?php
namespace Admin\Controller;
use Think\Controller;
use Libs\Authen;
class BaseController extends Controller {
    public function _initialize(){
		if(!isset($_SESSION['adminid'])||empty($_SESSION['adminid'])){
			$this->redirect("Login/index");
		}
		$rightList=Authen::checkRight();
		if(!IS_POST){
			if(empty($rightList['m'.MODULE_NAME]['c'.CONTROLLER_NAME]['a'.ACTION_NAME])){
				$this->error("您没有权限进行此操作");
			}
		}else{
			if(empty($rightList['m'.MODULE_NAME]['c'.CONTROLLER_NAME]['a'.ACTION_NAME])){
				exit(json_encode(array("code"=>-10000,'msg'=>"您没有权限进行此操作")));
			}
		}
		$configs=M("Config")->where(array("config_name"=>"inventory_time"))->find();
		$this->assign("configs",json_decode($configs['config_value'],true));
	}
}