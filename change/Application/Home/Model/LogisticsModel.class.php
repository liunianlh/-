<?php
namespace Home\Model;
use Think\Model;
class LogisticsModel extends Model {
    public function getLogisticsId($data,$userId){
		$where=array();
		$where['logistics_company_name']=trim($data['companyName']);
		$where['logistics_receiver']=trim($data['receiver']);
		$where['logistics_receiver_phone']=trim($data['receiverPhone']);
		$where['logistics_receiver_email']=trim($data['receiverEmail']);
		$where['logistics_country']=trim($data['country']);
		$where['logistics_city']=trim($data['city']);
		$where['logistics_dist']=trim($data['dist']);
		$where['logistics_address']=trim($data['address']);
		$where['user_id']=$userId;
		
		$logisticsId=0;
		$data2=$where;
		$logisticsInfo=$this->where($where)->find();
		if(!empty($logisticsInfo)){
			$logisticsId=$forworderInfo['forworder_id'];
		}else{
			$data2['logistics_province']=D("Admin/Area")->getParentAreaNameByAreaId($where['logistics_city']);
			$data2['logistics_time']=time();
			$logisticsId=$this->add($data2);
		}
		return $logisticsId;
	}
	public function getLogisticsInfoByUserId($userId){
		$logisticsInfo=$this->where(array("user_id"=>$userId))->select();
		return $logisticsInfo;
	}
}