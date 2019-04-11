<?php
namespace Admin\Model;
use Think\Model;
class GroupModel extends Model {
    public function getAllGroup(){
		$info=array();
		$info=$this->select();
		return $info;
	}
	public function getGroupNameByGroupId($groupId){
		$where=array();
		if(is_array($groupId)){
			$where['group_id']=array("in",implode(",",$groupId));
		}else{
			$where['group_id']=$groupId;
		}
		$groupName=$this->where($where)->getField("group_name",true);
		return $groupName;
	}
}