<?php
namespace Admin\Model;
use Think\Model;
class RoleRightModel extends Model {
    public function getRightIdByRoleId($roleId){
		$where=array();
		if(is_array($roleId)){
			$where['role_id']=array("in",implode(",",$roleId));
			return $this->where($where)->getField("right_id",true);
		}else{
			$where['role_id']=$roleId;
			$info=$this->where($where)->find();
			return $info['right_id'];
		}
	}
	public function addOrSaveRight($roleId,$nodes){
		$data=array(
			"right_id"=>implode(",",$nodes)
		);
		if($this->isExistsRoleId($roleId)){
			$where=array();
			$where['role_id']=$roleId;
			$res=$this->where($where)->save($data);
		}else{
			$data['role_id']=$roleId;
			$res=$this->add($data);
		}
		return $res;
	}
	public function isExistsRoleId($roleId){
		$where=array();
		$where['role_id']=$roleId;
		$count=$this->where($where)->count();
		if($count>0){
			return true;
		}else{
			return false;
		}
	}
	
}