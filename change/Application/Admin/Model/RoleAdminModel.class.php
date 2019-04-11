<?php
namespace Admin\Model;
use Think\Model;
class RoleAdminModel extends Model {
    public function addRA($role,$adminId){
		$data=array(
			"admin_id"=>$adminId,
			"role_id"=>$role
		);
		$roleAdminId=$this->add($data);
		return $roleAdminId;
	}
	public function saveRA($role,$adminId){
		$data=array(
			"role_id"=>$role
		);
		$res=$this->where(array("admin_id"=>$adminId))->save($data);
		return $res;
	}
	public function delRA($adminId){
		$res=$this->where(array('admin_id'=>$adminId))->delete();
		return $res;
	}
	public function getRoleIdByAdminId($adminId){
		$roleInfo=$this->where(array("admin_id"=>$adminId))->select();
		$_roleIds=array();
		foreach($roleInfo as $key=>$value){
			$_roleIds[]=$value['role_id'];
		}
		return $_roleIds;
	}
	public function getAdminIdByRoleId($roleId){
		$where=array();
		$where['role_id']=array("in",implode(",",$roleId));
		$adminInfo=$this->where($where)->select();
		$_adminIds=array();
		foreach($adminInfo as $key=>$value){
			$_adminIds[]=$value['admin_id'];
		}
		return $_adminIds;
	}
}