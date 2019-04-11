<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model {
    public function getUserInfoByUserId($userId){
		$userInfo=$this->where(array("user_id"=>$userId))->find();
		return $userInfo;
	}
}