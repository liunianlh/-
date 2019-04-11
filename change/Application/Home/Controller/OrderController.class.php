<?php
namespace Home\Controller;
use Libs\PDF;
class OrderController extends BaseController {
    public function index(){
		$where=setOrderWhere();
		$orderModel=D("Admin/Order");
		$count=$orderModel->where($where)->count();
		$page=new \Think\Page($count,20);
		$orderInfos=$orderModel->getAllOrders($page,$where);
		if(!IS_POST){
			$this->assign("order_info",$orderInfos);
			$this->assign("page",$page->show());
			$this->display();
		}else{
			foreach($orderInfos as $k=>$v){//添加链接,以应对URL模式调整
				$orderInfos[$k]['order_time']=date("Y-m-d",$v['order_time']);
				$orderInfos[$k]['admin_name']=empty($v['admin_name'])?"--":$v['admin_name'];
				$orderInfos[$k]["url"]=U("Order/view",array('id'=>$v['order_id']));
				$orderInfos[$k]["url2"]=U("Order/downloadOrder",array('id'=>$v['order_id']));
			}
			exit(json_encode(array("code"=>10102,"msg"=>$orderInfos,"page"=>$page->show())));
		}
	}
	public function view(){
		$id=I("get.id");
		$orderInfo=D("Order")->getOrderInfoByOrderId($id);
		$orderDetailInfo=D("OrderDetail")->getOrderDetailInfoByOrderId($id);
		$orderLogisticsInfo=D("OrderLogistics")->getOrderLogisticsByOrderId($id);
		$orderForworderInfo=D("OrderForworder")->getOrderForworderByOrderId($id);
		$orderInvoiceInfo=D("OrderInvoice")->getOrderInvoiceByOrderId($id);
		$this->assign("order_info",$orderInfo);
		$this->assign("order_detail_info",$orderDetailInfo);
		$this->assign("order_logistics_info",$orderLogisticsInfo);
		$this->assign("order_forworder_info",$orderForworderInfo);
		$this->assign("order_invoice_info",$orderInvoiceInfo);
		$this->display();
	}
	
	public function ERPOrder(){
		$id=I("get.id");
		$orderInfo=M("Order")->where(array('order_id'=>$id))->find();
		$orderDetail=M("OrderDetail")->where(array('order_id'=>$id))->select();
		
		//引入文件
	    Vendor('PHPExcel.PHPExcel');
	    $objPHPExcel = new \PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A1","型號")
					->setCellValue("B1","訂單總數量");
		foreach($orderDetail as $k => $v){
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A".($k+2),$v['model_name'])
					->setCellValue("B".($k+2),$v['total_number']);
        }
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$orderInfo['order_serial_number'].'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
	
	public function downloadOrder(){
		$id=I("get.id");
		$orderInfo=M("Order")->where(array('order_id'=>$id))->find();
		$orderDetail=M("OrderDetail")->where(array('order_id'=>$id))->select();
		PDF::createPDF2($orderInfo,$orderDetail);
	}
}