<?php
namespace Admin\Controller;
use Think\Controller;
class LogisticsController extends BaseController {
    public function index(){
		$logisticsModel=D("LogisticsTpl");
		$logisticsTplInfo=$logisticsModel->getAllLogisticsTpl();
		foreach($logisticsTplInfo as $key=>$value){
			$logistics=$logisticsModel->getAllLogisticsTplInfosByFlag($value['logistics_tpl_flag']);
			$logisticsTplInfo[$key]['value']=$logistics;
		}
		
		//默认物流
		$default=0;
		$defaultInfo=M("Logistics_company")->where(array('logistics_default'=>2))->find();
		if(!empty($defaultInfo)){
			$default=$defaultInfo['logistics_company_id'];
		}
		$this->assign("logistics",$logisticsTplInfo);
		$this->assign("default",$default);
		$this->display();
	}
	public function add(){
		if(!IS_POST){
			$name=I("get.name");
			if(!empty($name)){
				$logisticsTplInfo=D("LogisticsTpl")->getAllLogisticsTplInfosByFlag($name);
				$this->assign("logistics_info",$logisticsTplInfo);
			}
			$logisticsCompany=M("Logistics_company")->select();
			$areaInfo=D("Area")->getAllArea();
			$this->assign("area_info",$areaInfo);
			$this->assign("logistics_company",$logisticsCompany);
			$this->display();
		}else{
			$logisticsData=I("post.logisticsData");
			$logisticsTplName=$logisticsData['logisticsTplName'];
			$transportArea=$logisticsData['transportArea'];
			$priceWay=$logisticsData['priceWay'];
			$transCurrency=$logisticsData['transCurrency'];
			$firstWeight=$logisticsData['firstWeight'];
			$firstFee=$logisticsData['firstFee'];
			$secondWeight=$logisticsData['secondWeight'];
			$secondFee=$logisticsData['secondFee'];
			
			$id=intval($logisticsData['id']);
			$data=array(
				"logistics_tpl_flag"=>$logisticsTplName,
				"logistics_tpl_name"=>D("LogisticsCompany")->getLogisticsCompanyById($logisticsTplName),
				"logistics_tpl_area"=>empty($transportArea)?"默认地区":$transportArea,
				"logistics_tpl_price_way"=>$priceWay,
				"logistics_tpl_currency"=>$transCurrency,
				"logistics_tpl_first_weight"=>$firstWeight,
				"logistics_tpl_first_fee"=>$firstFee,
				"logistics_tpl_second_weight"=>$secondWeight,
				"logistics_tpl_second_fee"=>$secondFee,
				"logistics_time"=>time()
			);
			if(empty($id)){
				$logisticsId=M("Logistics_tpl")->add($data);
				exit(json_encode(array("code"=>10090,"msg"=>"操作成功","url"=>U('Logistics/add',array('name'=>$logisticsTplName)))));
			}else{
				$where=array();
				$where['logistics_tpl_id']=$id;
				M("Logistics_tpl")->where($where)->save($data);
				exit(json_encode(array("code"=>10090,"msg"=>"操作成功","url"=>U('Logistics/add',array('name'=>$logisticsTplName)))));
			}
		}
		
	}
	public function del(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10090,"msg"=>"非法操作")));
		}else{
			$logisticsFlag=I("get.name");
			$where=array(
				"logistics_tpl_flag"=>$logisticsFlag
			);
			$res=M("Logistics_tpl")->where($where)->delete();
			exit(json_encode(array("code"=>10091,"msg"=>"操作成功")));
		}
	}
	public function tplDel(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10090,"msg"=>"非法操作")));
		}else{
			$logisticsId=I("get.id");
			$where=array(
				"logistics_tpl_id"=>$logisticsId
			);
			$res=M("Logistics_tpl")->where($where)->delete();
			exit(json_encode(array("code"=>10091,"msg"=>"操作成功")));
		}
	}
	public function setDefault(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10090,"msg"=>"非法操作")));
		}else{
			$id=I("post.id");
			$where=array(
				"logistics_company_id"=>$id
			);
			$where2=array(
				"logistics_default"=>2
			);
			$res=M("Logistics_company")->where($where2)->save(array('logistics_default'=>1));
			$res2=M("Logistics_company")->where($where)->save(array('logistics_default'=>2));
			exit(json_encode(array("code"=>10091,"msg"=>"操作成功")));
		}
	}
	public function tplEdit(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10090,"msg"=>"非法操作")));
		}else{
			$logisticsId=I("get.id");
			$where=array(
				"logistics_tpl_id"=>$logisticsId
			);
			$logisticsInfo=M("Logistics_tpl")->where($where)->find();
			exit(json_encode(array("code"=>10091,"msg"=>$logisticsInfo)));
		}
	}
}