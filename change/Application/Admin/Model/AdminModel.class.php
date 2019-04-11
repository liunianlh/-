<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model {
    private $userInfo=array();
	
	public function getAllUser(){
		$info=$this->select();
		$roleAdminModel=D("RoleAdmin");
		$roleModel=D("Role");
		$_temp=array();
		foreach($info as $key=>$value){
			$adminId=$value['admin_id'];
			$adminIdKey=md5($adminId);
			if(empty($_temp[$adminIdKey])){
				$roleIds=$roleAdminModel->getRoleIdByAdminId($adminId);
				$roleName=$roleModel->getRoleNameByRoleId($roleIds);
				$roleNames=implode(",",$roleName);
				$_temp[$adminIdKey]=$roleNames;
			}else{
				$roleNames=$_temp[$adminIdKey];
			}
			$info[$key]["role"]=$roleNames;
		}
		return $info;
	}
	public function checkAdminByName($adminName){
		$count=$this->where(array("admin_name"=>$adminName))->count();
		return $count;
	}
	public function getAdminInfoById($adminId){
		$adminInfo=array();
		$adminInfo=$this->where(array('admin_id'=>$adminId))->find();
		$roleIds=D("RoleAdmin")->getRoleIdByAdminId($adminId);
		$adminInfo['role']=$roleIds[0];
		return $adminInfo;
	}
	public function checkLogin($account,$pwd){
		$info=$this->where(array("admin_name"=>$account))->select();
		foreach($info as $key=>$value){
			$strKey=substr(md5($value['admin_verify']),6,6);
			$pwd=md5($strKey.$pwd);
			if($pwd==$value['admin_pwd']){
				$this->userInfo=$value;
				break;
			}
		}
		if(!empty($this->userInfo)){
			return $this->userInfo;
		}else{
			return false;
		}
	}
	public function getAdminInfosByAdminIds($adminIds){
		$where=array();
		$where['admin_id']=array("in",implode(",",$adminIds));
		$adminInfos=$this->where($where)->select();
		return $adminInfos;
	}
	public function getAdminNameByAdminId($adminId){
		$adminName='';
		$adminInfo=$this->where(array('admin_id'=>$adminId))->find();
		if(!empty($adminInfo)){
			$adminName=$adminInfo['admin_name'];
		}
		return $adminName;
	}
}