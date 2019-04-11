<?php
namespace Home\Controller;

use Libs\PinYin;

class CartController extends BaseController
{
    public function ceshi(){
        return phpinfo();
    }

    public function send_mail_by_smtp($address, $subject, $body, $file = '')
    {
//        require('./PHPMailer-master/Exception.php');
//        require('./PHPMailer-master/PHPMailer.php');
//        require('./PHPMailer-master/SMTP.php');
        //date_default_timezone_set("Asia/Shanghai");//设定时区东八区
        Vendor("PHPMailer-master.class#Exception");
        Vendor("PHPMailer-master.class#PHPMailer");
        Vendor("PHPMailer-master.class#SMTP");

        $email_config=M("Email_setting")
            ->order("email_setting_id desc")
            ->limit(1)
            ->find();

        $mail=new \PHPMailer();
        //Server settings
        $mail->SMTPDebug = 2;
        $mail->isSMTP();                                      // 使用SMTP方式发送
        $mail->Host =$email_config['email_host'];                         // SMTP邮箱域名
        $mail->SMTPAuth = true;                               // 启用SMTP验证功能
        $mail->Username = $email_config['email_user'];                   // 邮箱用户名(完整email地址)
        $mail->Password =$email_config['email_pwd'];
        // smtp授权码，非邮箱登录密码
        $mail->SMTPSecure = 'ssl';
        $mail->Port =$email_config['email_port'];
        $mail->CharSet = "utf-8";                             //设置字符集编码 "GB2312"
        // 设置发件人信息，显示为  你看我那里像好人(xxxx@126.com)
        $mail->setFrom($mail->Username, 'Tonetron ');

        //设置收件人 参数1为收件人邮箱 参数2为该收件人设置的昵称  添加多个收件人 多次调用即可
        //$mail->addAddress('********@163.com', '你看我那里像好人');

        if (is_array($address)) {
            foreach ($address as $item) {
                if (is_array($item)) {
                    $mail->addAddress($item['address'], $item['nickname']);
                } else {
                    $mail->addAddress($item);
                }
            }
        } else {
            $mail->addAddress($address, 'adsf');
        }


        if ($file !== '') $mail->AddAttachment($file); // 添加附件

        $mail->isHTML(true);    //邮件正文是否为html编码 true或false
        $mail->Subject = $subject;     //邮件主题
        $mail->Body = $body;           //邮件正文 若isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取的html文件
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  //附加信息，可以省略

        return $mail->Send() ? true : 'ErrorInfo:' . $mail->ErrorInfo;
    }

    public function ERPOrder(){
//        $id=I("get.id");
        $id=1091;
        $orderInfo=M("Order")->where(array('order_id'=>$id))->find();//订单信息
        $orderdizhi=M("Order_logistics")->where(array('order_id'=>$id))->find();//收货地址
        $orderDetail=M("OrderDetail")->where(array('order_id'=>$id))->select();//货物信息
        $useremail=M("User")->where(array('user_id'=>$orderInfo['user_id']))->getField('user_email');//客户邮箱
        $admin_email=M("admin")->where(array('admin_id'=> $orderInfo["admin_id"]))->getField('admin_email');//管理员邮箱
        //引入文件
        Vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1","订单信息")
            ->setCellValue("A2","PO号码")
            ->setCellValue("A3","流水单号")
            ->setCellValue("A4","下单时间")
            ->setCellValue("A5","确认时间")
            ->setCellValue("A6","完成时间")
            ->setCellValue("A7","币种")
            ->setCellValue("A8","业务员")
            ->setCellValue("A9","销售条款");

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("B2",$orderInfo['order_ponumber'])
            ->setCellValue("B3",$orderInfo['order_serial_number'])
            ->setCellValue("B4",$orderInfo['order_time'])
            ->setCellValue("B5",$orderInfo['order_sure_time'])
            ->setCellValue("B6",$orderInfo['order_complete_time'])
            ->setCellValue("B7",$orderInfo['order_currency'])
            ->setCellValue("B8",$orderInfo['admin_name'])
            ->setCellValue("B9",$orderInfo['sales_terms']);


        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("D1","物流信息")
            ->setCellValue("D2","公司名称")
            ->setCellValue("D3","收件人")
            ->setCellValue("D4","收件人电话")
            ->setCellValue("D5","收货地址");



        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("E2",$orderdizhi['logistics_company_name'])
            ->setCellValue("E3",$orderdizhi['logistics_receiver'])
            ->setCellValue("E4",$orderdizhi['logistics_receiver_phone'])
            ->setCellValue("E5",$orderdizhi['logistics_address']);





        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A11","型号")
            ->setCellValue("B11","产品描述")
            ->setCellValue("C11","长度")
            ->setCellValue("D11","颜色")
            ->setCellValue("E11","装箱数量")
            ->setCellValue("F11","购买总数量")
            ->setCellValue("G11","单价")
            ->setCellValue("H11","小计");

//订单信息
//物流信息
//Forworder In.
// 开票信息
        foreach($orderDetail as $k => $v){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A".($k+12),$v['model_name'])
                ->setCellValue("B".($k+12),$v['products_chinese_name'])
                ->setCellValue("C".($k+12),$v['length'])
                ->setCellValue("D".($k+12),$v['color_name'])
                ->setCellValue("E".($k+12),$v['loading'])
                ->setCellValue("F".($k+12),$v['total_number'])
                ->setCellValue("G".($k+12),$v['price'])
                ->setCellValue("H".($k+12),$v['amount']);
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);


//
//        $stylearray=array(
//        'borders'=>array(
//            'outline'=>array(
//                'style'=>\PHPExcel_Style_Border::BORDER_THICK,//边框是粗的
//            ),
//        ),
//        );
//        $objPHPExcel->getActiveSheet()->getStyle('A1:B9')->applyFromArray($stylearray);



        $objPHPExcel->getActiveSheet()->getStyle('A1:H30')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//文字居中

        $user_path=$_SERVER['DOCUMENT_ROOT']."/Public/excel/";
        $filename = iconv("utf-8","gb2312",$orderInfo['order_serial_number']);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        header('Cache-Control: max-age=0');
//        $objWriter->save('php://output');
        $objWriter->save($user_path.$filename.'.xls');
        $dizhi=$user_path.$filename.'.xls';
        $ret = $this->send_mail_by_smtp("$admin_email", '用户订单', '有新订单！！！', $dizhi);
        $tel = $this->send_mail_by_smtp("$useremail", '用户订单', '有新订单！！！', $dizhi);
        unlink($dizhi);
    }









