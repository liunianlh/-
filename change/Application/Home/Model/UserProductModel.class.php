<?php
namespace Home\Model;
use Think\Model;
class UserProductModel extends Model {
    public function getUserProductInfoBySU($specId,$userId){// SU===specification_id  user_id
		$where=array();
		$where['specification_id']=$specId;
		$where['user_id']=$userId;
		$userProduct=$this->where($where)->find();
		return $userProduct;
	}
}