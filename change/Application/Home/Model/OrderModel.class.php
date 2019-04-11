<?php
namespace Home\Model;
use Think\Model;
class OrderModel extends Model {
    public function createOrder($data,$userId){
		
		$productsData=$data['productsData'];
		$totalPrice=$data['totalPrice'];
		$orderSerialNumber=$data['orderSerialNumber'];
		$dealTime=$data['dealTime'];
		$userInfo=D("User")->getUserInfoByUserId($userId);
		$companyInfo=M("Company")->where(array("user_id"=>$userId))->find();
		$userName=$companyInfo["company_contacts"];
		
		if($productsData["logisticsInfo"]["country"]==1){
			$data2=array(
				"order_prev_time"=>time(),
				"order_ponumber"=>$productsData['orderInfo']['PONumber'],
				"order_serial_number"=>$orderSerialNumber,
				"order_time"=>$dealTime,
				"order_total_price"=>$totalPrice,
				"user_id"=>$userId,
				"user_name"=>empty($userName)?"":$userName,
				"company_name"=>$companyInfo['company_name'],
				"user_uid"=>$userInfo['user_uid'],
				"tianxin_code"=>$userInfo['tianxin_code'],
				"order_remark"=>$productsData['orderInfo']['remark'],
				"order_currency"=>"RMB"
			);
			if(!empty($userInfo['admin_id'])){
				$admin=M("Admin")->where(array("admin_id"=>$userInfo['admin_id']))->getField("admin_name");
			}else{
				$admin="--";
			}
			$data2['admin_name']=$admin;
			$data2['admin_id']=$userInfo['admin_id'];
			if($data2["order_total_price"]>=20000){
				$data2['service_fee']=0;
			}else{
				$cartDetail=$data['cartDetail'];
				
				$specIds=array();
				$buyNum=array();
				
				foreach($cartDetail as $kk=>$vv){
					$specIds[]=$vv['specification_id'];
					$buyNum=$vv['buyNum'];
				}
				$result=calculateLogistics($productsData["logisticsInfo"]["city"],$specIds,$buyNum);
				if($result['pw']=="RMB"){
					$data2['service_fee']=$result['money'];
				}
				if($result['pw']=="USD"){
					$data2['service_fee']=USD2RMB($result['money']);
				}
				if($result['pw']=="UN"){
					$data2['service_fee']=0;
				}
			}
		}else{
			$data2=array(
				"order_prev_time"=>time(),
				"order_ponumber"=>$productsData['orderInfo']['PONumber'],
				"order_serial_number"=>$orderSerialNumber,
				"order_time"=>$dealTime,
				"order_total_price"=>$totalPrice,
				"user_name"=>empty($userName)?"":$userName,
				"user_id"=>$userId,
				"company_name"=>$companyInfo['company_name'],
				"user_uid"=>$userInfo['user_uid'],
				"tianxin_code"=>$userInfo['tianxin_code'],
				"order_remark"=>$productsData['orderInfo']['remark'],
				"order_currency"=>"USD"
			);
			if(!empty($userInfo['admin_id'])){
				$admin=M("Admin")->where(array("admin_id"=>$userInfo['admin_id']))->getField("admin_name");
			}else{
				$admin="--";
			}
			$data2['admin_name']=$admin;
			$data2['admin_id']=$userInfo['admin_id'];
			if($data2["order_total_price"]>=20000){
				$data2['service_fee']=0;
			}else{
				$data2['service_fee']=250.00;
			}
		}
		$orderId=$this->add($data2);
		return $orderId;
	}
	public function getOrderInfoByOrderId($orderId){
		$orderInfo=$this->where(array("order_id"=>$orderId))->find();
		return $orderInfo;
	}
}