    public function fob(){
        $fobmoney=M("fob")->where("id=3")->find();
        return $fobmoney["name"];
    }

    public function index()
    {

        if (!IS_POST) {
            $carts=session("cart");
            $userId=session("userid");
            $invoiceInfo=D("Admin/Invoice")->getAllInvoicesByUserId($_SESSION['userid']);
            $forworderInfo=D("Admin/Forworder")->getAllForworderByUserId($_SESSION['userid']);
            $userInfo=D("User")->getUserInfoByUserId($_SESSION['userid']);
            $shipmentInfo=D("Admin/Shipment")->getAllShipment();
            $specInfo=array();
            if (is_array($carts)) {
                $specInfo=D("Specification")->getWholeSpecificationInfo($carts, $userId);
                $cartDetail=array();

                if (defaultCurrency()=="USD") {
                    $defaultCurrency="USD";
                } else {
                    $defaultCurrency="RMB";
                }
                $this->assign("defaultCurrency", $defaultCurrency);

                foreach ($specInfo as $key=>$value) {
                    $data=array();
                    if (!empty($value['spec_img'])) {
                        $data['pic']=$value['spec_img'];
                    } else {
                        $data['pic']=$value['product']['products_img'];
                    }


                    $data['modelName']=$value['model_name'];
                    $data['specification']=$value['product']['products_chinese_name'];
                    $data['specification2']=$value['product']['products_english_name'];
                    $data['length']=$value['length'];
                    $data['colorName']=$value['color_name'];
                    $data['colorName2']=$value['color_name2'];
                    $data['loading']=$value['loading'];
                    $data['inventory']=$value['inventory'];
                    $data['rmb']=$value['rmb'];
                    $data['specification_id']=$value['specification_id'];
                    $data['buyNum']=1;

                    if ($defaultCurrency=="USD") {// 改变显示  币种

                        $specInfo[$key]['rmb']=RMB2USD($value['rmb']);


                        $specInfo[$key]['subtotal']=$data['subtotal']=$data['buyNum']*$data['loading']*RMB2USD($value['rmb']);



                    } else {


                        $specInfo[$key]['subtotal']=$data['subtotal']=$data['buyNum']*$data['loading']*$value['rmb'];


                    }
                    $cartDetail[md5($value['specification_id'])]=$data;
                }
                session("cartDetail", $cartDetail);

                $totalPrice=D("Products", "Logic")->calcCartTotalPrice($cartDetail);

                if($userInfo["discount"]==0){
                    $GradeInfo=M("Grade")->where(array("grade_id"=>$userInfo['grade_id']))->find();
                    $discount=(100-intval($GradeInfo["gr_discount"]))*0.01;
                    if($this->rangeTime($GradeInfo["gr_time"],$GradeInfo["gr_endtime"])){
                        $totalPrice=$totalPrice*$discount;
                    }
                }else{
                    $discount=(100-intval($userInfo["discount"]))*0.01;
                    if($this->rangeTime($userInfo["start_time"],$userInfo["end_time"])){
                        $totalPrice=$totalPrice*$discount;
                    }
                }



                session("totalPrice", $totalPrice);
                // 如果显示的是  RMB，就是是  RMB，
                // 如果显示的是  USD，就是是  USD，
                $this->assign("totalPrice", $totalPrice);
            }
            $configInfo=M("Config")->where(array("config_name"=>"warn_set"))->find();
            if ($this->langFlag==1) {
                $config_value=$configInfo["config_value"];
            } elseif ($this->langFlag==2) {
                $config_value=$configInfo["config_value2"];
            }

            $countryInfo=D("ACH/Country")->getAllCountry();


            $fobmoney=M("fob")->where("id=3")->find();
            $this->assign("fobmoney", $fobmoney["name"]);

            $logisticsInfo=D("Logistics")->getLogisticsInfoByUserId($_SESSION['userid']);
            $this->assign("logistics_info", $logisticsInfo);
            $this->assign("config_value", $config_value);
            $this->assign("country_info", $countryInfo);
            $this->assign("language", $this->langFlag);
            $this->assign("specInfo", $specInfo);
            $this->assign("invoice_info", $invoiceInfo);
            $this->assign("forworder_info", $forworderInfo);
            $this->assign("shipment_info", $shipmentInfo);
            $this->assign("user_Info", $userInfo);
            $this->display();
        } else {
            $productsData=I("post.productsData");
            if ($productsData["logisticsInfo"]["country"]==1) {
                $productsData["logisticsInfo"]["prov2"]=M("Area")->where(array("area_id"=>$productsData["logisticsInfo"]["prov"]))->find();
                $productsData["logisticsInfo"]["city2"]=M("Area")->where(array("area_id"=>$productsData["logisticsInfo"]["city"]))->find();
            }

            if (!empty($productsData["forworderInfo"])) {
                if ($productsData["forworderInfo"]["fwdCountry"]==1) {
                    $productsData["forworderInfo"]["fwdProv2"]=M("Area")->where(array("area_id"=>$productsData["forworderInfo"]["fwdProv"]))->find();
                    $productsData["forworderInfo"]["fwdCity2"]=M("Area")->where(array("area_id"=>$productsData["forworderInfo"]["fwdCity"]))->find();
                }
                $productsData["forworderInfo"]["fwdCountry2"]=M("Country")->where(array("country_id"=>$productsData["forworderInfo"]["fwdCountry"]))->find();
            }
            $productsData["logisticsInfo"]["country2"]=M("Country")->where(array("country_id"=>$productsData["logisticsInfo"]["country"]))->find();


            $cartDetail=session("cartDetail");
            if (!is_array($cartDetail)) {//阻止非法请求
                exit(json_encode(array("code"=>10051,"info"=>"非法请求")));
            }
            foreach ($cartDetail as $key=>$value) {
                if ($value['buyNum']<1) {
                    unset($cartDetail[$key]);//删除购买量为0的数据记录
                }
            }
            $totalPrice=D("Products", "Logic")->calcCartTotalPrice($cartDetail);

//            折扣
            $userInfo=D("User")->getUserInfoByUserId($_SESSION['userid']);
            if($userInfo["discount"]==0){
                $GradeInfo=M("Grade")->where(array("grade_id"=>$userInfo['grade_id']))->find();
                $discount=(100-intval($GradeInfo["gr_discount"]))*0.01;
                if($this->rangeTime($GradeInfo["gr_time"],$GradeInfo["gr_endtime"])){
                    $totalPrice=$totalPrice*$discount;
                }
            }else{
                $discount=(100-intval($userInfo["discount"]))*0.01;
                if($this->rangeTime($userInfo["start_time"],$userInfo["end_time"])){
                    $totalPrice=$totalPrice*$discount;
                }
            }


            $data=array(
                "productsData"=>$productsData,
                "cartDetail"=>$cartDetail,
                "totalPrice"=>$totalPrice,
                "orderSerialNumber"=>date("YmdHis").rand(100511, 987899),
                "dealTime"=>time()
            );
            S(md5($_SESSION['userid']), $data, 6*3600);//缓存6个小时
            exit(json_encode(array("code"=>10029,"info"=>"提交成功","url"=>U('Cart/sureCart'))));
        }
    }


