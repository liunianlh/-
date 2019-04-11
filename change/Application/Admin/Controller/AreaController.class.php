<?php
namespace Admin\Controller;
use Think\Controller;

class AreaController extends Controller {
    public function getPC(){
		$countryId=I("post.countryId");
		if($countryId==1){//中国
			/************省*************/
			$provInfo=M("Area")->where(array("area_deep"=>1,"area_parent_id"=>0))->select();
			
			$select="<select id='prov'>";
			$opt="";
			foreach($provInfo as $key=>$value){
				$opt.="<option value='".$value['area_id']."'>".$value['area_name']."</option>";
			}
			$select.=$opt;
			$select.="</select>";
			
			/************市*************/
			$areaId=$provInfo[0]['area_id'];
			$cityInfo=M("Area")->where(array("area_parent_id"=>$areaId))->select();
			
			$select2="<select id='city'>";
			$opt2="";
			foreach($cityInfo as $k=>$v){
				$opt2.="<option value='".$v['area_id']."'>".$v['area_name']."</option>";
			}
			$select2.=$opt2;
			$select2.="</select>";
			
			exit(json_encode(array("code"=>10090,"msg"=>$select.$select2)));
		}else{
			$msg='';
			$msg.='<input style="width:39%;" type="text" id="prov" placeholder="州"/>';
			$msg.='<input style="width:39%;margin-left:2%;" type="text" id="city" placeholder="城市"/>';
			exit(json_encode(array("code"=>10090,"msg"=>$msg)));
		}
	}
		
	public function getC(){
		$areaId=I("post.areaId");
		$cityInfo=M("Area")->where(array("area_parent_id"=>$areaId))->select();
		$select="<select id='city'>";
		$opt="";
		foreach($cityInfo as $k=>$v){
			$opt.="<option value='".$v['area_id']."'>".$v['area_name']."</option>";
		}
		$select.=$opt;
		$select.="</select>";
		
		exit(json_encode(array("code"=>10090,"msg"=>$select)));
	}
	
	/************货代信息*************/
	/************货代信息*************/
	/************货代信息*************/
	
	public function getPC2(){
		$countryId=I("post.countryId");
		if($countryId==1){//中国
			/************省*************/
			$provInfo=M("Area")->where(array("area_deep"=>1,"area_parent_id"=>0))->select();
			
			$select="<select id='fwdProv'>";
			$opt="";
			foreach($provInfo as $key=>$value){
				$opt.="<option value='".$value['area_id']."'>".$value['area_name']."</option>";
			}
			$select.=$opt;
			$select.="</select>";
			
			/************市*************/
			$areaId=$provInfo[0]['area_id'];
			$cityInfo=M("Area")->where(array("area_parent_id"=>$areaId))->select();
			
			$select2="<select id='fwdCity'>";
			$opt2="";
			foreach($cityInfo as $k=>$v){
				$opt2.="<option value='".$v['area_id']."'>".$v['area_name']."</option>";
			}
			$select2.=$opt2;
			$select2.="</select>";
			
			exit(json_encode(array("code"=>10090,"msg"=>$select.$select2)));
		}else{
			$msg='';
			$msg.='<input style="width:39%;" type="text" id="fwdProv" placeholder="州"/>';
			$msg.='<input style="width:39%;margin-left:2%;" type="text" id="fwdCity" placeholder="城市"/>';
			exit(json_encode(array("code"=>10090,"msg"=>$msg)));
		}
	}
		
	public function getC2(){
		$areaId=I("post.areaId");
		$cityInfo=M("Area")->where(array("area_parent_id"=>$areaId))->select();
		$select="<select id='fwdCity'>";
		$opt="";
		foreach($cityInfo as $k=>$v){
			$opt.="<option value='".$v['area_id']."'>".$v['area_name']."</option>";
		}
		$select.=$opt;
		$select.="</select>";
		
		exit(json_encode(array("code"=>10090,"msg"=>$select)));
	}
}