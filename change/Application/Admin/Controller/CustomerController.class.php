<?php
namespace Admin\Controller;

use Think\Controller;

class CustomerController extends BaseController
{
    public function index()
    {
        $companyModel=D("Company");
        $gradeModel=D("Grade");
        $adminModel=D("Admin");

        $roleIds=D("GroupRole")->getRoleIdByGroupId(2);
        $adminIds=D("RoleAdmin")->getAdminIdByRoleId($roleIds);
        $adminInfos=$adminModel->getAdminInfosByAdminIds($adminIds);
        $gradeInfos=$gradeModel->getAllGrade();

        $where=setUserWhere();
        $count=M("User")
                ->join("left join __COMPANY__ on __COMPANY__.user_id=__USER__.user_id")
                ->where($where)->count();
        $page=new \Think\Page($count, 20);
        $userInfos=D("User")->getAllUser($page, $where);
        // print_r($userInfos);
        // die;
        $_adminNames=array();
        foreach ($userInfos as $key=>$value) {
            $userInfos[$key]['contacts']=$value['company_contacts'];
            $userInfos[$key]['grade']=$gradeModel->getGradeName($gradeInfos, $value['grade_id']);
            $adminId=$value['admin_id'];
            $adminIdKey=md5($adminId);
            if (empty($_adminNames[$adminIdKey])) {
                $adminName=$adminModel->getAdminNameByAdminId($adminId);
                $_adminNames[$adminIdKey]=$adminName;
            } else {
                $adminName=$_adminNames[$adminIdKey];
            }
            $userInfos[$key]['admin_name']=$adminName;
            $userInfos[$key]['contract_time']=$value['contract_time']==0?'-':date("Y-m-d", $value['contract_time']);
            $userInfos[$key]['last_time']=$value['last_time']==0?'-':date("Y-m-d", $value['last_time']);
            $userInfos[$key]['order_time']=$value['order_time']==0?'-':date("Y-m-d", $value['order_time']);
            $userInfos[$key]['url']=U("Customer/view", array('id'=>$value['user_id']));
        }
        if (!IS_POST) {
            $this->assign("user_info", $userInfos);
            $this->assign("grade_info", $gradeInfos);
            $this->assign("admin_info", $adminInfos);
            $this->assign("page", $page->show());
            $this->display();
        } else {
            foreach ($userInfos as $kk=>$vv) {
                $userInfos[$kk]['hash']=md5($vv['user_id']).":".$vv['user_id'];
            }
            $data=array(
                "user_info"=>$userInfos,
                "grade_info"=>$gradeInfos,
                "admin_info"=>$adminInfos,
                "page"=>$page->show()
            );
            exit(json_encode(array("code"=>10123,"msg"=>$data)));
        }
    }
    public function saveStatus()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10127,'msg'=>"非法操作")));
        } else {
            $userData=I("post.userData");
            $dataValue=I("post.dataValue", '', 'intval');
            if (!is_numeric($dataValue)||$dataValue<1||$dataValue>4) {
                exit(json_encode(array("code"=>10127,'msg'=>"非法操作")));
            }
            $res=D("User")->saveUserStatus($userData, $dataValue);
            if (false!==$res) {
                exit(json_encode(array("code"=>10123,'msg'=>"更新成功")));
            } else {
                exit(json_encode(array("code"=>10128,'msg'=>"更新失败，请稍后重试...")));
            }
        }
    }
    public function view()
    {
        $id=I("get.id");
        $orderModel=D("Order");
        $userInfo=D("User")->getUserInfoByUserId($id);
        $userInfo['grade']=D('Grade')->getGradeNameById($userInfo['grade_id']);
        $companyInfo=D("Company")->getCompanyInfoByUserId($id);
        $orderCollectInfo=$orderModel->getCollectOrderInfoByUserId($id);
        $operationInfo=D("Admin")->getAdminInfoById($userInfo['admin_id']);

        $where=setOrderWhere();
        if (!is_array($where)) {
            $where=array();
        }
        $where['user_id']=$id;
        $count=M("Order")->where($where)->count();
        $page=new \Think\Page($count, 10);
        $orderInfos=$orderModel->getAllOrdersByuserId($page, $where, $id);
        if (!IS_POST) {
            $this->assign("order_info", $orderInfos);
            $this->assign("operation_info", $operationInfo);
            $this->assign("order_collect_info", $orderCollectInfo);
            $this->assign("user_info", $userInfo);
            $this->assign("company_info", $companyInfo);
            $this->assign("page", $page->show());
            $this->assign("id", $id);
            $this->display();
        } else {
            foreach ($orderInfos as $k=>$v) {//添加链接,以应对URL模式调整

                //上次操作时间

                $orderInfos[$k]['prev_time']=date("Y-m-d", $v['order_prev_time']);

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
    public function edit()
    {
        $id=I("get.id");
        $orderModel=D("Order");
        $userInfo=D("User")->getUserInfoByUserId($id);
        $companyInfo=D("Company")->getCompanyInfoByUserId($id);
        $countryInfo=M("Country")->select();
        $orderCollectInfo=$orderModel->getCollectOrderInfoByUserId($id);
        $gradeInfo=D("Grade")->getAllGrade();
        $roleIds=D("GroupRole")->getRoleIdByGroupId(2);
        $adminIds=D("RoleAdmin")->getAdminIdByRoleId($roleIds);
        $adminInfos=D("Admin")->getAdminInfosByAdminIds($adminIds);

        $where=setOrderWhere();
        if (!is_array($where)) {
            $where=array();
        }
        $where['user_id']=$id;
        $count=M("Order")->where($where)->count();
        $page=new \Think\Page($count, 10);
        $orderInfos=$orderModel->getAllOrdersByuserId($page, $where, $id);
        if (!IS_POST) {
            $this->assign("contract_time", convertTimestampToDate($userInfo['contract_time']));
            $this->assign("admin_infos", $adminInfos);
            $this->assign("grade_info", $gradeInfo);
            $this->assign("order_collect_info", $orderCollectInfo);
            $this->assign("order_info", $orderInfos);
            $this->assign("country_info", $countryInfo);
            $this->assign("user_info", $userInfo);
            $this->assign("company_info", $companyInfo);
            $this->assign("page", $page->show());
            $this->assign("id", $id);
            $this->display();
        } else {
            foreach ($orderInfos as $k=>$v) {
                //添加链接,以应对URL模式调整
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
    public function del()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $id=I("post.id");
            $where=array();
            $where['user_id']=$id;
            $userRes=M("User")->where($where)->delete();

            if ($userRes!==false) {
                $companyRes=M("Company")->where($where)->delete();
                $findPasswordRes=M("Find_password")->where($where)->delete();
                $forworderRes=M("Forworder")->where($where)->delete();
                $invoiceRes=M("Invoice")->where($where)->delete();
                $logisticsRes=M("Logistics")->where($where)->delete();
                $messageRes=M("Message")
                            ->join("left join __ATTACH__ on __ATTACH__.msg_id=__MESSAGR__.message_id")
                            ->where(array("www_message.msg_to"=>$id))
                            ->delete();
                $userProductRes=M("User_product")->where($where)->delete();
                exit(json_encode(array("code"=>10029,"msg"=>"删除成功")));
            } else {
                exit(json_encode(array("code"=>10030,"msg"=>"删除失败")));
            }
        }
    }
    public function customerSave()
    {

        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $userCompany=I("post.userCompany");
            $userData=array(
                "user_email"=>$userCompany["contactsEmail"],
                "account_status_id"=>$userCompany["accountStatus"],
                "default_currency"=>$userCompany["defaultCurrency"],
                "grade_id"=>$userCompany["grade"],
                "admin_id"=>$userCompany["operator"],
                "contract_time"=>convertDateToTimestamp($userCompany["year"], $userCompany["month"], $userCompany["day"], $userCompany["hour"], $userCompany["minute"], $userCompany["second"]),
                "is_deal"=>2,
                "tianxin_code"=>$userCompany["tianxinCode"],
                "discount"=>$userCompany["discount"],
                "start_time"=>$userCompany["start_time"],
                "end_time"=>$userCompany["end_time"]
            );
            $userId=$userCompany["userId"];
            $res=M("User")->where(array("user_id"=>$userId))->save($userData);
            $areaModel=D("Area");
            $companyData=array(
                "company_name"=>$userCompany["companyName"],
                "company_website"=>$userCompany["companyWebsit"],
                "company_postalcode"=>$userCompany["companyPostalcode"],
                "company_area"=>M("Country")->where(array("country_id"=>$userCompany["companyArea"]))->getField("country_name"),
                "company_dist"=>'',
                "company_address"=>$userCompany["address"],
                "company_contacts"=>$userCompany["contacts"],
                "company_phone"=>$userCompany["contactsPhone"]
            );
            if ($userCompany["companyArea"]==1) {
                $companyData['company_province']=$areaModel->getParentAreaNameByAreaId($userCompany["city"]);
                $companyData['company_city']=$areaModel->getAreaNameByAreaId($userCompany["city"]);
            } else {
                $companyData['company_province']=$userCompany["prov"];
                $companyData['company_city']=$userCompany["city"];
            }
            $aa=M("Company")->where(array("user_id"=>$userId))->select();
            //如果输入没有company则创建
            if (count($aa)==0) {
                $companyData['user_id']=$userCompany["userId"];
                M("Company")->where(array("user_id"=>$userId))->add($companyData);
            } else {
                $res2=M("Company")->where(array("user_id"=>$userId))->save($companyData);
            }
            exit(json_encode(array("code"=>10029,"msg"=>"保存成功",'url'=>U('view', array('id'=>$userCompany["userId"])))));
        }
    }
    public function batchSet()
    {
        $id=I("get.id");
        $searchModel=I("post.searchModel");
        $where=array();
        if (empty($searchModel)) {
            $where="1=1";
        } else {
            $where['model_name']=array("like","%".$searchModel."%");
        }
        $specInfo=D("Specification")->getAllSpecification($where);
        $userInfo=D("User")->getUserInfoByUserId($id);
        $userProduct=D("UserProduct")->getUserProductInfoByUserId($id);
        $afterAdjustUser=D("Specification", "Logic")->adjustSpecificationByUserProduct($specInfo, $userProduct, $id);
        $groupSpec=array();
        foreach ($afterAdjustUser as $k=>$v) {
            $checkKey=$v['specification_id'].":".rand(7, 31).":".$id.":".$v['upp_id'];
            $v["_hashKey"]=md5($checkKey).":".$checkKey;
            $v["m5_model"]=md5($v["model_name"]);
            $groupSpec["s".$v['serial_id']][]=$v;
        }
        if (!IS_POST) {
            $this->assign("spec_info", $groupSpec);
            $this->assign("user_info", $userInfo);
            $this->display();
        } else {
            exit(json_encode(array("code"=>10104,"msg"=>array("spec_info"=>$groupSpec,"user_info"=>$userInfo))));
        }
    }
    public function batchSetSave()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $gps=I("post.gps");
            D("UserProduct")->saveUserProduct($gps);
            exit(json_encode(array("code"=>10104,"msg"=>"保存数据成功")));
        }
    }
    public function editPassword()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $id=I("post.id");
            $pwd=I("post.pwd");
            // if(strlen($pwd)<6||strlen($pwd)>12||preg_match("/^\d+$/",$pwd)||preg_match("/^[a-zA-Z]+$/",$pwd)){
            // 	exit(json_encode(array("code"=>10022,"msg"=>"密码密码必须是6-12位数字和英文混合")));
            // }

            $where=array();
            $where['user_id']=$id;

            $userInfo=M("User")->where($where)->find();
            $strKey=substr(md5($userInfo['user_verify']), 6, 6);
            $pwd=md5($strKey.$userInfo['user_uid'].$pwd);

            $data=array(
                "user_password"=>$pwd
            );
            $res=M("User")->where($where)->save($data);
            if ($res!==false) {
                exit(json_encode(array("code"=>10104,"msg"=>"修改密码成功")));
            } else {
                exit(json_encode(array("code"=>10105,"msg"=>"修改密码失败")));
            }
        }
    }
}
