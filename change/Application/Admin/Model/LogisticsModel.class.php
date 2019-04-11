<?php
namespace Admin\Model;
use Think\Model;
class LogisticsModel extends Model {
    public function getAllLogisticsByUserId($userId){
		$info=$this->where(array("user_id"=>$userId))->select();
		foreach($info as $key=>$value){
			$info[$key]["checkKey"]=md5($value['logistics_id'].$value['user_id']);
		}
		return $info;
	}
	public function getLogisticsCountByUserId($userId){
		$count=$this->where(array("user_id"=>$userId))->count();
		return $count;
	}
	public function delLogisticsByLogisticsId($userId,$logisticsId){
		$res=$this->where(array("user_id"=>$userId,"logistics_id"=>$logisticsId))->delete();
		return $res;
	}
	public function getLogisticsInfo($userId,$logisticsId){
		$info=$this->where(array("user_id"=>$userId,"logistics_id"=>$logisticsId))->find();
		return $info;
	}
}