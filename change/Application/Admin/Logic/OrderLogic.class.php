<?php
namespace Admin\Logic;
use Think\Model;
class OrderLogic extends Model {
	public function getTodayOrder(){
		$orderInfo=D("Order")->getTodayOrder();
		$today=getToday();
		return $this->dealOrder($orderInfo,$today);
	}
	
	public function getMonthOrder(){
		$orderInfo=D("Order")->getMonthOrder();
		$month=getCurrentMonth();
		return $this->dealOrder($orderInfo,$month);
	}
	
	private function dealOrder($orderInfo,$time){
		$dealOrderCount=0;//处理订单数量
		$dealOrderMoney=0;//处理订单金额
		$undealOrderCount=0;//未处理订单数量
		$undealOrderMoney=0;//未处理订单金额
		
		$sendOrderCount=0;//已发货订单数量
		$sendOrderMoney=0;//已发货订单金额
		$completedOrderCount=0;//已完成订单数量
		$completedOrderMoney=0;//已完成订单金额
		
		foreach($orderInfo as $key=>$value){
			if(($value['order_time']>=$time)&&($value['order_sure_time']<$time)){
				$undealOrderCount++;
				if($value['order_currency']=="USD"){
					$RMB=USD2RMB($value['order_total_price']+$value['service_fee']);
					$undealOrderMoney+=$RMB;
				}else{
					$undealOrderMoney+=($value['order_total_price']+$value['service_fee']);
				}
			}
			if(($value['order_sure_time']>=$time)){
				$dealOrderCount++;
				if($value['order_currency']=="USD"){
					$RMB=USD2RMB($value['order_total_price']+$value['service_fee']);
					$dealOrderMoney+=$RMB;
				}else{
					$dealOrderMoney+=($value['order_total_price']+$value['service_fee']);
				}
			}
			if(($value['order_send_time']>=$time)){
				$sendOrderCount++;
				if($value['order_currency']=="USD"){
					$RMB=USD2RMB($value['order_total_price']+$value['service_fee']);
					$sendOrderMoney+=$RMB;
				}else{
					$sendOrderMoney+=($value['order_total_price']+$value['service_fee']);
				}
			}
			if(($value['order_complete_time']>=$time)){
				$completedOrderCount++;
				if($value['order_currency']=="USD"){
					$RMB=USD2RMB($value['order_total_price']+$value['service_fee']);
					$completedOrderMoney+=$RMB;
				}else{
					$completedOrderMoney+=($value['order_total_price']+$value['service_fee']);
				}
			}
		}
		
		$data=array(
			"dealOrderCount"=>$dealOrderCount,
			"dealOrderMoney"=>$dealOrderMoney,
			"undealOrderCount"=>$undealOrderCount,
			"undealOrderMoney"=>$undealOrderMoney,
			"sendOrderCount"=>$sendOrderCount,
			"sendOrderMoney"=>$sendOrderMoney,
			"completedOrderCount"=>$completedOrderCount,
			"completedOrderMoney"=>$completedOrderMoney
		);
		return $data;
	}
}