<?php
namespace Home\Model;
use Think\Model;
class OrderDetailModel extends Model {
    public function createOrderDetail($cartDetail,$orderId,$countryId){
		$orderDetailIds=array();
		if($countryId==1){
			foreach($cartDetail as $key=>$value){
				$data2=array(
					"order_id"=>$orderId,
					"specification_id"=>$value["specification_id"],
					"products_img"=>$value["pic"],
					"model_name"=>$value["modelName"],
					"products_chinese_name"=>$value["specification"],
					"length"=>$value["length"],
					"color_name"=>$value["colorName"],
					"loading"=>$value["loading"],
					"buy_number"=>$value["buyNum"],
					"total_number"=>$value['buyNum']*$value['loading'],
					"price"=>$value["rmb"],
					"amount"=>$value["subtotal"],
				);
				$orderForworderId=$this->add($data2);
				$orderDetailIds[]=$orderForworderId;
			}
		}else{
			foreach($cartDetail as $key=>$value){
				$data2=array(
					"order_id"=>$orderId,
					"specification_id"=>$value["specification_id"],
					"products_img"=>$value["pic"],
					"model_name"=>$value["modelName"],
					"products_chinese_name"=>$value["specification"],
					"length"=>$value["length"],
					"color_name"=>$value["colorName"],
					"loading"=>$value["loading"],
					"buy_number"=>$value["buyNum"],
					"total_number"=>$value['buyNum']*$value['loading'],
					"price"=>RMB2USD($value["rmb"]),
					"amount"=>$value["subtotal"],
				);
				$orderForworderId=$this->add($data2);
				$orderDetailIds[]=$orderForworderId;
			}
		}
		return $orderDetailIds;
	}
	public function getOrderDetailInfoByOrderId($orderId){
		$orderInfo=$this->where(array("order_id"=>$orderId))->select();
		return $orderInfo;
	}
}