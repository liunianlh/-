<?php
namespace Admin\Controller;
use Think\Controller;
class OrderOther2Controller extends Controller {
	public function getLogistics(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$logistics=M("Logistics")->where(array("logistics_id"=>$dataId))->find();
			exit(json_encode(array("code"=>10161,"msg"=>$logistics)));
		}
	}
	public function getForworder(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$forworder=M("Forworder")->where(array("forworder_id"=>$dataId))->find();
			exit(json_encode(array("code"=>10161,"msg"=>$forworder)));
		}
	}
	public function getInvoice(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$invoice=M("Invoice")->where(array("invoice_id"=>$dataId))->find();
			if($invoice['invoice_type_id']==2){
				$invoice['invoice_addon']=json_decode($invoice['invoice_addon'],true);
			}
			exit(json_encode(array("code"=>10161,"msg"=>$invoice)));
		}
	}
	public function calculatePriceByNumber(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$userId=I("post.userId");
			$number=I("post.number",0,'intval');
			if($number<=0){
				$number=0;
			}
			$cache=orderEditGetCacheData();
			$index=$this->getOrderDetailBySpecId($cache['detail'],$dataId);
			$orderDetail=$cache['detail'][$index];
			$orderDetail['total_number']=$number;
			$orderDetail['amount']=sprintf("%.2f",$number*$orderDetail['price']);
			$cache['detail'][$index]=$orderDetail;
			$res['amount']=$orderDetail['amount'];
			$res['total_price']=$this->calcTotalPrice($cache['detail']);
			$cache['order']['order_total_price']=$res['total_price'];
			$cache['order']['order_total_price2']=$cache['order']['order_total_price']+$cache['order']['service_fee'];
			orderEditCacheData($cache['order'],$cache['detail'],$userId);
			
			$res['total_price']=$res['total_price']+$cache['order']['service_fee'];
			$res['number']=$number;
			exit(json_encode(array("code"=>10161,"msg"=>$res)));
		}
	}
	public function calculatePriceByPrice(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$userId=I("post.userId");
			$price=I("post.price",0,'floatval');
			if($price<=0){
				$price=0;
			}
			$cache=orderEditGetCacheData();
			$index=$this->getOrderDetailBySpecId($cache['detail'],$dataId);
			$orderDetail=$cache['detail'][$index];
			$orderDetail['price']=$orderDetail['rmb']=$price;
			$orderDetail['amount']=sprintf("%.2f",$price*$orderDetail['total_number']);
			$cache['detail'][$index]=$orderDetail;
			$res['amount']=$orderDetail['amount'];
			$res['total_price']=$this->calcTotalPrice($cache['detail']);
			$cache['order']['order_total_price']=$res['total_price'];
			$cache['order']['order_total_price2']=$cache['order']['order_total_price']+$cache['order']['service_fee'];
			orderEditCacheData($cache['order'],$cache['detail'],$userId);
			
			$res['total_price']=$res['total_price']+$cache['order']['service_fee'];
			$res['price']=$price;
			exit(json_encode(array("code"=>10161,"msg"=>$res)));
		}
	}
	public function calcServicePrice(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$userId=I("post.userId");
			$price=I("post.price",0,'floatval');
			if($price<=0){
				$price=0;
			}
			$res=array();
			$cache=orderEditGetCacheData();
			$cache['order']['service_fee']=$price;
			$res['total_price']=$this->calcTotalPrice($cache['detail']);
			$cache['order']['order_total_price']=$res['total_price'];
			$cache['order']['order_total_price2']=$cache['order']['order_total_price']+$cache['order']['service_fee'];
			orderEditCacheData($cache['order'],$cache['detail'],$userId);
			
			$res['total_price']=number_format($res['total_price']+$cache['order']['service_fee'],2);
			$res['price']=sprintf("%.2f",$price);
			exit(json_encode(array("code"=>10161,"msg"=>'???'.$res)));
		}
	}
	public function addSpec(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$userId=I("post.userId");
			$oId=I("post.oId");
			$res=D("Specification")->getOneSpecInfoBySUID($dataId,$userId);
			$res['total_number']=$res['loading'];
			$res['rmb']=$res['price']=RMB2USD($res['rmb']);
			$res['amount']=$res['loading']*$res['price'];
			$cache=orderEditGetCacheData();
			$cache['detail'][]=$res;
			$res['total_price']=$this->calcTotalPrice($cache['detail']);
			$cache['order']['order_total_price']=$res['total_price'];
			$cache['order']['order_total_price2']=$cache['order']['order_total_price']+$cache['order']['service_fee'];
			orderEditCacheData($cache['order'],$cache['detail'],$userId);
			$res['total_price']=sprintf("%.2f",$res['total_price']+$cache['order']['service_fee']);
			exit(json_encode(array("code"=>10161,"msg"=>$res)));
		}
	}
	
	public function deleteProduct(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$userId=I("post.userId");
			
			$cache=orderEditGetCacheData();//1.获取缓存
			
			if(!empty($cache)){			   //2.取出对应规格的数据
				$orderDetailInfo=$cache['detail'];
				foreach($orderDetailInfo as $key=>$value){
					if($dataId==$value['specification_id']){
						unset($orderDetailInfo[$key]);//3. 删除数据
						break;
					}
				}
				$cache['detail']=$orderDetailInfo;
			}
			
			$cache['order']['order_total_price']=$this->calcTotalPrice($cache['detail']);//重计总价
			$cache['order']['order_total_price2']=$cache['order']['order_total_price']+$cache['order']['service_fee'];
			orderEditCacheData($cache['order'],$cache['detail'],$userId);//更新缓存
			exit(json_encode(array("code"=>10161,"msg"=>$cache)));
		}
	}
	
	public function loadProductByUID(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$userId=I("post.userId");
			$cache=orderEditGetCacheData();
			$specIds=array();
			if(!empty($cache)){
				$orderDetailInfo=$cache['detail'];
				foreach($orderDetailInfo as $key=>$value){
					$specIds[]=$value['specification_id'];
				}
			}
			$res=$this->getProductInfo($userId);
			$res['specIds']=$specIds;
			exit(json_encode(array("code"=>10161,"msg"=>$res)));
		}
	}
	public function getProductInfo($userId){
		$specInfo=D("Specification","Logic")->getAllSpecification($userId);
		$count=count($specInfo);
		$page=new \Think\Page($count,4);
		if(!isset($_GET['p'])||empty($_GET['p'])){
			$curPage=1;
		}else{
			$curPage=$_GET['p'];
		}
		$specInfo=array_slice($specInfo,4*($curPage-1),4);
		return array("spec_info"=>$specInfo,"page"=>$page->show());
	}
	private function calcTotalPrice($orderDetail){
		$total_price=0.00;
		foreach($orderDetail as $key=>$value){
			$total_price+=$value['amount'];
		}
		return sprintf("%.2f",$total_price);
	}
	private function getOrderDetailBySpecId($orderDetailInfo,$specId){
		$index=-1;
		foreach($orderDetailInfo as $key=>$value){
			if($value['specification_id']==$specId){
				$index=$key;
				break;
			}
		}
		return $index;
	}
}