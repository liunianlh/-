<?php
namespace Admin\Logic;
use Think\Model;
class UserProductLogic extends Model {
	public function getUPByUserId($uppInfo,$userId){
		$UP=array("rmb"=>0.00,"products_status_id"=>1);
		foreach($uppInfo as $key=>$value){
			if($value['user_id']==$userId){
				$UP["rmb"]=$value['rmb'];
				$UP["products_status_id"]=$value['products_status_id'];
				break;
			}
		}
		return $UP;
	}
	public function getUPByUserIdAndSpecId($uppInfo,$userId,$specId){
		$UP=array("rmb"=>0.00,"products_status_id"=>1);
		foreach($uppInfo as $key=>$value){
			if(($value['user_id']==$userId)&&($value['specification_id']==$specId)){
				$UP["rmb"]=$value['rmb'];
				$UP["products_status_id"]=$value['products_status_id'];
				break;
			}
		}
		return $UP;
	}
}