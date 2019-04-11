<?php
namespace Admin\Model;
use Think\Model;
class OrderDetailModel extends Model {
    public function getOrderDetailByOrderId($orderId){
		$orderDetailInfo=$this->where(array("order_id"=>$orderId))->select();
		return $orderDetailInfo;
	}
	public function calculatePriceByNumber($orderDetailId,$number){
		$where=array();
		$where['order_detail_id']=$orderDetailId;
		$orderDetailInfo=$this->where($where)->find();
		$amount=$number*$orderDetailInfo['price'];
		$data=array(
			"total_number"=>$number,
			"amount"=>$amount
		);
		$res=$this->where($where)->save($data);
		$where2=array();
		$orderId=$orderDetailInfo['order_id'];
		$where2['order_id']=$orderId;
		$totalPrice=$this->calcTotalPriceByOrderId($orderId);
		$data2=array(
			"order_total_price"=>$totalPrice,
			"order_prev_time"=>time(),
		);
		M("Order")->where($where2)->save($data2);
		$serviceFee=M("Order")->where($where2)->getField("service_fee");
		return array("amount"=>number_format($amount),"total_price"=>number_format($totalPrice+$serviceFee));
	}
	public function calcTotalPriceByOrderId($orderId){
		$orderDetailInfo=$this->getOrderDetailByOrderId($orderId);
		$totalPrice=0.00;
		foreach($orderDetailInfo as $key=>$value){
			$totalPrice+=$value['amount'];
		}
		return $totalPrice;
	}
	public function calculatePriceByPrice($orderDetailId,$price){
		$where=array();
		$where['order_detail_id']=$orderDetailId;
		$orderDetailInfo=$this->where($where)->find();
		$amount=$price*$orderDetailInfo['total_number'];
		$data=array(
			"price"=>$price,
			"amount"=>$amount
		);
		$res=$this->where($where)->save($data);
		$where2=array();
		$orderId=$orderDetailInfo['order_id'];
		$where2['order_id']=$orderId;
		$totalPrice=$this->calcTotalPriceByOrderId($orderId);
		$data2=array(
			"order_total_price"=>$totalPrice,
			"order_prev_time"=>time(),
		);
		M("Order")->where($where2)->save($data2);
		$serviceFee=M("Order")->where($where2)->getField("service_fee");
		return array("amount"=>number_format($amount),"total_price"=>number_format($totalPrice+$serviceFee));
	}
	public function addOneSpecToOrderDetail($orderId,$specInfo){
		$total_number=$specInfo['loading']*1;
		$amount=$total_number*$specInfo['rmb'];
		$data=array(
			"order_id"=>$orderId,
			"specification_id"=>$specInfo['specification_id'],
			"model_name"=>$specInfo['model_name'],
			"products_img"=>$specInfo['products_img'],
			"products_chinese_name"=>$specInfo['products_chinese_name'],
			"length"=>$specInfo['length'],
			"color_name"=>$specInfo['color_name'],
			"loading"=>$specInfo['loading'],
			"buy_number"=>1,
			"total_number"=>$total_number,
			"price"=>$specInfo['rmb'],
			"amount"=>$amount
		);
		$specInfo['products_chinese_name']=mb_substr($specInfo['products_chinese_name'],0,6,'utf-8');
		$orderDetailId=$this->add($data);
		$specInfo['order_detail_id']=$orderDetailId;
		$specInfo['total_number']=number_format($total_number);
		$specInfo['amount']=number_format($amount);
		$totalPrice=$this->calcTotalPriceByOrderId($orderId);
		$data2=array(
			"order_total_price"=>$totalPrice,
			"order_prev_time"=>time(),
		);
		M("Order")->where(array("order_id"=>$orderId))->save($data2);
		$serviceFee=M("Order")->where(array("order_id"=>$orderId))->getField("service_fee");
		$specInfo['total_price']=number_format($totalPrice+$serviceFee);
		return $specInfo;
	}
	
	public function addOrderDetail($cache,$orderId){
		$specInfos=$cache["order_detail"];
		foreach($specInfos as $key=>$specInfo){
			
			$data=array(
				"order_id"=>$orderId,
				"specification_id"=>$specInfo['specification_id'],
				"model_name"=>$specInfo['model_name'],
				"products_img"=>empty($specInfo['spec_img'])?$specInfo['products_img']:$specInfo['spec_img'],
				"products_chinese_name"=>$specInfo['products_chinese_name'],
				"length"=>$specInfo['length'],
				"color_name"=>$specInfo['color_name'],
				"loading"=>$specInfo['loading'],
				"buy_number"=>$specInfo['buy_number'],
				"total_number"=>$specInfo['total_number'],
				"price"=>$specInfo['rmb'],
				"amount"=>$specInfo['amount']
			);
			
			$this->add($data);
		}
		
	}
}