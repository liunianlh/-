<?php
namespace Home\Model;
use Think\Model;
class OrderInvoiceModel extends Model {
    public function createOrderInvoice($data,$orderId){
		$invoiceType=$data['invoiceType'];
		$invoiceContent=$data['invoiceContent']==1?"大类":"明细";
		
		$data2=array(
			"order_id"=>$orderId,
			"invoice_content"=>$invoiceContent
		);
		if($invoiceType==1){//个人开票
			$data2['invoice_name']=$data['invoiceName'];
			$data2['invoice_type']="个人";
		}else{
			$data2['invoice_credit']=$data['invoiceCredit'];
			$data2['invoice_type']="单位专用发票";
			$data2['invoice_addon']=json_encode($data['invoiceAddon']);
		}
		$orderInvoiceId=$this->add($data2);
		return $orderInvoiceId;
	}
	public function addOrderInvoice($data,$orderId){
		
		
		$data2=array(
			"order_id"=>$orderId,
			"invoice_name"=>empty($data['invoiceName'])?'':$data['invoiceName'],
			"invoice_content"=>empty($data['invoiceContent'])?'':$data['invoiceContent'],
			"invoice_type"=>empty($data['invoiceType'])?'':$data['invoiceType'],
			"invoice_credit"=>empty($data['invoiceCredit'])?'':$data['invoiceCredit']
		);
		if(empty($data['addon'])){
			$data2['invoice_addon']='';
		}else{
			$data2['invoice_addon']=json_encode($data['addon']);
		}
		$orderInvoiceId=$this->add($data2);
		return $orderInvoiceId;
	}
	public function getOrderInvoiceByOrderId($orderId){
		$orderInvoiceInfo=$this->where(array("order_id"=>$orderId))->find();
		if(isset($orderInvoiceInfo['invoice_addon'])&&!empty($orderInvoiceInfo['invoice_addon'])){
			$orderInvoiceInfo['invoice_addon']=json_decode($orderInvoiceInfo['invoice_addon'],true);
		}
		return $orderInvoiceInfo;
	}
	
	public function saveOrderInvoice($invoice='',$orderId=''){
		$where=array();
		$where['order_invoice_id']=$invoice['id'];
		
		$data=array(
			"invoice_name"=>empty($invoice['invoiceName'])?'':$invoice['invoiceName'],
			"invoice_content"=>empty($invoice['invoiceContent'])?'':$invoice['invoiceContent'],
			"invoice_type"=>empty($invoice['invoiceType'])?'':$invoice['invoiceType'],
			"invoice_credit"=>empty($invoice['invoiceCredit'])?'':$invoice['invoiceCredit']
		);
		if(empty($invoice['addon'])){
			$data['invoice_addon']='';
		}else{
			$data['invoice_addon']=json_encode($invoice['addon']);
		}
		
		if(!empty($invoice['id'])){
			$res=$this->where($where)->save($data);
		}else{
			if(!empty($invoice['invoiceName'])||!empty($invoice['invoiceCredit'])){
				$data['order_id']=$orderId;
				$res=$this->add($data);
			}
		}
		$res=$this->where($where)->save($data);
		return $res;
	}
}