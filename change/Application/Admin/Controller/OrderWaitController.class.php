<?php
namespace Admin\Controller;
use Think\Controller;
class OrderWaitController extends Controller {
    public function wait(){
		if(!IS_POST){
			orderEditClearCacheData();//清除缓存
		
			$id=I("get.id");
			$adminModel=D("Admin");
			
			$roleIds=D("GroupRole")->getRoleIdByGroupId(2);
			$adminIds=D("RoleAdmin")->getAdminIdByRoleId($roleIds);
			$adminInfos=$adminModel->getAdminInfosByAdminIds($adminIds);
			
			$countryInfo=M("Country")->select();
			
			$logisticsInfo=M("Order_logistics")->where(array('order_id'=>$id))->find();
			$forworderInfo=M("Order_forworder")->where(array('order_id'=>$id))->find();
			$invoiceInfo=M("Order_invoice")->where(array('order_id'=>$id))->find();
			$invoiceInfo['addon']=json_decode($invoiceInfo['invoice_addon'],true);
			
			
			$orderInfo=M("Order")->where(array('order_id'=>$id))->find();
			$orderDetailInfo=D("OrderDetail")->getOrderDetailByOrderId($id);
			
			if($orderInfo['order_currency']=="USD"){
				$orderInfo['order_total_price']=USD2RMB($orderInfo['order_total_price']);
				$orderInfo['service_fee']=USD2RMB($orderInfo['service_fee']);
				
				foreach($orderDetailInfo as $key=>$value){
					$orderDetailInfo[$key]['price']=USD2RMB($value['price']);
					$orderDetailInfo[$key]['amount']=USD2RMB($value['amount']);
				}
			}
			$orderInfo['order_total_price2']=$orderInfo['order_total_price']+$orderInfo['service_fee'];
			
			$where2=array("user_id"=>$orderInfo['user_id']);
			$allLogistics=M("Logistics")->where($where2)->select();
			$allForworder=M("Forworder")->where($where2)->select();
			$allInvoice=M("Invoice")->where($where2)->select();
			
			$category=D("Category")->getAllSerials();
			$joint=D("Category")->getAllSmallJoints();
			
			orderEditCacheData($orderInfo,$orderDetailInfo,$userId);//缓存数据
			
			$this->assign("country_info",$countryInfo);
			$this->assign("category",$category);
			$this->assign("order_detail_info",$orderDetailInfo);
			$this->assign("joint",$joint);
			$this->assign("forworder_info",$forworderInfo);
			$this->assign("invoice_info",$invoiceInfo);
			$this->assign("logistics_info",$logisticsInfo);
			$this->assign("order_info",$orderInfo);
			$this->assign("admin_info",$adminInfos);
			$this->assign("all_logistics",$allLogistics);
			$this->assign("all_invoice",$allInvoice);
			$this->assign("all_forworder",$allForworder);
			$this->display();
		}else{
			$logistics=I("post.logistics");
			$forworder=I("post.forworder");
			$invoice=I("post.invoice");
			$order=I("post.order");
			
			D("Home/OrderLogistics")->saveOrderLogistics($logistics);
			D("Home/OrderForworder")->saveOrderForworder($forworder);
			D("Home/OrderInvoice")->saveOrderInvoice($invoice);
			// D("Order")->saveOrder($order);
			
			$cache=orderEditGetCacheData();//缓存数据
			$where['order_id']=$order['id'];
			$data=array(
				"order_prev_time"=>time(),
				"order_status"=>6,//等待确认
				"admin_id"=>$order['operator'],
				"admin_name"=>D("Admin")->getAdminNameByAdminId($order['operator']),
				"order_currency"=>$order['currency'],
				"order_total_price"=>$cache['order']['order_total_price'],
				"service_fee"=>$cache['order']['service_fee'],
			);
			$res=M("Order")->where($where)->save($data);
			
			$orderDetailInfo=$cache['detail'];
			$orderDetailM=M("Order_detail");
			foreach($orderDetailInfo as $key=>$value){
				$data2=array(
					"order_id"=>$order['id'],
					"specification_id"=>$value['specification_id'],
					"model_name"=>$value['model_name'],
					"products_img"=>$value['products_img'],
					"products_chinese_name"=>$value['products_chinese_name'],
					"length"=>$value['length'],
					"color_name"=>$value['color_name'],
					"loading"=>$value['loading'],
					"buy_number"=>1,
					"total_number"=>$value['total_number'],
					"price"=>$value['price'],
					"amount"=>$value['amount']
				);
				if(!empty($value['order_detail_id'])){
					unset($orderDetailIds[md5($value['order_detail_id'])]);
					$orderDetailM->where(array('order_detail_id'=>$value['order_detail_id']))->save($data2);
				}else{
					$orderDetailM->add($data2);
				}
			}
			$count=M("Order_detail")->where($where)->count();
			if($count<=0){
				M("Order")->where($where)->delete();
			}
			orderEditClearCacheData();//清除缓存
			exit(json_encode(array("code"=>10161,"msg"=>"保存成功","url"=>U('Order/index'))));
		}
	}
	
