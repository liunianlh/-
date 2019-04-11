<?php
namespace Admin\Model;
use Think\Model;
class OrderModel extends Model {
    public function getAllOrdersCount($userId){
		$where=array();
		$where['user_id']=$userId;
		$count=$this->where($where)->count();
		return $count;
	}
	public function getAllOrdersCountByOrderStatus($orderStatus,$userId){
		$where=array();
		$where['user_id']=$userId;
		$where["order_status"]=$orderStatus;
		$count=$this->where($where)->count();
		return $count;
	}
	public function getAllCurrentMonthOrdersCount($userId){
		$currentMonthTimestamp=strtotime(date("Y-m",time()));
		$where=array();
		$where['user_id']=$userId;
		$where['order_time']=array("egt",$currentMonthTimestamp);
		$count=$this->where($where)->count();
		return $count;
	}
	public function getAllCurrentMonthOrdersMoney($orderCurrency,$userId){
		$currentMonthTimestamp=strtotime(date("Y-m",time()));
		$where=array();
		$where['user_id']=$userId;
		$where['order_time']=array("egt",$currentMonthTimestamp);
		$where['order_status']=5;
		$where['order_currency']=$orderCurrency;
		$orderInfo=$this->where($where)->select();
		
		$money=0;
		foreach($orderInfo as $key=>$value){
			$money+=$value['order_total_price'];
		}
		return $money;
	}
	public function getLastOrderTime($userId){
		$time="--";
		$where['user_id']=$userId;
		$orderInfo=$this->where($where)->order("order_time desc")->find();
		if(!empty($orderInfo)){
			$time=date("Y-m-d H:i:s",$orderInfo['order_time']);
		}
		return $time;
	}
	public function getCollectOrderInfoByUserId($userId){
		$allOrdersCount=$this->getAllOrdersCount($userId);
		$completedOrdersCount=$this->getAllOrdersCountByOrderStatus(3,$userId);
		$canceledOrdersCount=$this->getAllOrdersCountByOrderStatus(4,$userId);
		$currentMonthOrdersCount=$this->getAllCurrentMonthOrdersCount($userId);
		$currentMonthOrdersUSD=$this->getAllCurrentMonthOrdersMoney("USD",$userId);
		$currentMonthOrdersRMB=$this->getAllCurrentMonthOrdersMoney("RMB",$userId);
		$lastOrderTime=$this->getLastOrderTime($userId);
		$data=array(
			"all"=>$allOrdersCount,
			"completed"=>$completedOrdersCount,
			"canceled"=>$canceledOrdersCount,
			"current"=>$currentMonthOrdersCount,
			"usd"=>$currentMonthOrdersUSD,
			"rmb"=>$currentMonthOrdersRMB,
			"last_time"=>$lastOrderTime
		);
		return $data;
	}
	public function getAllOrdersByuserId($page,$where,$userId){
		if(!is_array($where)){
			$where=array();
		}
		$where['user_id']=$userId;
		$orderInfos=$this->where($where)->limit($page->firstRow.",".$page->listRows)->select();
		return $orderInfos;
	}
	public function getAllOrders($page,$where){
		$orderInfos=$this
		->where($where)
		->order(array("www_order.order_time desc"))
		->limit($page->firstRow.",".$page->listRows)
		->select();
		return $orderInfos;
	}
	public function calcServiceFee($orderId,$serviceFee){
		$where=array();
		$where['order_id']=$orderId;
		$orderInfo=$this->where($where)->find();
		$data=array(
			"service_fee"=>$serviceFee
		);
		$this->where($where)->save($data);
		$total_price=$serviceFee+$orderInfo['order_total_price'];
		return $total_price;
	}
	
	public function saveOrder($order){
		$where=array();
		$where['order_id']=$order['id'];
		
		$data=array(
			"order_prev_time"=>time(),
			"order_status"=>6,//等待确认
			"admin_id"=>$order['operator'],
			"admin_name"=>D("Admin")->getAdminNameByAdminId($order['operator'])
		);
		
		$res=$this->where($where)->save($data);
		return $res;
	}
	
	public function addOrder($order,$specInfo,$userId){
		
		$userInfo=M("User")->where(array("user_id"=>$userId))->find();
		$companyInfo=M("Company")->where(array("user_id"=>$userId))->find();	
		
		$data=array(
			"order_ponumber"=>$order['orderPONumber'],
			"order_serial_number"=>date("YmdHis").rand(100511,987899),
			"order_time"=>time(),
			"order_prev_time"=>time(),
			"order_sure_time"=>time(),
			"order_currency"=>$order['orderCurrency'],
			"order_status"=>2,
			"order_total_price"=>$specInfo['total_price'],
			"service_fee"=>$specInfo['service_fee'],
			"user_id"=>$userId,
			"user_uid"=>D("User")->getUserUidByUserId($userId),
			"user_name"=>D("Company")->getUserNameByUserId($userId),
			"tianxin_code"=>$userInfo['tianxin_code'],
			"company_name"=>$companyInfo['company_name'],
			"order_remark"=>$order['orderRemark']
		);
		
		if(!empty($userInfo['admin_id'])){
			$data['admin_id']=$userInfo['admin_id'];
			$data['admin_name']=D("Admin")->getAdminNameByAdminId($userInfo['admin_id']);
		}
		
		return $this->add($data);
	}
	public function getTodayOrder(){
		$where=array();
		$where['order_time|order_sure_time|order_send_time|order_complete_time']=array("egt",getToday());
		$orderInfo=$this->where($where)->select();
		return $orderInfo;
	}
	public function getMonthOrder(){
		$where=array();
		$where['order_time|order_sure_time|order_send_time|order_complete_time']=array("egt",getCurrentMonth());
		$orderInfo=$this->where($where)->select();
		return $orderInfo;
	}
}