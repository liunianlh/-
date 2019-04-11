<?php
namespace Admin\Model;
use Think\Model;
class LogisticsCompanyModel extends Model {
    public function getAllLogisticsCompany(){
		$info=array();
		$info=$this->select();
		return $info;
	}
	public function getLogisticsCompanyById($companyId){
		$where=array();
		$where['logistics_company_id']=$companyId;
		$info=$this->where($where)->find();
		return $info['logistics_company_name'];
	}
}