    public function sureCart()
    {

        if (!IS_POST) {
            $data=S(md5($_SESSION['userid']));
            $phone=$data["productsData"]["logisticsInfo"]["receiverPhone"];
            $email=$data["productsData"]["logisticsInfo"]["receiverEmail"];
            if (empty($data)) {
                exit("错误");
            }else if(!preg_match("/^1[34578]\d{9}$/",$phone)){
                $this->error('手机号错误');
            }else if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/",$email)){
                $this->error('邮箱错误');
            }else{
                $data["fobmoney"]=$this->fob();
                $this->assign("data", $data);
                $this->assign("language", $this->langFlag);
                $this->display();
            }
        } else {
            $data=S(md5($_SESSION['userid']));
            if (empty($data)) {
                exit(json_encode(array("code"=>10051,"info"=>"非法请求")));
            }else {
                $productsData=$data['productsData'];
                $cartDetail=$data['cartDetail'];
                $userId=$_SESSION['userid'];

                $orderId=D("Order")->createOrder($data, $userId);



                if (isset($productsData['invoiceInfo'])&&!empty($productsData['invoiceInfo'])) {
                    $orderInvoiceId=D("OrderInvoice")->createOrderInvoice($productsData['invoiceInfo'], $orderId);
                }
                if (isset($productsData['forworderInfo'])&&!empty($productsData['forworderInfo'])) {
                    $orderForworderId=D("OrderForworder")->createOrderForworder($productsData['forworderInfo'], $orderId);
                }



                $orderLogisticsId=D("OrderLogistics")->createOrderLogistics($productsData['logisticsInfo'], $orderId);
                $OrderDetailIds=D("OrderDetail")->createOrderDetail($cartDetail, $orderId, $productsData["logisticsInfo"]["country"]);




//                $this->ERPOrder($orderId);
                if ($orderId) {
                    M("User")->where(array("user_id"=>$_SESSION['userid']))->save(array("order_time"=>time()));
                    S(md5($_SESSION['userid']), null);//删除缓存
                    session("cartDetail", null);//删除session
                    session("cart", null);
                    session("totalPrice", null);
                    $orderIdMD5=substr(md5($orderId), 6, 6).str_replace("=", '', base64_encode($orderId));
                    exit(json_encode(array("code"=>10029,"info"=>"提交成功","url"=>U('Cart/orderSuccess', array("orderId"=>md5($orderIdMD5).":".$orderIdMD5)))));
                }
            }
        }
    }



