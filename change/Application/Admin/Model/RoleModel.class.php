<?php
namespace Admin\Model;
use Think\Model;
class RoleModel extends Model {
    public function getAllRole(){
		$info=array();
		$info=$this->select();
		$groupRoleModel=D("GroupRole");
		$groupModel=D("Group");
		$_temp=array();
		foreach($info as $key=>$value){
			$roleId=$value['role_id'];
			$roleIdKey=md5($roleId);
			if(empty($_temp[$roleIdKey])){
				$groupIds=$groupRoleModel->getGroupIdByRoleId($roleId);
				$groupName=$groupModel->getGroupNameByGroupId($groupIds);
				$groupNames=implode(",",$groupName);
				$_temp[$roleIdKey]=$groupNames;
			}else{
				$groupNames=$_temp[$roleIdKey];
			}
			$info[$key]["group"]=$groupNames;
		}
		return $info;
	}
	public function checkRoleByName($roleName){
		$count=$this->where(array("role_name"=>$roleName))->count();
		return $count;
	}
	public function getRoleInfoById($roleId){
		$roleInfo=array();
		$roleInfo=$this->where(array('role_id'=>$roleId))->find();
		$groupIds=D("GroupRole")->getGroupIdByRoleId($roleId);
		$roleInfo['group']=$groupIds[0];
		return $roleInfo;
	}
	public function getRoleNameByRoleId($roleId){
		$where=array();
		if(is_array($roleId)){
			$where['role_id']=array("in",implode(",",$roleId));
		}else{
			$where['role_id']=$roleId;
		}
		$roleName=$this->where($where)->getField("role_name",true);
		return $roleName;
	}
}