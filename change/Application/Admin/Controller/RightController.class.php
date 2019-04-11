<?php
namespace Admin\Controller;
use Think\Controller;
class RightController extends BaseController {
    public function index(){
		$roleId=I("get.id");
		$roleName=D("Role")->getRoleNameByRoleId($roleId);
		$rightId=D("RoleRight")->getRightIdByRoleId($roleId);
		$rightIds=explode(",",$rightId);
		$rights=D("Right")->getAllRight();
		
		$datas=array();
		foreach($rights as $key=>$value){
			$data=array(
				"id"=>$value['right_id'],
				"name"=>$value['name'],
				"pid"=>$value['pid']
			);
			if(false===array_search($value['right_id'],$rightIds)){
				$data['checked']=false;
			}else{
				$data['checked']=true;
			}
			$datas[]=$data;
		}
		$this->assign("role_id",$roleId);
		$this->assign("role_name",$roleName[0]);
		$this->assign("nodes",json_encode($datas));
		$this->display();
	}
	public function save(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$roleId=I("post.rid");
			$sels=I("post.sels");
			$res=D("RoleRight")->addOrSaveRight($roleId,$sels);
			if(false!==$res){
				exit(json_encode(array("code"=>10103,"msg"=>"保存数据成功")));
			}else{
				exit(json_encode(array("code"=>10104,"msg"=>"保存数据失败，请稍后重试...")));
			}
		}
	}
}