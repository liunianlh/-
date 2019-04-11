<?php
namespace Admin\Controller;
use Think\Controller;
class PrintController extends Controller {
    public function orderPrint(){
		$id=I("get.id");
		$adminModel=D("Admin");
		
		$roleIds=D("GroupRole")->getRoleIdByGroupId(2);
		$adminIds=D("RoleAdmin")->getAdminIdByRoleId($roleIds);
		$adminInfos=$adminModel->getAdminInfosByAdminIds($adminIds);
		
		$areaInfo=D("Area")->getSpecificateAreasByLevel(2);
		
		$logisticsInfo=M("Order_logistics")->where(array('order_id'=>$id))->find();
		$forworderInfo=M("Order_forworder")->where(array('order_id'=>$id))->find();
		$invoiceInfo=M("Order_invoice")->where(array('order_id'=>$id))->find();
		$invoiceInfo['addon']=json_decode($invoiceInfo['addon'],true);
		
		$orderInfo=M("Order")->where(array('order_id'=>$id))->find();
		$orderDetailInfo=D("OrderDetail")->getOrderDetailByOrderId($id);
		
		$where2=array("user_id"=>$orderInfo['user_id']);
		$allLogistics=M("Logistics")->where($where2)->select();
		$allForworder=M("Forworder")->where($where2)->select();
		$allInvoice=M("Invoice")->where($where2)->select();
		
		$category=D("Category")->getAllSerials();
		$joint=D("Category")->getAllSmallJoints();
		
		
		$specIds=array();
		foreach($orderDetailInfo as $key=>$value){
			$specIds[]=$value['specification_id'];
		}
		$logisticsCompany=D("LogisticsCompany")->getAllLogisticsCompany();
		$this->assign("area_info",$areaInfo);
		$this->assign("spec_ids",$specIds);
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
		$this->assign("logisyics_company",$logisticsCompany);
		$this->display();
	}
}