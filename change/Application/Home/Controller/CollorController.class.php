<?php
namespace Home\Controller;

use Libs\PinYin;

class CollorController extends BaseController
{
    public function index()
    {
        if (!IS_POST) {
            $carts=session("collect");
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
            // print_r($data);die;
            if (empty($data)) {
                exit("错误");
            } else {
                $this->assign("data", $data);
                $this->assign("language", $this->langFlag);
                $this->display();
            }
        } else {
            $data=S(md5($_SESSION['userid']));
            if (empty($data)) {
                exit(json_encode(array("code"=>10051,"info"=>"非法请求")));
            } else {
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
//    public function addCollect()
//    {
//        if (!IS_POST) {
//            exit(json_encode(array("code"=>10051,"info"=>"非法请求")));
//        } else {
//            $specId=I("post.specId");
//            $carts=session("collect");
//            if (!is_array($carts)) {
//                $carts=array();
//            }
//            $cartKey=md5($specId);
//            if (false===array_key_exists($cartKey, $carts)) {
//                $carts[$cartKey]=$specId;
//                session("collect", $carts);
//            }
//            $cartNum=count($carts);
//            exit(json_encode(array("code"=>10112,"info"=>$cartNum)));
//        }
//    }
    
    
       public function addCollect()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10051,"info"=>"非法请求")));
        } else {
            $specId=I("post.specId");
            $collect=session("collect");
            if (!is_array($collect)) {
                $collect=array();
            }
            $cartKey=md5($specId);
            if (false===array_key_exists($cartKey, $collect)) {
                $collect[$cartKey]=$specId;
                session("collect", $collect);
            }
            $cartNum=count($collect);
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
        session("totalPrice", $totalPrice);
        exit(json_encode(array("code"=>10115,"info"=>$totalPrice)));
        // }
    }
    public function getCartList()
    {
        $cartDetail=session("cartDetail");
        $countryId=I("post.countryId");
        $fob_city=I("post.fob_city");
        if ($fob_city == 'shenzhen') {
            $sum_fob = 250;
        } else {
            $sum_fob = 450;
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
                    $secondUl.='<li>'.$spi['colorName2'].'m</li>';
                } else {
                    $secondUl.='<li>'.$spi['colorName'].'m</li>';
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
            if ($totalPrice>20000) {
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
                        $secondUl.='<li>'.$spi['colorName2'].'m</li>';
                    } else {
                        $secondUl.='<li>'.$spi['colorName'].'m</li>';
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
                if ($totalPrice>20000) {
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
                        $secondUl.='<li>'.$spi['colorName2'].'m</li>';
                    } else {
                        $secondUl.='<li>'.$spi['colorName'].'m</li>';
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
                if ($totalPrice>20000) {
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
                    $secondUl.='<li>'.$spi['colorName2'].'m</li>';
                } else {
                    $secondUl.='<li>'.$spi['colorName'].'m</li>';
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
            if ($totalPrice>20000) {
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
        }

        session("cartDetail", $cartDetail);

        exit(json_encode(array("code"=>10090,"msg"=>$firstUl.$secondUl.$threeUl.$fourthUl)));
    }
}
