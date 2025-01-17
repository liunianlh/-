<?php
namespace Home\Controller;
class ForworderController extends BaseController {
    public function index(){
		if(!IS_POST){
			$forworderInfo=D("Admin/Forworder")->getAllForworderByUserId($_SESSION['userid']);
			$shipmentModel=D("Admin/Shipment");
			foreach($forworderInfo as $key=>$value){
				$shipmentInfo=$shipmentModel->getShipmentInfoByShipmentId($value['shipment_id']);
				if($this->langFlag==2){
					$forworderInfo[$key]['for_shipment']=$shipmentInfo['shi_english_name'];
				}else{
					$forworderInfo[$key]['for_shipment']=$shipmentInfo['shi_chinese_name'];
				}
			}
			$shipmentAllInfos=$shipmentModel->getAllShipment();
			$count=count($forworderInfo);
			
			$countryInfo=M("Country")->select();
			$this->assign("forworder_info",$forworderInfo);
			$this->assign("country_info",$countryInfo);
			$this->assign("language",$this->langFlag);
			$this->assign("shipment_info",$shipmentAllInfos);
			$this->assign("count",$count);
			$this->assign("rest",10-$count);
			$this->display();
		}else{
			$company=I("post.company");
			if(empty($company)){
				exit(json_encode(array("code"=>10070,"msg"=>"公司名称不能为空！")));
			}
			$receiver=I("post.receiver");
			if(empty($receiver)){
				exit(json_encode(array("code"=>10071,"msg"=>"收货人不能为空！")));
			}
			$phone=I("post.phone");
			if(empty($phone)){
				exit(json_encode(array("code"=>10072,"msg"=>"收货人电话不能为空！")));
			}
			$email=I("post.email");
			if(empty($email)){
				exit(json_encode(array("code"=>10073,"msg"=>"收货人邮箱不能为空！")));
			}
			$country=I("post.country");
			$city=I("post.city");
			$prov=I("post.prov");
			$address=I("post.address");
			if(empty($city)||empty($prov)||empty($address)){
				exit(json_encode(array("code"=>10074,"msg"=>"收货人地址不能为空！")));
			}
			$shipment=I("post.shipment");
			if(empty($shipment)){
				exit(json_encode(array("code"=>10075,"msg"=>"出货方式不能为空！")));
			}
			$data=array();
			$data['user_id']=$_SESSION['userid'];
			$data['for_company_name']=$company;
			$data['for_receiver']=$receiver;
			$data['for_receiver_phone']=$phone;
			$data['for_receiver_email']=$email;
			$data['for_country_id']=$country;
			$areaModel=D("Admin/Area");
			if($this->langFlag==1){
				$data['for_country']=M("Country")->where(array("country_id"=>$country))->getField("country_name");
			}else{
				$data['for_country']=M("Country")->where(array("country_id"=>$country))->getField("country_name2");
			}
			if($country==1){
				if($this->langFlag==1){
					$data['for_province']=$areaModel->getParentAreaNameByAreaId($city);
					$data['for_city']=$areaModel->getAreaNameByAreaId($city);
				}else{
					$data['for_province']=M("Area")->where(array("area_id"=>$prov))->getField("area_name2");;
					$data['for_city']=M("Area")->where(array("area_id"=>$city))->getField("area_name2");;
				}
				
			}else{
				$data['for_province']=$prov;
				$data['for_city']=$city;
			}
			$data['for_dist']='';
			$data['for_address']=$address;
			$data['shipment_id']=$shipment;
			$dataForworder=I("post.data_forworder");
			if(empty($dataForworder)){
				$data['for_add_time']=time();
				$savedNumber=D("Admin/Forworder")->getForworderCountByUserId($_SESSION["userid"]);
				if($savedNumber>=10){
					exit(json_encode(array("code"=>10076,"msg"=>"最多保存10条！")));
				}
				$forworderId=M("Forworder")->add($data);
				if($forworderId){
					exit(json_encode(array("code"=>10077,"msg"=>"添加成功")));
				}else{
					exit(json_encode(array("code"=>10078,"msg"=>"添加失败")));
				}
			}else{
				$data['for_edit_time']=time();
				$checkKey=session("checkKey");
				if($checkKey!=md5($dataForworder.$_SESSION["userid"])){
					exit(json_encode(array("code"=>10079,"msg"=>"非法操作")));
				}else{
					$res=M("Forworder")->where(array("forworder_id"=>$dataForworder))->save($data);
					if($res!==false){
						exit(json_encode(array("code"=>10080,"msg"=>"更新成功")));
					}else{
						exit(json_encode(array("code"=>10081,"msg"=>"更新失败，请稍后重试...")));
					}
				}
			}
		}
	}
	public function edit(){
		$key=I("post.key");
		$id=I("get.id");
		if($key!=md5($id.$_SESSION["userid"])){
			exit(json_encode(array("code"=>10079,"msg"=>"非法操作")));
		}
		$info=D("Admin/Forworder")->getForworderInfo($_SESSION["userid"],$id);
		session("checkKey",$key);
		exit(json_encode(array("code"=>10082,"msg"=>$info)));
	}
	public function delA(){
		$key=I("post.key");
		$id=I("get.id");
		if($key!=md5($id.$_SESSION["userid"])){
			exit(json_encode(array("code"=>10079,"msg"=>"非法操作")));
		}
		$res=D("Admin/Forworder")->delForworderByForworderId($_SESSION["userid"],$id);
		if(false!==$res){
			exit(json_encode(array("code"=>10083,"msg"=>"删除成功")));
		}else{
			exit(json_encode(array("code"=>10084,"msg"=>"删除失败，请稍后重试...")));
		}
	}
	public function getForworderByForworderId(){
		if(!IS_POST){
			exit(json_encode(array("code"=>5,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$info=D("Admin/Forworder")->getForworderInfo($_SESSION["userid"],$dataId);
			exit(json_encode(array("code"=>6,"msg"=>$info)));
		}
	}
}