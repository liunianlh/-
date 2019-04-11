<?php
namespace Admin\Model;
use Think\Model;
class ForworderModel extends Model {
    public function getAllForworderByUserId($userId){
		$info=$this->where(array("user_id"=>$userId))->select();
		foreach($info as $key=>$value){
			$info[$key]["checkKey"]=md5($value['forworder_id'].$value['user_id']);
		}
		return $info;
	}
	public function getForworderCountByUserId($userId){
		$count=$this->where(array("user_id"=>$userId))->count();
		return $count;
	}
	public function delForworderByForworderId($userId,$forworderId){
		$res=$this->where(array("user_id"=>$userId,"forworder_id"=>$forworderId))->delete();
		return $res;
	}
	public function getForworderInfo($userId,$forworderId){
		$info=$this->where(array("user_id"=>$userId,"forworder_id"=>$forworderId))->find();
		return $info;
	}
	public function getForworderId($data,$userId){
		$where=array();
		$where['for_company_name']=trim($data['fwdCompanyName']);
		$where['for_receiver']=trim($data['fwdReceiver']);
		$where['for_receiver_phone']=trim($data['fwdReceiverPhone']);
		$where['for_receiver_email']=trim($data['fwdReceiverEmail']);
		$where['for_country']=trim($data['fwdCountry']);
		$where['for_city']=trim($data['fwdCity']);
		$where['for_dist']=trim($data['fwdDist']);
		$where['for_address']=trim($data['fwdAddress']);
		$where['shipment_id']=trim($data['fwdShipment']);
		$where['user_id']=$userId;
		
		$forworderId=0;
		$data2=$where;
		$forworderInfo=$this->where($where)->find();
		if(!empty($forworderInfo)){
			$forworderId=$forworderInfo['forworder_id'];
			$data2['for_edit_time']=time();
			$res=$this->where(array("forworder_id"=>$forworderId))->save($data2);
		}else{
			$count=$this->getForworderCountByUserId($userId);
			if($count>=10){
				return false;
			}
			$data2['for_province']=D("Admin/Area")->getParentAreaNameByAreaId($where['for_city']);
			$data2['for_add_time']=time();
			$forworderId=$this->add($data2);
		}
		return $forworderId;
	}
}