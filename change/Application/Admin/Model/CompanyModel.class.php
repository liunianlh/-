<?php
namespace Admin\Model;
use Think\Model;
class CompanyModel extends Model {
	public function getCompanyInfoByCompanyId($companyId){
		$userInfo=$this->where(array("company_id"=>$companyId))->find();
		return $userInfo;
	}
	public function getCompanyInfoByUserId($userId){
		$userInfo=$this->where(array("user_id"=>$userId))->find();
		return $userInfo;
	}
	public function getUserNameByUserId($userId){
		$where=array();
		$where['user_id']=$userId;
		return $this->where($where)->getField("company_contacts");
	}
}