	public function waitc(){
		if(!IS_POST){
			orderEditClearCacheData();//清除缓存
		
			$id=I("get.id");
			$adminModel=D("Admin");
			
			$roleIds=D("GroupRole")->getRoleIdByGroupId(2);
			$adminIds=D("RoleAdmin")->getAdminIdByRoleId($roleIds);
			$adminInfos=$adminModel->getAdminInfosByAdminIds($adminIds);
			
			$countryInfo=M("Country")->select();
			
			$logisticsInfo=M("Order_logistics")->where(array('order_id'=>$id))->find();
			$forworderInfo=M("Order_forworder")->where(array('order_id'=>$id))->find();
			$invoiceInfo=M("Order_invoice")->where(array('order_id'=>$id))->find();
			$invoiceInfo['addon']=json_decode($invoiceInfo['invoice_addon'],true);
			
			$orderInfo=M("Order")->where(array('order_id'=>$id))->find();
			$orderDetailInfo=D("OrderDetail")->getOrderDetailByOrderId($id);
			
			if($orderInfo['order_currency']=="RMB"){
				$orderInfo['order_total_price']=RMB2USD($orderInfo['order_total_price']);
				$orderInfo['service_fee']=RMB2USD($orderInfo['service_fee']);
				
				foreach($orderDetailInfo as $key=>$value){
					$orderDetailInfo[$key]['price']=RMB2USD($value['price']);
					$orderDetailInfo[$key]['amount']=RMB2USD($value['amount']);
				}
			}
			$orderInfo['order_total_price2']=$orderInfo['order_total_price']+$orderInfo['service_fee'];
			
			$where2=array("user_id"=>$orderInfo['user_id']);
			$allLogistics=M("Logistics")->where($where2)->select();
			$allForworder=M("Forworder")->where($where2)->select();
			$allInvoice=M("Invoice")->where($where2)->select();
			
			$category=D("Category")->getAllSerials();
			$joint=D("Category")->getAllSmallJoints();
			
			orderEditCacheData($orderInfo,$orderDetailInfo,$userId);//缓存数据
			
			$this->assign("country_info",$countryInfo);
			$this->assign("category",$category);
			$this->assign("order_detail_info",$orderDetailInfo);
			$this->assign("joint",$joint);
			$this->assign("forworder_info",$forworderInfo);
			$this->assign("invoice_info",$invoiceInfo);
			$this->assign("logistics_info",$logisticsInfo);
			$this->assign("order_info",$orderInfo);
			$this->assign("admin_info",$adminInfos);
			$this->assign("all_logistics",$allLogistics);
			$this->assign("all_invoice",$allInvoice);
			$this->assign("all_forworder",$allForworder);
			$this->display();
		}else{
			$logistics=I("post.logistics");
			$forworder=I("post.forworder");
			$invoice=I("post.invoice");
			$order=I("post.order");
			
			D("Home/OrderLogistics")->saveOrderLogistics($logistics,$order['id']);
			D("Home/OrderForworder")->saveOrderForworder($forworder,$order['id']);
			D("Home/OrderInvoice")->saveOrderInvoice($invoice,$order['id']);
			// D("Order")->saveOrder($order);
			
			$cache=orderEditGetCacheData();//缓存数据
			$where['order_id']=$order['id'];
			$data=array(
				"order_prev_time"=>time(),
				"order_status"=>6,//等待确认
				"admin_id"=>$order['operator'],
				"admin_name"=>D("Admin")->getAdminNameByAdminId($order['operator']),
				"order_currency"=>$order['currency'],
				"order_total_price"=>$cache['order']['order_total_price'],
				"service_fee"=>$cache['order']['service_fee'],
			);
			$salesItem=$order['salesItem'];
			if(!empty($salesItem)){
				if($salesItem==1){
					$data['sales_terms']="FOB Shen Zhen";
				}
				if($salesItem==2){
					$data['sales_terms']="FOB Hong Kong";
				}
			}
			$res=M("Order")->where($where)->save($data);
			
			$orderDetailIds=array();
			$orderDetailId=M("OrderDetail")->where($where)->select();
			foreach($orderDetailId as $key=>$value){
				$orderDetailIds[md5($value['order_detail_id'])]=$value['order_detail_id'];
			}
			
			$orderDetailInfo=$cache['detail'];
			$orderDetailM=M("Order_detail");
			foreach($orderDetailInfo as $key=>$value){
				$data2=array(
					"order_id"=>$order['id'],
					"specification_id"=>$value['specification_id'],
					"model_name"=>$value['model_name'],
					"products_img"=>$value['products_img'],
					"products_chinese_name"=>$value['products_chinese_name'],
					"length"=>$value['length'],
					"color_name"=>$value['color_name'],
					"loading"=>$value['loading'],
					"buy_number"=>1,
					"total_number"=>$value['total_number'],
					"price"=>$value['price'],
					"amount"=>$value['amount']
				);
				if(!empty($value['order_detail_id'])){
					unset($orderDetailIds[md5($value['order_detail_id'])]);
					$orderDetailM->where(array('order_detail_id'=>$value['order_detail_id']))->save($data2);
				}else{
					$orderDetailM->add($data2);
				}
			}
			if(!empty($orderDetailIds)){
				M("Order_detail")->where(array("order_detail_id"=>array("in",implode(",",$orderDetailIds))))->delete();
			}
			$count=M("Order_detail")->where($where)->count();
			if($count<=0){
				M("Order")->where($where)->delete();
			}
			orderEditClearCacheData();//清除缓存
			exit(json_encode(array("code"=>10161,"msg"=>"保存成功","url"=>U('Order/index'))));
		}
	}
}