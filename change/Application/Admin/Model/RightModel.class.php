<?php
namespace Admin\Model;
use Think\Model;
class RightModel extends Model {
    public function getAllRight(){
		return $this->select();
	}
	public function getRightInfoByRightId($rightId){
		$where=array();
		if(is_array($rightId)){
			$where['right_id']=array("in",implode(",",$rightId));
		}else{
			$where['right_id']=$rightId;
		}
		$info=$this->where($where)->select();
		return $info;
	}
}