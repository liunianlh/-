<?php
namespace Home\Controller;
class AccountController extends BaseController {
    public function index(){
		$userInfo=D("Admin/User")->getUserInfoByUserId($_SESSION['userid']);
		$companyInfo=D("Admin/Company")->getCompanyInfoByUserId($_SESSION['userid']);
		$invoiceInfo=D("Admin/Invoice")->getAllInvoicesByUserId($_SESSION['userid']);
		$forworderInfo=D("Admin/Forworder")->getAllForworderByUserId($_SESSION['userid']);
		$shipmentModel=D("Admin/Shipment");
		foreach($forworderInfo as $key=>$value){
			$shipmentInfo=$shipmentModel->getShipmentInfoByShipmentId($value['shipment_id']);
			$forworderInfo[$key]['for_shipment']=$shipmentInfo['shi_chinese_name'];
		}
		$logisticsInfo=D("Logistics")->getLogisticsInfoByUserId($_SESSION['userid']);
		$this->assign("logistics_info",$logisticsInfo);
		$this->assign("forworder_info",$forworderInfo);
		$this->assign("invoice_info",$invoiceInfo);
		$this->assign("user_info",$userInfo);
		$this->assign("company_info",$companyInfo);
		$this->display();
	}
	public function account(){
		if(!IS_POST){
			$userInfo=D("Admin/User")->getUserInfoByUserId($_SESSION['userid']);
			$companyInfo=D("Admin/Company")->getCompanyInfoByUserId($_SESSION['userid']);
//			var_dump($companyInfo);die;
			$countryInfo=M("Country")->select();
			$this->assign("user_info",$userInfo);
			$this->assign("country_info",$countryInfo);
			$this->assign("company_info",$companyInfo);
			$this->display();
		}else{
			$country=I("post.country");
			$contacts=I("post.contacts");
			$email=I("post.email");
			$phone=I("post.phone");
//			var_dump($_POST);die;
			if(empty($contacts)){
				exit(json_encode(array("code"=>10051,"info"=>"联系人不能为空")));
			}
			if(!preg_match("/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/",$email)){
				exit(json_encode(array("code"=>10021,"info"=>"邮箱格式不正确")));
			}
			if(!preg_match("/^[0-9\-]{7,15}$/",$phone)){
				exit(json_encode(array("code"=>10052,"info"=>"电话格式不正确")));
			}
			$data=array(
				"company_area"=>$country,
				"company_contacts"=>$contacts,
				"company_phone"=>$phone
			);
			$res=M("Company")->where(array("user_id"=>$_SESSION['userid']))->save($data);
			$data2=array(
				"user_email"=>$email
			);
			$res2=M("User")->where(array("user_id"=>$_SESSION['userid']))->save($data2);
			if((false!==$res)&&(false!==$res2)){
				exit(json_encode(array("code"=>10053,"info"=>"修改信息成功","url"=>U('Account/account'))));
			}else{
				exit(json_encode(array("code"=>10054,"info"=>"修改信息失败，请稍后重试...")));
			}
		}
	}
}