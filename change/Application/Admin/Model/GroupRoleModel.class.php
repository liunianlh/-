<?php
namespace Admin\Model;
use Think\Model;
class GroupRoleModel extends Model {
    public function addGR($groupId,$roleId){
		$data=array(
			"group_id"=>$groupId,
			"role_id"=>$roleId
		);
		$groupRoleId=$this->add($data);
		return $groupRoleId;
	}
	public function saveGR($groupId,$roleId){
		$data=array(
			"group_id"=>$groupId
		);
		$res=$this->where(array('role_id'=>$roleId))->save($data);
		return $res;
	}
	public function delGR($roleId){
		$res=$this->where(array('role_id'=>$roleId))->delete();
		return $res;
	}
	public function getGroupIdByRoleId($roleId){
		$groupInfo=$this->where(array("role_id"=>$roleId))->select();
		$_groupIds=array();
		foreach($groupInfo as $key=>$value){
			$_groupIds[]=$value['group_id'];
		}
		return $_groupIds;
	}
	public function getRoleIdByGroupId($groupId){
		$roleInfo=$this->where(array("group_id"=>$groupId))->select();
		$_roleIds=array();
		foreach($roleInfo as $key=>$value){
			$_roleIds[]=$value['role_id'];
		}
		return $_roleIds;
	}
}