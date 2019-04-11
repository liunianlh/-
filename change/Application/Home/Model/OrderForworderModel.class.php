<?php
namespace Home\Model;
use Think\Model;
class OrderForworderModel extends Model {
    public function createOrderForworder($data,$orderId){
		$shipmentInfo=D("Admin/Shipment")->getShipmentInfoByShipmentId($data['fwdShipment']);
		$areaModel=D("Admin/Area");
		$data2=array(
			"order_id"=>$orderId,
			"for_company_name"=>trim($data['fwdCompanyName']),
			"for_receiver"=>trim($data['fwdReceiver']),
			"for_receiver_phone"=>trim($data['fwdReceiverPhone']),
			"for_receiver_email"=>trim($data['fwdReceiverEmail']),
			"for_dist"=>'',
			"for_address"=>trim($data['fwdAddress']),
			"shipment"=>$shipmentInfo['shi_chinese_name'],
			"shipment2"=>$shipmentInfo['shi_english_name'],
			"web_mode"=>webMode(),
		);
		$countryInfo=M("Country")->where(array("country_id"=>$data['fwdCountry']))->find();
		if($data['fwdCountry']==1){
			$cityInfo=$areaModel->getAreaInfoByAreaId($data['fwdCity']);
			$provinceInfo=$areaModel->getAreaInfoByAreaId($data['fwdProv']);
			
			$data2['for_country_id']=$data['fwdCountry'];
			$data2['for_country']=$countryInfo['country_name'];
			$data2['for_country2']=$countryInfo['country_name2'];
			$data2['for_city']=$cityInfo['area_name'];
			$data2['for_city2']=$cityInfo['area_name2'];
			$data2['for_province']=$provinceInfo['area_name'];
			$data2['for_province2']=$provinceInfo['area_name2'];
		}else{
			$data2['for_country_id']=$data['fwdCountry'];
			$data2['for_country']=$countryInfo['country_name'];
			$data2['for_country2']=$countryInfo['country_name2'];
			$data2['for_city']=$data['fwdCity'];
			$data2['for_province']=$data['fwdProv'];
		}
		
		$orderForworderId=$this->add($data2);
		return $orderForworderId;
	}
	public function addOrderForworder($data,$orderId){
		//$shipmentInfo=D("Admin/Shipment")->getShipmentInfoByShipmentId($data['fwdShipment']);
		$areaModel=D("Admin/Area");
		$data2=array(
			"order_id"=>$orderId,
			"for_company_name"=>trim($data['fwdCompanyName']),
			"for_receiver"=>trim($data['fwdReceiver']),
			"for_receiver_phone"=>trim($data['fwdReceiverPhone']),
			"for_receiver_email"=>trim($data['fwdReceiverEmail']),
			"for_dist"=>'',
			"for_address"=>trim($data['fwdAddress']),
			//"shipment"=>$shipmentInfo['shi_chinese_name']
		);
		
		if($data['fwdCountry']==1){
			$data['for_country']="中国";
			$data2['for_city']=$areaModel->getAreaNameByAreaId($data['fwdCity']);
			$data2['for_province']=$areaModel->getParentAreaNameByAreaId($data['fwdCity']);
		}else{
			$data2['for_country']=M("Country")->where(array("country_id"=>$data['fwdCountry']))->getField("country_name");
			$data2['for_city']=$data['fwdCity'];
			$data2['for_province']=$data['fwdProv'];
		}
		
		$orderForworderId=$this->add($data2);
		return $orderForworderId;
	}
	public function getOrderForworderByOrderId($orderId){
		$orderForworderInfo=$this->where(array("order_id"=>$orderId))->find();
		return $orderForworderInfo;
	}
	
	public function saveOrderForworder($forworder='',$orderId=''){
		
		$where=array();
		$where['order_forworder_id']=$forworder['id'];
		
		$areaModel=D("Admin/Area");
		$data=array(
			"for_company_name"=>$forworder['fwdCompanyName'],
			"for_receiver"=>$forworder['fwdReceiver'],
			"for_receiver_phone"=>$forworder['fwdReceiverPhone'],
			"for_receiver_email"=>$forworder['fwdReceiverEmail'],
			"for_country"=>M("Country")->where(array("country_id"=>$forworder['fwdCountry']))->getField("country_name"),
			"for_dist"=>'',
			"for_address"=>$forworder['fwdAddress'],
		);
		if($forworder['fwdCountry']==1){
			$data["for_province"]=D("Admin/Area")->getParentAreaNameByAreaId($forworder['fwdCity']);
			$data["for_city"]=$areaModel->getAreaNameByAreaId($forworder['fwdCity']);
		}else{
			$data["for_province"]=$forworder['fwdProv'];
			$data["for_city"]=$forworder['fwdCity'];
		}
		
		
		if(!empty($forworder['id'])){
			$res=$this->where($where)->save($data);
		}else{
			if(!empty($forworder['fwdCompanyName'])&&!empty($forworder['fwdReceiver'])&&!empty($forworder['fwdReceiverPhone'])){
				$data['order_id']=$orderId;
				$res=$this->add($data);
			}
		}
		return $res;
	}
}