//    订单完成
    public function orderSuccess()
    {
        if (!IS_POST) {
            $orderId=I("get.orderId");
            $orderId=explode(":", $orderId);
            $orderId=base64_decode(substr($orderId[1], 6));
            $orderInfo=M("Order")->where(array("order_id"=>$orderId))->find();
            $this->assign("order_info", $orderInfo);
            $this->display();
        }
    }







    public function addCart()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10051,"info"=>"非法请求")));
        } else {
            $specId=I("post.specId");
            $carts=session("cart");
            if (!is_array($carts)) {
                $carts=array();
            }
            $cartKey=md5($specId);
            if (false===array_key_exists($cartKey, $carts)) {
                $carts[$cartKey]=$specId;
                session("cart", $carts);
            }
            $cartNum=count($carts);
            exit(json_encode(array("code"=>10112,"info"=>$cartNum)));
        }
    }
    public function addCollect()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10051,"info"=>"非法请求")));
        } else {
            $specId=I("post.specId");
            $carts=session("collect");
            if (!is_array($carts)) {
                $carts=array();
            }
            $cartKey=md5($specId);
            if (false===array_key_exists($cartKey, $carts)) {
                $carts[$cartKey]=$specId;
                session("collect", $carts);
            }
            $cartNum=count($carts);
            exit(json_encode(array("code"=>10112,"info"=>$cartNum)));
        }
    }





    public function getProductsInfoFromCart()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10051,"info"=>"非法请求")));
        } else {
            $carts=session("cart");
            if (!is_array($carts)) {
                exit(json_encode(array("code"=>10051,"info"=>"空空如也")));
            } else {
                $specInfo=D("Specification")->getProductsInfoBySpecId($carts);
                foreach ($specInfo as $key=>$value) {
                    if ($this->langFlag==2) {
                        $specInfo[$key]['product']['products_name']=$value['product']['products_english_name'];
                    } else {
                        $specInfo[$key]['product']['products_name']=$value['product']['products_chinese_name'];
                    }
                }
                exit(json_encode(array("code"=>10113,"info"=>$specInfo)));
            }
        }
    }
    public function calcPrice()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10051,"info"=>"非法请求")));
        } else {
            // 取出session中数据
            $cartDetail=session("cartDetail");
            if (!is_array($cartDetail)) {//阻止非法请求
                exit(json_encode(array("code"=>10051,"info"=>"非法请求")));
            }

            $countryId=I("post.countryId");
            $specId=I("post.specId");
            $buyNum=I("post.buyNum");

            $specIdKey=md5($specId);
            $cartDetail[$specIdKey]['specId']=$specId;
            $cartDetail[$specIdKey]['buyNum']=$buyNum;

            if ($countryId==1) {//  中国地区  ‘RMB’

                $cartDetail[$specIdKey]['subtotal']=$buyNum*$cartDetail[$specIdKey]['loading']*$cartDetail[$specIdKey]['rmb'];
            } elseif (empty($countryId)) {  //  未选择国家  采取客户默认币种

                if (defaultCurrency()=="USD") {//  ‘USD’  币种

                    $defaultCurrency="USD";

                    $cartDetail[$specIdKey]['subtotal']=$buyNum*$cartDetail[$specIdKey]['loading']*RMB2USD($cartDetail[$specIdKey]['rmb']);
                } else {// ‘RMB’币种

                    $defaultCurrency="RMB";

                    $cartDetail[$specIdKey]['subtotal']=$buyNum*$cartDetail[$specIdKey]['loading']*$cartDetail[$specIdKey]['rmb'];
                }
            } else {//  国外地区  ‘USD’

                $cartDetail[$specIdKey]['subtotal']=$buyNum*$cartDetail[$specIdKey]['loading']*RMB2USD($cartDetail[$specIdKey]['rmb']);
            }

            //  存数据
            session("cartDetail", $cartDetail);

            //  重新计算价格
            $totalPrice=D("Products", "Logic")->calcCartTotalPrice($cartDetail);

            //            折扣
            $userInfo=D("User")->getUserInfoByUserId($_SESSION['userid']);
            if($userInfo["discount"]==0){
                $GradeInfo=M("Grade")->where(array("grade_id"=>$userInfo['grade_id']))->find();
                $discount=(100-intval($GradeInfo["gr_discount"]))*0.01;
                if($this->rangeTime($GradeInfo["gr_time"],$GradeInfo["gr_endtime"])){
                    $totalPrice=$totalPrice*$discount;
                }
            }else{
                $discount=(100-intval($userInfo["discount"]))*0.01;
                if($this->rangeTime($userInfo["start_time"],$userInfo["end_time"])){
                    $totalPrice=$totalPrice*$discount;
                }
            }

            session("totalPrice", $totalPrice);
            $userId=session("userid");
            $specInfo=D("Specification")->getWholeSpecificationInfo($specId, $userId);

            $data=array(
                "orderTotalNum"=>($specInfo[0]['loading']*$buyNum),
                "smallPrice"=>number_format($cartDetail[$specIdKey]['subtotal'], 2),
                "inventory"=>$specInfo[0]['inventory']
            );
            exit(json_encode(array("code"=>10114,"info"=>$data,'totalPrice'=>$totalPrice)));
        }
    }
    public function delSpecFromCart()
    {




        // if (!IS_POST) {
        //     exit(json_encode(array("code"=>10051,"info"=>"非法请求")));
        // } else {
        $cartDetail=session("cartDetail");
        // if (!is_array($cartDetail)) {//阻止非法请求
        //     exit(json_encode(array("code"=>10051,"info"=>"非法请求")));
        // }
        // echo "string";
        // die();

        $specId=I("post.specId");
        $specIdKey=md5($specId);
        $carts=session("cart");


        unset($cartDetail[$specIdKey]);//删除购物车里的相关数据
        unset($carts[$specIdKey]);


        session("cartDetail", $cartDetail);
        session("cart", $carts);
        $totalPrice=D("Products", "Logic")->calcCartTotalPrice($cartDetail);

        //            折扣
        $userInfo=D("User")->getUserInfoByUserId($_SESSION['userid']);
        if($userInfo["discount"]==0){
            $GradeInfo=M("Grade")->where(array("grade_id"=>$userInfo['grade_id']))->find();
            $discount=(100-intval($GradeInfo["gr_discount"]))*0.01;
            if($this->rangeTime($GradeInfo["gr_time"],$GradeInfo["gr_endtime"])){
                $totalPrice=$totalPrice*$discount;
            }
        }else{
            $discount=(100-intval($userInfo["discount"]))*0.01;
            if($this->rangeTime($userInfo["start_time"],$userInfo["end_time"])){
                $totalPrice=$totalPrice*$discount;
            }
        }



        session("totalPrice", $totalPrice);
        exit(json_encode(array("code"=>10115,"info"=>$totalPrice)));
        // }
    }


    public function delSpecFromCart2()
    {


        $cartDetail=session("cartDetail");

        $specId=I("post.specId");
        $specIdKey=md5($specId);
        $carts=session("collect");


        unset($cartDetail[$specIdKey]);//删除购物车里的相关数据
        unset($carts[$specIdKey]);


        session("cartDetail", $cartDetail);
        session("collect", $carts);
        $totalPrice=D("Products", "Logic")->calcCartTotalPrice($cartDetail);

        //            折扣
        $userInfo=D("User")->getUserInfoByUserId($_SESSION['userid']);
        if($userInfo["discount"]==0){
            $GradeInfo=M("Grade")->where(array("grade_id"=>$userInfo['grade_id']))->find();
            $discount=(100-intval($GradeInfo["gr_discount"]))*0.01;
            if($this->rangeTime($GradeInfo["gr_time"],$GradeInfo["gr_endtime"])){
                $totalPrice=$totalPrice*$discount;
            }
        }else{
            $discount=(100-intval($userInfo["discount"]))*0.01;
            if($this->rangeTime($userInfo["start_time"],$userInfo["end_time"])){
                $totalPrice=$totalPrice*$discount;
            }
        }
        session("totalPrice", $totalPrice);
        exit(json_encode(array("code"=>10115,"info"=>$totalPrice)));
        // }
    }



    public function getCartList()
    {
        $cartDetail=session("cartDetail");
        $countryId=I("post.countryId");
        $fob_city=I("post.fob_city");
        $fobmoney=M("fob")->where("id=3")->find();
        if ($fob_city == 'shenzhen') {
            $sum_fob = 250;
        } else {
            $sum_fob = $fobmoney["fob"];
        }
        $firstUl='';
        $secondUl='';
        $threeUl='';
        $fourthUl='';

        $firstUl.='<ul><li>'.L('_PUBLIC_PRODUCT_IMG_').'</li>';
        $firstUl.='<li>'.L('_PUBLIC_PRODUCT_MODEL_').'</li>';
        $firstUl.='<li>'.L('_PUBLIC_PRODUCT_SPECIFICATION_').'</li>';
        $firstUl.='<li>'.L('_PUBLIC_PRODUCT_LENGTH_').'</li>';
        $firstUl.='<li>'.L('_PUBLIC_PRODUCT_COLOR_').'</li>';
        // $firstUl.='<li>'.L('_PUBLIC_PRODUCT_PACKAGE_').'</li>';
        $firstUl.='<li>'.L('_PUBLIC_PRODUCT_LOADING_').'</li>';
        $firstUl.='<li>'.L('_PUBLIC_PRODUCT_BOX_').'</li>';
        $firstUl.='<li>'.L('_PUBLIC_PRODUCT_TOTAL_').'</li>';
        $firstUl.='<li>'.L('_PUBLIC_PRODUCT_SINGLE_PRICE_').'</li>';
        $firstUl.='<li>'.L('_PUBLIC_PRODUCT_AMOUNT_').'</li>';
        $firstUl.='</ul>';


        $count=count($cartDetail);
        $index=0;
        if ($countryId==1) {//  这是中国

            foreach ($cartDetail as $key=>$spi) {
                $index++;
                $secondUl.='<ul class="cartPItem">';
                $secondUl.='<li><img src="'.WEB_ROOT."/Public/".$spi['pic'].'"/></li>';
                $secondUl.='<li>'.$spi['modelName'].'</li>';
                if ($this->langFlag==2) {
                    $secondUl.='<li title="'.$spi['specification2'].'">'.$spi['specification2'].'</li>';
                } else {
                    $secondUl.='<li title="'.$spi['specification'].'">'.$spi['specification'].'</li>';
                }
                $secondUl.='<li>'.$spi['length'].'m</li>';
                if ($this->langFlag==2) {
                    $secondUl.='<li>'.$spi['colorName2'].'</li>';
                } else {
                    $secondUl.='<li>'.$spi['colorName'].'</li>';
                }
                // $secondUl.='<li>'.L('_PUBLIC_CARTON_').'</li>';
                $secondUl.='<li>'.$spi['loading'].'</li>';
                $secondUl.='<li><div data-spec-id="'.$spi['specification_id'].'"><span data-desc="down" class="down" style="cursor:pointer;">-</span><font class="buyNum">'.$spi['buyNum'].'</font><span data-desc="up" class="up" style="cursor:pointer;">+</span></div></li>';
                $secondUl.='<li class="orderBuyNum">'.($spi['loading']*$spi['buyNum']).'</li>';
                $secondUl.='<li>'.$spi['rmb'].'</li>';
                $secondUl.='<li class="smallPrice">'.number_format($spi['loading']*$spi['buyNum']*$spi['rmb'], 2).'</li>';
                $secondUl.='<div style="cursor:pointer;" data-spec-id="'.$spi['specification_id'].'" class="del">-</div>';
                $secondUl.='<div class="shopping_tip">';
                $secondUl.='<span>'.L('_PUBLIC_REFERENCE_INVENTORY_').'<font class="inventory">'.$spi['inventory'].'</font></span>';



                if (($spi['inventory']-($spi['loading']*$spi['buyNum']))<0) {
                    $secondUl.='<span class="error" style="color:red">*'.L('_PUBLIC_EXCESS_INVENTORY_').'</span>';
                }
                $secondUl.='</div></ul>';
                if ($index<($count)) {
                    $secondUl.='<div class="line"></div>';
                }

                $cartDetail[$key]['subtotal']=$spi['loading']*$spi['buyNum']*$spi['rmb'];
            }

            $totalPrice=D("Products", "Logic")->calcCartTotalPrice($cartDetail);
            //            折扣
            $userInfo=D("User")->getUserInfoByUserId($_SESSION['userid']);
            if($userInfo["discount"]==0){
                $GradeInfo=M("Grade")->where(array("grade_id"=>$userInfo['grade_id']))->find();
                $discount=(100-intval($GradeInfo["gr_discount"]))*0.01;
                if($this->rangeTime($GradeInfo["gr_time"],$GradeInfo["gr_endtime"])){
                    $totalPrice=$totalPrice*$discount;
                }
            }else{
                $discount=(100-intval($userInfo["discount"]))*0.01;
                if($this->rangeTime($userInfo["start_time"],$userInfo["end_time"])){
                    $totalPrice=$totalPrice*$discount;
                }
            }

            if ($totalPrice>$this->fob()) {
                $threeUl.='<dl style="display:none;" class="extraLogisticsFee">';
                $threeUl.='<dd>'.L('_PUBLIC_LOGISTICS_EX_').'</dd>';
                $threeUl.='</dl>';
                $fourthUl.='<dl><div><dt>'.L('_PUBLIC_RMB_TOTAL_').'</dt>';
                $fourthUl.='<dd id="totalPrice">'.number_format($totalPrice, 2).'</dd>';
                $fourthUl.='</div></dl>';
            } else {
                $threeUl.='<dl class="extraLogisticsFee">';
                $threeUl.='<dd>'.L('_PUBLIC_LOGISTICS_EX_').'</dd>';
                $threeUl.='</dl>';
                $fourthUl.='<dl><div><dt>'.L('_PUBLIC_RMB_TAX_').'</dt>';
                $fourthUl.='<dd id="totalPrice">'.number_format($totalPrice, 2).'</dd>';
                $fourthUl.='</div></dl>';
            }
        } elseif ($countryId==0) {//  没有任何选择，取客户默认币种

            if (defaultCurrency()=="USD") {
                $defaultCurrency="USD";//  默认  USD  币种，选择，取法

                foreach ($cartDetail as $key=>$spi) {
                    $index++;
                    $secondUl.='<ul class="cartPItem">';
                    $secondUl.='<li><img src="'.WEB_ROOT."/Public/".$spi['pic'].'"/></li>';
                    $secondUl.='<li>'.$spi['modelName'].'</li>';
                    if ($this->langFlag==2) {
                        $secondUl.='<li title="'.$spi['specification2'].'">'.$spi['specification2'].'</li>';
                    } else {
                        $secondUl.='<li title="'.$spi['specification'].'">'.$spi['specification'].'</li>';
                    }
                    $secondUl.='<li>'.$spi['length'].'m</li>';
                    if ($this->langFlag==2) {
                        $secondUl.='<li>'.$spi['colorName2'].'</li>';
                    } else {
                        $secondUl.='<li>'.$spi['colorName'].'</li>';
                    }
                    // $secondUl.='<li>'.L('_PUBLIC_CARTON_').'</li>';
                    $secondUl.='<li>'.$spi['loading'].'</li>';
                    $secondUl.='<li><div data-spec-id="'.$spi['specification_id'].'"><span data-desc="down" class="down" style="cursor:pointer;">-</span><font class="buyNum">'.$spi['buyNum'].'</font><span data-desc="up" class="up" style="cursor:pointer;">+</span></div></li>';
                    $secondUl.='<li class="orderBuyNum">'.($spi['loading']*$spi['buyNum']).'</li>';
                    $secondUl.='<li>'.RMB2USD($spi['rmb']).'</li>';
                    $secondUl.='<li class="smallPrice">'.number_format($spi['loading']*$spi['buyNum']*RMB2USD($spi['rmb']), 2).'</li>';
                    $secondUl.='<div style="cursor:pointer;" data-spec-id="'.$spi['specification_id'].'" class="del">-</div>';
                    $secondUl.='<div class="shopping_tip">';
                    $secondUl.='<span>'.L('_PUBLIC_REFERENCE_INVENTORY_').'<font class="inventory">'.$spi['inventory'].'</font></span>';
                    if (($spi['inventory']-($spi['loading']*$spi['buyNum']))<0) {
                        $secondUl.='<span class="error" style="color:red">*'.L('_PUBLIC_EXCESS_INVENTORY_').'</span>';
                    }
                    $secondUl.='</div></ul>';
                    if ($index<($count)) {
                        $secondUl.='<div class="line"></div>';
                    }

                    $cartDetail[$key]['subtotal']=$spi['loading']*$spi['buyNum']*RMB2USD($spi['rmb']);
                }





                $totalPrice=D("Products", "Logic")->calcCartTotalPrice($cartDetail);

                //            折扣
                $userInfo=D("User")->getUserInfoByUserId($_SESSION['userid']);
                if($userInfo["discount"]==0){
                    $GradeInfo=M("Grade")->where(array("grade_id"=>$userInfo['grade_id']))->find();
                    $discount=(100-intval($GradeInfo["gr_discount"]))*0.01;
                    if($this->rangeTime($GradeInfo["gr_time"],$GradeInfo["gr_endtime"])){
                        $totalPrice=$totalPrice*$discount;
                    }
                }else{
                    $discount=(100-intval($userInfo["discount"]))*0.01;
                    if($this->rangeTime($userInfo["start_time"],$userInfo["end_time"])){
                        $totalPrice=$totalPrice*$discount;
                    }
                }

                if ($totalPrice>$this->fob()) {
                    $threeUl.='<dl style="display:none;" class="extraLogisticsFee">';
                    $threeUl.='<dd>'.L('_PUBLIC_SERVICE_FEE_').'</dd>';
                    $threeUl.='</dl>';
                    $fourthUl.='<dl><div><dt>'.L('_PUBLIC_USD_TOTAL_').'</dt>';
                    $fourthUl.='<dd id="totalPrice">'.$totalPrice.'</dd>';
                    $fourthUl.='</div></dl>';
                } else {
                    $threeUl.='<dl class="extraLogisticsFee">';
                    $threeUl.='<dd>'.L('_PUBLIC_SERVICE_FEE_').'：<span id="fob_num">'.$sum_fob.'</span>USD</dd>';
                    $threeUl.='</dl>';
                    $fourthUl.='<dl><div><dt>'.L('_PUBLIC_USD_TAX_').'</dt>';
                    $fourthUl.='<dd id="totalPrice">'.number_format($totalPrice+$sum_fob, 2).'</dd>';
                    $fourthUl.='</div></dl>';
                }
            } else {//  默认  RMB  币种，选择，取法

                $defaultCurrency="RMB";

                foreach ($cartDetail as $key=>$spi) {
                    $index++;
                    $secondUl.='<ul class="cartPItem">';
                    $secondUl.='<li><img src="'.WEB_ROOT."/Public/".$spi['pic'].'"/></li>';
                    $secondUl.='<li>'.$spi['modelName'].'</li>';
                    if ($this->langFlag==2) {
                        $secondUl.='<li title="'.$spi['specification2'].'">'.$spi['specification2'].'</li>';
                    } else {
                        $secondUl.='<li title="'.$spi['specification'].'">'.$spi['specification'].'</li>';
                    }
                    $secondUl.='<li>'.$spi['length'].'m</li>';
                    if ($this->langFlag==2) {
                        $secondUl.='<li>'.$spi['colorName2'].'</li>';
                    } else {
                        $secondUl.='<li>'.$spi['colorName'].'</li>';
                    }
                    // $secondUl.='<li>'.L('_PUBLIC_CARTON_').'</li>';
                    $secondUl.='<li>'.$spi['loading'].'</li>';
                    $secondUl.='<li><div data-spec-id="'.$spi['specification_id'].'"><span data-desc="down" class="down" style="cursor:pointer;">-</span><font class="buyNum">'.$spi['buyNum'].'</font><span data-desc="up" class="up" style="cursor:pointer;">+</span></div></li>';
                    $secondUl.='<li class="orderBuyNum">'.($spi['loading']*$spi['buyNum']).'</li>';
                    $secondUl.='<li>'.$spi['rmb'].'</li>';
                    $secondUl.='<li class="smallPrice">'.number_format($spi['loading']*$spi['buyNum']*$spi['rmb'], 2).'</li>';
                    $secondUl.='<div style="cursor:pointer;" data-spec-id="'.$spi['specification_id'].'" class="del">-</div>';
                    $secondUl.='<div class="shopping_tip">';
                    $secondUl.='<span>'.L('_PUBLIC_REFERENCE_INVENTORY_').'<font class="inventory">'.$spi['inventory'].'</font></span>';
                    if (($spi['inventory']-($spi['loading']*$spi['buyNum']))<0) {
                        $secondUl.='<span class="error" style="color:red">*'.L('_PUBLIC_EXCESS_INVENTORY_').'</span>';
                    }
                    $secondUl.='</div></ul>';
                    if ($index<($count)) {
                        $secondUl.='<div class="line"></div>';
                    }

                    $cartDetail[$key]['subtotal']=$spi['loading']*$spi['buyNum']*$spi['rmb'];
                }



                $totalPrice=D("Products", "Logic")->calcCartTotalPrice($cartDetail);
                //            折扣
                $userInfo=D("User")->getUserInfoByUserId($_SESSION['userid']);
                if($userInfo["discount"]==0){
                    $GradeInfo=M("Grade")->where(array("grade_id"=>$userInfo['grade_id']))->find();
                    $discount=(100-intval($GradeInfo["gr_discount"]))*0.01;
                    if($this->rangeTime($GradeInfo["gr_time"],$GradeInfo["gr_endtime"])){
                        $totalPrice=$totalPrice*$discount;
                    }
                }else{
                    $discount=(100-intval($userInfo["discount"]))*0.01;
                    if($this->rangeTime($userInfo["start_time"],$userInfo["end_time"])){
                        $totalPrice=$totalPrice*$discount;
                    }
                }

//
//                echo $totalPrice;
//                die();

                if ($totalPrice>$this->fob()) {
                    $threeUl.='<dl style="display:none;" class="extraLogisticsFee">';
                    $threeUl.='<dd>'.L('_PUBLIC_LOGISTICS_EX_').'</dd>';
                    $threeUl.='</dl>';
                    $fourthUl.='<dl><div><dt>'.L('_PUBLIC_RMB_TOTAL_').'</dt>';

                    $fourthUl.='<dd id="totalPrice">'.number_format($totalPrice, 2).'</dd>';
                    $fourthUl.='</div></dl>';
                } else {
                    $threeUl.='<dl class="extraLogisticsFee">';
                    $threeUl.='<dd>'.L('_PUBLIC_LOGISTICS_EX_').'</dd>';
                    $threeUl.='</dl>';
                    $fourthUl.='<dl><div><dt>'.L('_PUBLIC_RMB_TAX_').'</dt>';
                    $fourthUl.='<dd id="totalPrice">'.number_format($totalPrice, 2).'</dd>';
                    $fourthUl.='</div></dl>';
                }
            }
        } else {//   国外地区

            foreach ($cartDetail as $key=>$spi) {
                $index++;
                $secondUl.='<ul class="cartPItem">';
                $secondUl.='<li><img src="'.WEB_ROOT."/Public/".$spi['pic'].'"/></li>';
                $secondUl.='<li>'.$spi['modelName'].'</li>';
                if ($this->langFlag==2) {
                    $secondUl.='<li title="'.$spi['specification2'].'">'.$spi['specification2'].'</li>';
                } else {
                    $secondUl.='<li title="'.$spi['specification'].'">'.$spi['specification'].'</li>';
                }
                $secondUl.='<li>'.$spi['length'].'m</li>';
                if ($this->langFlag==2) {
                    $secondUl.='<li>'.$spi['colorName2'].'</li>';
                } else {
                    $secondUl.='<li>'.$spi['colorName'].'</li>';
                }
                // $secondUl.='<li>'.L('_PUBLIC_CARTON_').'</li>';
                $secondUl.='<li>'.$spi['loading'].'</li>';
                $secondUl.='<li><div data-spec-id="'.$spi['specification_id'].'"><span data-desc="down" class="down" style="cursor:pointer;">-</span><font class="buyNum">'.$spi['buyNum'].'</font><span data-desc="up" class="up" style="cursor:pointer;">+</span></div></li>';
                $secondUl.='<li class="orderBuyNum">'.($spi['loading']*$spi['buyNum']).'</li>';
                $secondUl.='<li>'.RMB2USD($spi['rmb']).'</li>';
                $secondUl.='<li class="smallPrice">'.number_format($spi['loading']*$spi['buyNum']*RMB2USD($spi['rmb']), 2).'</li>';
                $secondUl.='<div style="cursor:pointer;" data-spec-id="'.$spi['specification_id'].'" class="del">-</div>';
                $secondUl.='<div class="shopping_tip">';
                $secondUl.='<span>'.L('_PUBLIC_REFERENCE_INVENTORY_').'<font class="inventory">'.$spi['inventory'].'</font></span>';


//                $secondUl.='<select name="fob_city" id="fob_city" style="width: 200px;height: 30px;border-color: #DCDCDC;">';
//                $secondUl.='<option value="shenzhen">FOB ShenZhen</option>';
//                $secondUl.=' <option value="hk">FOB HK</option>';
//                $secondUl='</select>';

                if (($spi['inventory']-($spi['loading']*$spi['buyNum']))<0) {
                    $secondUl.='<span class="error" style="color:red">*'.L('_PUBLIC_EXCESS_INVENTORY_').'</span>';
                }
                $secondUl.='</div></ul>';
                if ($index<($count)) {
                    $secondUl.='<div class="line"></div>';
                }

                $cartDetail[$key]['subtotal']=$spi['loading']*$spi['buyNum']*RMB2USD($spi['rmb']);
            }


            $totalPrice=D("Products", "Logic")->calcCartTotalPrice($cartDetail);
            //            折扣
            $userInfo=D("User")->getUserInfoByUserId($_SESSION['userid']);



            if($userInfo["discount"]==0){
                $GradeInfo=M("Grade")->where(array("grade_id"=>$userInfo['grade_id']))->find();
                $discount=(100-intval($GradeInfo["gr_discount"]))*0.01;
                if($this->rangeTime($GradeInfo["gr_time"],$GradeInfo["gr_endtime"])){
                    $totalPrice=$totalPrice*$discount;
                }
            }else{
                $discount=(100-intval($userInfo["discount"]))*0.01;
                if($this->rangeTime($userInfo["start_time"],$userInfo["end_time"])){
                    $totalPrice=$totalPrice*$discount;
                }
            }

            $fobdz=M("fob")->where("id=1")->getField('fob');

            $fobdz2=M("fob")->where("id=2")->getField('fob');
            if ($totalPrice>$this->fob()) {
                $threeUl.='<dl style="display:none;" class="extraLogisticsFee">';
                $threeUl.='<dd>'.L('_PUBLIC_SERVICE_FEE_').'</dd>';
                $threeUl.='</dl>';
                $fourthUl.='<dl><div class="ceshi"><dd>'.L('_PUBLIC_USD_TOTAL_').'</dd>';
                $fourthUl.='<dd id="totalPrice">'.$totalPrice.'</dd>';
                $fourthUl.='</div></dl>';
//               $fourthUl.='<dl><select name="fob_city" id="fob_city" style="width: 200px;height: 30px;border-color: #DCDCDC;"><option value="shenzhen">FOB ShenZhen</option><option value="shenzhen">FOB ShenZhen</option></select></dl>';

            } else {
                $threeUl.='<dl class="extraLogisticsFee">';
                $threeUl.='<dd>'.L('_PUBLIC_SERVICE_FEE_').'：<span id="fob_num">'.$sum_fob.'</span>USD</dd>';
                $threeUl.='</dl>';
                $fourthUl.='<dl><div class="ceshi" ><dd id="totalPrice">'.number_format($totalPrice+$sum_fob, 2).'</dd>';
                $fourthUl.='<dd>'.L('_PUBLIC_USD_TAX_').'</dd>';
                $fourthUl.='</div></dl>';
                $fourthUl.='<dl style="float:right;">
                            <select name="fob_city" id="fob_city" style="width: 200px;height: 30px;border-color: #DCDCDC;">
                            <option value="shenzhen">'.$fobdz.'</option>
                            <option value="hk">'.$fobdz2.'</option>
                            </select>
                            </dl>';

            }
        }

        session("cartDetail", $cartDetail);

        exit(json_encode(array("code"=>10090,"msg"=>$firstUl.$secondUl.$threeUl.$fourthUl)));
    }

//判断当前是否在折扣时间段内
    public function rangeTime($startTime,$endTime){
        //开始时间
        $start = strtotime($date.$startTime);
        $end = strtotime($date.$endTime);
        //当前时间
        $now = time();
        if($now >=$start && $now<=$end){
            return true;
        }else{
            return false;
        }
    }


}
