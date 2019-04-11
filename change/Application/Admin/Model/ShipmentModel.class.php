<?php
namespace Admin\Model;
use Think\Model;
class ShipmentModel extends Model {
    public function getAllShipment(){
		$info=array();
		$info=$this->select();
		return $info;
	}
	public function getShipmentInfoByShipmentId($shipmentId){
		$info=$this->where(array("shipment_id"=>$shipmentId))->find();
		return $info;
	}
	public function checkShipmentByName($shipmentName){
		$count=$this->where(array("shi_chinese_name"=>$shipmentName))->count();
		return $count;
	}
}