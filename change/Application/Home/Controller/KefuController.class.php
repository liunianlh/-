<?php
namespace Home\Controller;
class KefuController extends BaseController {
    public function index(){
		$userInfo=D('User')->getUserInfoByUserId($_SESSION['userid']);
		$adminInfo=M("Admin")->where(array("admin_id"=>$userInfo['admin_id']))->find();
		$this->assign("admin_info",$adminInfo);
		$this->display();
	}
}