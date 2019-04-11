<?php
namespace Admin\Controller;

use Think\Controller;
use Libs\PDF;

class OrderController extends BaseController
{
    public function index()
    {
        $orderModel=D("Order");
        $adminModel=D("Admin");

        $roleIds=D("GroupRole")->getRoleIdByGroupId(2);
        $adminIds=D("RoleAdmin")->getAdminIdByRoleId($roleIds);
        $adminInfos=$adminModel->getAdminInfosByAdminIds($adminIds);

        if (isset($_GET['keyword'])&&!empty($_GET['keyword'])) {
            $where['www_order.order_ponumber|www_order.order_serial_number|www_order.user_uid|www_order.user_name|www_order.admin_id']=array("like","%".$_GET['keyword']."%");
            $this->assign("keyword", $_GET['keyword']);
        } else {
            $where=setOrderWhere();
        }
        $count=M("Order")->where($where)->count();
        $page=new \Think\Page($count, 20);
        $orderInfos=$orderModel->getAllOrders($page, $where);
        foreach ($orderInfos as $key=>$value) {
            $orderInfos[$key]['company_name']=M("Company")->where(array("user_id"=>$value['user_id']))->getField("company_name");
            $orderInfos[$key]['tianxin_code']=M("User")->where(array("user_id"=>$value['user_id']))->getField("tianxin_code");
        }
        if (!IS_POST) {
            $this->assign("order_info", $orderInfos);
            $this->assign("admin_info", $adminInfos);
            $this->assign("page", $page->show());
            $this->display();
        } else {
            foreach ($orderInfos as $k=>$v) {//添加链接,以应对URL模式调整
                $orderInfos[$k]['order_time']=date("Y-m-d", $v['order_time']);
                $orderInfos[$k]['admin_name']=empty($v['admin_name'])?"--":$v['admin_name'];
                $orderInfos[$k]["url"]=U("Order/view", array('id'=>$v['order_id']));
                $orderInfos[$k]["url2"]=U("Order/printOrder", array('id'=>$v['order_id']));
                $orderInfos[$k]["url3"]=U("Order/downloadOrder", array('id'=>$v['order_id']));
                $orderInfos[$k]["url4"]=U("Order/ERPOrder", array('id'=>$v['order_id']));
            }
            exit(json_encode(array("code"=>10102,"msg"=>$orderInfos,"page"=>$page->show())));
        }
    }
    public function view()
    {
        $id=I("get.id");
        if (empty($id)) {
            exit("缺少参数");
        }
        $adminModel=D("Admin");

        $roleIds=D("GroupRole")->getRoleIdByGroupId(2);
        $adminIds=D("RoleAdmin")->getAdminIdByRoleId($roleIds);
        $adminInfos=$adminModel->getAdminInfosByAdminIds($adminIds);

        $areaInfo=D("Area")->getSpecificateAreasByLevel(2);

        $logisticsInfo=M("Order_logistics")->where(array('order_id'=>$id))->find();
        $forworderInfo=M("Order_forworder")->where(array('order_id'=>$id))->find();
        $invoiceInfo=M("Order_invoice")->where(array('order_id'=>$id))->find();
        $invoiceInfo['addon']=json_decode($invoiceInfo['invoice_addon'], true);

        $orderInfo=M("Order")->where(array('order_id'=>$id))->find();
        $orderDetailInfo=D("OrderDetail")->getOrderDetailByOrderId($id);

        $where2=array("user_id"=>$orderInfo['user_id']);
        $allLogistics=M("Logistics")->where($where2)->select();
        $allForworder=M("Forworder")->where($where2)->select();
        $allInvoice=M("Invoice")->where($where2)->select();

        $category=D("Category")->getAllSerials();
        $joint=D("Category")->getAllSmallJoints();

        $this->assign("area_info", $areaInfo);
        $this->assign("category", $category);
        $this->assign("order_detail_info", $orderDetailInfo);
        $this->assign("joint", $joint);
        $this->assign("forworder_info", $forworderInfo);
        $this->assign("invoice_info", $invoiceInfo);
        $this->assign("logistics_info", $logisticsInfo);
        $this->assign("order_info", $orderInfo);
        $this->assign("admin_info", $adminInfos);
        $this->assign("all_logistics", $allLogistics);
        $this->assign("all_invoice", $allInvoice);
        $this->assign("all_forworder", $allForworder);
        switch ($orderInfo['order_status']) {
            case 1://待处理
                session("order_wait", "order_wait");
                if ($orderInfo['order_currency']=="RMB") {
                    redirect(U("OrderWait/wait", array("id"=>$orderInfo['order_id'])));
                } elseif ($orderInfo['order_currency']=="USD") {
                    redirect(U("OrderWait/waitc", array("id"=>$orderInfo['order_id'])));
                }

                break;
            case 2://已确认，等待发货
                $logisticsCompany=D("LogisticsCompany")->getAllLogisticsCompany();
                $this->assign("logisyics_company", $logisticsCompany);
                $this->display("confirmGoods");
                break;
            case 3://已完成
                $logisticsCompany=D("LogisticsCompany")->getAllLogisticsCompany();
                $this->assign("logisyics_company", $logisticsCompany);
                $this->display();
                break;
            case 4://已取消
                $logisticsCompany=D("LogisticsCompany")->getAllLogisticsCompany();
                $this->assign("logisyics_company", $logisticsCompany);
                $this->display();
                break;
            case 5://已发货，运输中
                $logisticsCompany=D("LogisticsCompany")->getAllLogisticsCompany();
                $this->assign("logisyics_company", $logisticsCompany);
                $this->display();
                break;
            case 6://等待确认
                $this->display("waitConfirm");
                break;
        }
    }

