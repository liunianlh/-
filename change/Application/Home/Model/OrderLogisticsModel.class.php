<?php
namespace Home\Model;
use Think\Model;
class OrderLogisticsModel extends Model {
    public function createOrderLogistics($data,$orderId){
		$areaModel=D("Admin/Area");
		$data2=array(
			"order_id"=>$orderId,
			'logistics_company_name'=>trim($data['companyName']),
			'logistics_receiver'=>trim($data['receiver']),
			'logistics_receiver_phone'=>trim($data['receiverPhone']),
			'logistics_receiver_email'=>trim($data['receiverEmail']),
			'logistics_dist'=>'',
			'logistics_address'=>trim($data['address'])
		);
		
		if($data['country']==1){
			$data2['logistics_country']="中国";
			$data2['logistics_city']=$areaModel->getAreaNameByAreaId($data['city']);
			$data2['logistics_province']=$areaModel->getParentAreaNameByAreaId($data['city']);
		}else{
			$data2['logistics_country']=M("Country")->where(array("country_id"=>$data['country']))->getField("country_name");
			$data2['logistics_city']=$data['city'];
			$data2['logistics_province']=$data['prov'];
		}
		$orderLogisticsId=$this->add($data2);
		return $orderLogisticsId;
	}
	public function addOrderLogistics($data,$orderId){
		$areaModel=D("Admin/Area");
		$data2=array(
			"order_id"=>$orderId,
			'logistics_company_name'=>trim($data['logCompanyName']),
			'logistics_receiver'=>trim($data['logReceiver']),
			'logistics_receiver_phone'=>trim($data['logReceiverPhone']),
			'logistics_receiver_email'=>trim($data['logReceiverEmail']),
			'logistics_dist'=>'',
			'logistics_address'=>trim($data['address']),
		);
		
		if($data['logCountry']==1){
			$data2['logistics_country']="中国";
			$data2['logistics_city']=$areaModel->getAreaNameByAreaId($data['city']);
			$data2['logistics_province']=$areaModel->getParentAreaNameByAreaId($data['city']);
		}else{
			$data2['logistics_country']=M("Country")->where(array("country_id"=>$data['logCountry']))->getField("country_name");
			$data2['logistics_city']=$data['city'];
			$data2['logistics_province']=$data['prov'];
		}
		
		$orderLogisticsId=$this->add($data2);
		return $orderLogisticsId;
	}
	public function getOrderLogisticsByOrderId($orderId){
		$orderLogisticsInfo=$this->where(array("order_id"=>$orderId))->find();
		return $orderLogisticsInfo;
	}
	
	public function saveOrderLogistics($logistics='',$orderId=''){
		
		$where=array();
		$where['order_logistics_id']=$logistics['id'];
		
		$areaModel=D("Admin/Area");
		$data=array(
			"logistics_company_name"=>$logistics['logCompanyName'],
			"logistics_receiver"=>$logistics['logReceiver'],
			"logistics_receiver_phone"=>$logistics['logReceiverPhone'],
			"logistics_receiver_email"=>$logistics['logReceiverEmail'],
			"logistics_country"=>M("Country")->where(array("country_id"=>$logistics['logCountry']))->getField("country_name"),
			"logistics_dist"=>'',
			"logistics_address"=>$logistics['address']
		);
		if($logistics['logCountry']==1){
			$data["logistics_province"]=$areaModel->getParentAreaNameByAreaId($logistics['city']);
			$data["logistics_city"]=$areaModel->getAreaNameByAreaId($logistics['city']);
		}else{
			$data["logistics_province"]=$logistics['prov'];
			$data["logistics_city"]=$logistics['city'];
		}
		if(!empty($logistics['id'])){
			$res=$this->where($where)->save($data);
		}else{
			if(!empty($logistics['logCompanyName'])&&!empty($logistics['logReceiver'])&&!empty($logistics['logReceiverPhone'])){
				$data['order_id']=$orderId;
				$res=$this->add($data);
			}
		}
		return $res;
	}
}