<?php
namespace Home\Controller;

class ProductController extends BaseController
{
    public function product_list()
    {
        $where=array();
        $keyword=I("get.keyword");
        if (!empty($keyword)) {
            if ($this->langFlag==1) {
                $fields=array(
                    "www_products.products_chinese_name",
                    "www_specification.color_name",
                    "www_specification.model_name"
                );
            } else {
                $fields=array(
                    "www_products.products_english_name",
                    "www_specification.color_name2",
                    "www_specification.model_name"
                );
            }

            $where[implode("|", $fields)]=array("like","%".$keyword."%");
            $this->assign("keyword", $keyword);
        }
        $joint=I("get.joint");
        $serial=I("get.serial");
        if (!empty($joint)) {
            $joint2=base64_decode($joint);
            $where['joint_id']=array("in",$joint2);
            $joint3=explode(",", $joint2);
            if (count($joint3)==1) {
                $this->assign("joint", $joint3[0]);
            }
        }
        if (!empty($serial)) {
            $this->assign("serial", $serial);
        }
        if (empty($where)) {
            $where="1=1";
        }
        $displayNumber=10;
        if (isset($_SESSION['displayNumber'])&&!empty($_SESSION['displayNumber'])) {
            $displayNumber=$_SESSION['displayNumber'];
        }

        $productsIds=M("Products")
                    ->join("left join __SPECIFICATION__ on __SPECIFICATION__.products_id=__PRODUCTS__.products_id")
                    ->where($where)
                    ->order("www_products.products_id asc")
                    ->group("www_products.products_id")
                    ->field(array("www_products.products_id"))
                    ->select();
        $oneProductsIds=array();
        foreach ($productsIds as $k=>$v) {
            $oneProductsIds[]=$v['products_id'];
        }

        $productsIds=M("Products")
                    ->join("left join __SPECIFICATION__ on __SPECIFICATION__.products_id=__PRODUCTS__.products_id")
                    ->where(array("www_products.products_id"=>array("in",implode(",", $oneProductsIds))))
                    ->order("www_products.products_id asc")
                    ->field(array("www_products.products_id","www_specification.specification_id"))
                    ->select();
        $userId=$_SESSION["userid"];
        //执行过滤
        foreach ($productsIds as $kk=>$vv) {
            $userProduct=D("UserProduct")->getUserProductInfoBySU($vv['specification_id'], $userId);
            if (empty($userProduct)) {// 没有找到数据，说明没有授权----》进一步查找等级授权

                //  ||empty($userProduct['rmb'])||($userProduct['rmb']<=0)

                $userInfo=D("User")->getUserInfoByUserId($userId);
                $gradeProduct=D("GradeProduct")->getGradeProductInfoBySG($vv['specification_id'], $userInfo['grade_id']);

                if (empty($gradeProduct)) {//  没有数据，说明等级也没有授权--》就是没有给用户分配等级

                    unset($productsIds[$kk]);//删除此产品
                } else {//  不为空，有数据


                    if ($gradeProduct['products_status_id']==2) {//  产品对于此用户处于‘下架’状态

                        unset($productsIds[$kk]);//删除此产品
                    }
                }
            } else {//   不为空，说明授权过

                if ($userProduct['products_status_id']==2) {//  产品处于下架状态

                    unset($productsIds[$kk]);//删除此产品
                }
            }
        }

        $productIds=array();
        foreach ($productsIds as $kkk=>$vvv) {
            if (false===array_search($vvv['products_id'], $productIds)) {
                $productIds[]=$vvv['products_id'];
            } else {
                unset($productsIds[$kkk]);
            }
        }

        $count=count($productsIds);
        $page=new \Think\Page($count, $displayNumber);

        $curP=1;
        if (isset($_GET['p'])&&!empty($_GET['p'])) {
            $curP=$_GET['p'];
        }
        $curP=intval($curP);
        if (empty($curP)||$curP<=0) {
            $curP=1;
        }

        // print_r($productsIds);die;

        $productsIds2=array_slice($productsIds, ($curP-1)*$displayNumber, $displayNumber);



        $proIds=array();
        foreach ($productsIds2 as $key=>$value) {
            $proIds[]=$value['products_id'];
        }

        $productsInfo=M("Products")
                    ->where(array("products_id"=>array("in",implode(",", $proIds))))
                    ->select();

        $products=D("Admin/Products")->getProductsList($productsInfo);
        $products=D("Home/Products", "Logic")->dealProducts($products);


        $zhen=$_GET["hl"];
        $this->assign("zhen", $zhen);

        $this->assign("count", $count);
        $this->assign("serial_info", filterSerial());
        $this->assign("page", $page->show());
        $this->assign("products", $products);
        $this->assign("displayNumber", $displayNumber);
        $this->display();
    }
    public function confirmDisplay()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10051,"msg"=>"非法请求")));
        } else {
            $number=I("post.number");
            $keyword=I("post.keyword");
            if ($number<=0) {
                $number=10;
            }
            session("displayNumber", $number);
            if (!empty($keyword)) {
                $url=U('Product/product_list', array('keyword'=>$keyword));
            } else {
                $url=U('Product/product_list');
            }
            exit(json_encode(array("code"=>10111,"url"=>$url)));
        }
    }
    public function getProductInfo()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10051,"info"=>"非法请求")));
        } else {
            $productsId=I("post.productsId");
            $length=I("post.length");
            $color=I("post.color");
            $userId=session("userid");
            $specInfo=D("Specification")->getSpecificationInfoByPLC($productsId, $length, $color);
            if (empty($specInfo)) {
                exit(json_encode(array("code"=>10110,"info"=>L('_PUBLIC_COMING_SOON_'))));
            } else {
                $userProduct=D("UserProduct")->getUserProductInfoBySU($specInfo['specification_id'], $userId);
                $price=9999999;
                if (empty($userProduct)||empty($userProduct['rmb'])||($userProduct['rmb']<=0)) {
                    if (empty($userProduct)||($userProduct['products_status_id']==1)) {
                        $userInfo=D("User")->getUserInfoByUserId($userId);
                        $gradeProduct=D("GradeProduct")->getGradeProductInfoBySG($specInfo['specification_id'], $userInfo['grade_id']);

                        if ($userProduct['products_status_id']==1) {
                            $price=$gradeProduct['rmb'];
                        } else {
                            if ($gradeProduct['products_status_id']==1) {
                                $price=$gradeProduct['rmb'];
                            }
                            if ($gradeProduct['products_status_id']==2) {
                                exit(json_encode(array("code"=>10110,"info"=>L('_PUBLIC_COMING_SOON_'))));
                            }
                        }
                    } else {
                        exit(json_encode(array("code"=>10110,"info"=>L('_PUBLIC_COMING_SOON_'))));
                    }
                } else {
                    if ($userProduct['products_status_id']==1) {
                        $price=$userProduct['rmb'];
                    }
                    if ($userProduct['products_status_id']==2) {
                        exit(json_encode(array("code"=>10110,"info"=>L('_PUBLIC_COMING_SOON_'))));
                    }
                }
                if ($this->langFlag==2) {
                    $specInfo["color_name"]=$specInfo["color_name2"];
                }
                if (!empty($specInfo["spec_img"])) {
                    $specInfo['specImg']=PUBLIC_RESOURCE.$specInfo["spec_img"];
                } else {
                    $proImg=M("Products")->where(array("products_id"=>$productsId))->getField("products_img");
                    $specInfo['specImg']=PUBLIC_RESOURCE.$proImg;
                }

                $specInfo['model']='<dl>
									<dt class="model">'.$specInfo["model_name"].'</dt>
								</dl>';

                if (defaultCurrency()=="RMB") {//  默认币种如果是人民币

                    $specInfo['rmb']='<dl>
									<dt class="priceNum">'.$price.'</dt>
									<dt>RMB/PCS</dt>
								</dl>';
                } else {//  否则，当做USD处理

                    $specInfo['rmb']='<dl>
									<dt class="priceNum">'.RMB2USD($price).'</dt>
									<dt>USD/PCS</dt>
								</dl>';
                }

                $carts=session("cart");
                 $collects=session("collect");
                if (cookie("think_language") == 'zh-cn') {
                    $collor = '收藏';
                } else {
                    $collor = 'Add to collect';
                }
                
                    if ((!empty($carts)&&is_array($carts)&&(false!==array_search($specInfo['specification_id'], $carts)))&&(!empty($collects)&&is_array($collects)&&(false!==array_search($specInfo['specification_id'], $collects)))   ) {
                      $specInfo['inventory']='<p>'.L('_PUBLIC_INVENTORY_').':<font >'.$specInfo['inventory'].'</font></p>
                    <a data-spec-id="'.$specInfo['specification_id'].'" class="addCart" style="background-color:#e7e7e7;" href="javascript:;">'.L('_PUBLIC_CARTED_').'</a>'.'<a id="fucking_add_collo" class="addCollo" data-spec-id="'.$specInfo['specification_id'].'" style="background-color:#e7e7e7;" href="javacript:;">'.$collor.'</a>';
                        }
                else if (!empty($carts)&&is_array($carts)&&(false!==array_search($specInfo['specification_id'], $carts))) {
                    $specInfo['inventory']='<p>'.L('_PUBLIC_INVENTORY_').':<font >'.$specInfo['inventory'].'</font></p>
                    <a data-spec-id="'.$specInfo['specification_id'].'" class="addCart" style="background-color:#e7e7e7;" href="javascript:;">'.L('_PUBLIC_CARTED_').'</a>'.'<a id="fucking_add_collo" class="addCollo" data-spec-id="'.$specInfo['specification_id'].'" href="javacript:;">'.$collor.'</a>';
                }else if(!empty($collects)&&is_array($collects)&&(false!==array_search($specInfo['specification_id'], $collects))){
                      $specInfo['inventory']='<p>'.L('_PUBLIC_INVENTORY_').':<font >'.$specInfo['inventory'].'</font></p>
                    <a data-spec-id="'.$specInfo['specification_id'].'" class="addCart"  href="javascript:;">'.L('_PUBLIC_ADD_CART_').'</a>'.'<a id="fucking_add_collo" class="addCollo" data-spec-id="'.$specInfo['specification_id'].'" style="background-color:#e7e7e7;" href="javacript:;">'.$collor.'</a>';
                } else {
                    $specInfo['inventory']='<p>'.L('_PUBLIC_INVENTORY_').':<font >'.$specInfo['inventory'].'</font></p>
                    <a data-spec-id="'.$specInfo['specification_id'].'" class="addCart" href="javascript:;">'.L('_PUBLIC_ADD_CART_').'</a>'.'<a id="fucking_add_collo" class="addCollo" data-spec-id="'.$specInfo['specification_id'].'"  href="javacript:;">'.$collor.'</a>';
                }

                $paramOther='<dl>
								<dt>'.L('_PUBLIC_PRODUCT_COLOR_').'：'.$specInfo["color_name"].'</dt>
								<dt>'.L('_PUBLIC_PRODUCT_LOADING_').'：'.$specInfo["loading"].'PCS</dt>
								<dt>'.L('_PUBLIC_ROUGH_WEIGHT_').'：'.$specInfo["rough_weight"].'KG</dt>
								<dt>'.L('_PUBLIC_NET_WEIGHT_').'：'.$specInfo["net_weight"].'KG</dt>
								<dt>'.L('_PUBLIC_VOLUME_').'：'.$specInfo["volume"].'CBM</dt>
								<dt>'.L('_PUBLIC_RNR_').'</dt>
							</dl>';
                $specInfo['paramOther']=$paramOther;
                exit(json_encode(array("code"=>10111,"info"=>$specInfo)));
            }
        }
    }

    
    
    public function createUrl()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10150,"msg"=>"非法操作")));
        } else {
            $serial=I("post.serial");
            $joint=I("post.joint");
            $keywords=I("post.keywords");

            $sJointIds=array();
            if (empty($joint)) {//  没有选择  ’小接头‘

                $smallJoint=filterSmallJoint($serial);//  获取指定系列下  所有‘小接头’

                foreach ($smallJoint as $key=>$value) {
                    $sJointIds[]=$value['category_id'];
                }
            } else {//  选择了  ’小接头‘
                $sJointIds[]=$joint;
            }

            $condition=array();//条件数组
            if (!empty($sJointIds)) {
                $sJointIds=str_replace("=", "", base64_encode(implode(",", $sJointIds)));
                $condition['joint']=$sJointIds;
            }
            if (!empty($keywords)) {
                $condition['keyword']=$keywords;
            }
            if (!empty($serial)) {
                $condition['serial']=$serial;
            }
            exit(json_encode(array("code"=>10151,"msg"=>U('Product/product_list', $condition))));
        }
    }
}