    public function delOrder()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $orderId=I("post.id");
            $where=array("order_id"=>$orderId);
            $orderRes=M("Order")->where($where)->delete();
            $orderDetailRes=M("Order_detail")->where($where)->delete();
            $orderForworderRes=M("Order_forworder")->where($where)->delete();
            $orderInvoiceRes=M("Order_invoice")->where($where)->delete();
            $orderLogisticsRes=M("Order_logistics")->where($where)->delete();
            if ($orderRes!==false) {
                exit(json_encode(array("code"=>10511,"msg"=>"删除成功")));
            } else {
                exit(json_encode(array("code"=>10512,"msg"=>"删除失败")));
            }
        }
    }

    public function confirmGoods()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $orderId=I("post.oid");
            $lg=I("post.lg");
            $type=I("post.type");
            $where=array("order_id"=>$orderId);


            if (!empty($lg['logisticsCompany'])) {
                $lgInfo=M("Logistics_company")->where(array("logistics_company_name"=>$lg['logisticsCompany']))->find();
            }


            $data=array(
                "order_prev_time"=>time(),
                "order_status"=>5,
                "logistics_no"=>empty($lg['logisticsNo'])?'':$lg['logisticsNo'],
                // "logistics_company"=>empty($lg['logisticsCompany'])?'':$lg['logisticsCompany'],
                "logistics_time"=>convertDateToTimestamp($lg['year'], $lg['month'], $lg['day'], 0, 0, 0)
            );


            if (!empty($lgInfo)) {
                $data['logistics_company']=$lgInfo["logistics_company_name"];
                $data['logistics_company2']=$lgInfo["logistics_company_name2"];
            }

            $res=M("Order")->where($where)->save($data);

            if (empty($type)) {
                if ($res!==false) {
                    exit(json_encode(array("code"=>10511,"msg"=>"确认发货成功","url"=>U("Order/index"))));
                } else {
                    exit(json_encode(array("code"=>10512,"msg"=>"确认发货失败，请稍后重试...")));
                }
            } else {
                if ($res!==false) {
                    exit(json_encode(array("code"=>10511,"msg"=>"保存成功","url"=>U("Order/index"))));
                } else {
                    exit(json_encode(array("code"=>10512,"msg"=>"保存失败，请稍后重试...")));
                }
            }
        }
    }
    public function orderConfirm()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $orderId=I("post.oid");
            $where=array("order_id"=>$orderId);
            $data=array(
                "order_status"=>2,
                "order_sure_time"=>time(),
                "order_prev_time"=>time(),
            );
            $res=M("Order")->where($where)->save($data);
            if ($res!==false) {
                exit(json_encode(array("code"=>10511,"msg"=>"确认成功","url"=>U("Order/index"))));
            } else {
                exit(json_encode(array("code"=>10512,"msg"=>"确认失败，请稍后重试...")));
            }
        }
    }

    public function addSpec()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $dataId=I("post.dataId");
            $userId=I("post.userId");
            $oId=I("post.oId");
            $res=D("Specification")->getOneSpecInfoBySUID($dataId, $userId);
            $res2=D("OrderDetail")->addOneSpecToOrderDetail($oId, $res);
            exit(json_encode(array("code"=>10161,"msg"=>$res2)));
        }
    }

    public function saveWaiting()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $logistics=I("post.logistics");
            $forworder=I("post.forworder");
            $invoice=I("post.invoice");
            $order=I("post.order");

            D("Home/OrderLogistics")->saveOrderLogistics($logistics);
            D("Home/OrderForworder")->saveOrderForworder($forworder);
            D("Home/OrderInvoice")->saveOrderInvoice($invoice);
            D("Order")->saveOrder($order);
            exit(json_encode(array("code"=>10161,"msg"=>"保存成功","url"=>U('Order/index'))));
        }
    }
    public function saveNewOrder()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $logistics=I("post.logistics");
            $forworder=I("post.forworder");
            $invoice=I("post.invoice");
            $order=I("post.order");
            $userId=I("post.userId");

            if (isset($_SESSION['S_C'])&&!empty($_SESSION['S_C'])) {
                $verifyKey=$_SESSION['S_C'];
            } else {
                $verifyKey=md5($userId.rand(17487, 98962));//key
                session("S_C", $verifyKey);
            }
            $cache=S($verifyKey);

            $orderId=D("Order")->addOrder($order, $cache, $userId);

            $orderDetailId=D("OrderDetail")->addOrderDetail($cache, $orderId);
            D("Home/OrderLogistics")->addOrderLogistics($logistics, $orderId);
            D("Home/OrderForworder")->addOrderForworder($forworder, $orderId);
            D("Home/OrderInvoice")->addOrderInvoice($invoice, $orderId);
            S(md5($userId), null);
            exit(json_encode(array("code"=>10161,"msg"=>"保存成功")));
        }
    }
    public function createOrder()
    {
        S(session("S_C"), null);
        session("S_C", null);
        $userInfos=D("User")->getAllUser2();
        $areaInfo=D("Area")->getSpecificateAreasByLevel(2);
        $category=D("Category")->getAllSerials();
        $joint=D("Category")->getAllSmallJoints();
        $countryInfo=M("Country")->select();

        $this->assign("country_info", $countryInfo);
        $this->assign("category", $category);
        $this->assign("joint", $joint);
        $this->assign("area_info", $areaInfo);
        $this->assign("user_info", $userInfos);
        $this->display();
    }

    public function ERPOrder()
    {
        $id=I("get.id");
        $orderInfo=M("Order")->where(array('order_id'=>$id))->find();
        $orderDetail=D("OrderDetail")->getOrderDetailByOrderId($id);

        //引入文件
        Vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("A1", "型號")
                    ->setCellValue("B1", "訂單總數量");
        foreach ($orderDetail as $k => $v) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("A".($k+2), $v['model_name'])
                    ->setCellValue("B".($k+2), $v['total_number']);
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

    public function downloadOrder()
    {
        $id=I("get.id");
        $orderInfo=M("Order")->where(array('order_id'=>$id))->find();
        $orderDetail=D("OrderDetail")->getOrderDetailByOrderId($id);
        PDF::createPDF($orderInfo, $orderDetail);
    }

    //订单取消

    public function cancel()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $id=I("post.id");
            $where=array();
            $where['order_id']=$id;
            $data=array(
                "order_status"=>4
            );
            $res=M("Order")->where($where)->save($data);
            if ($res!==false) {
                exit(json_encode(array("code"=>10161,"msg"=>"订单取消成功","url"=>U('Order/index'))));
            } else {
                exit(json_encode(array("code"=>10162,"msg"=>"订单取消失败")));
            }
        }
    }
}
