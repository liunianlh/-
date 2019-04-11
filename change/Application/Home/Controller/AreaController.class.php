<?php
namespace Home\Controller;
use Think\Controller;
class AreaController extends Controller {
    public function getProv($method="post"){
		$provInfo=M("Area")->where(array("area_deep"=>1,"area_parent_id"=>0))->select();
		$langSet=cookie('think_language');
		if(empty($langSet)){
			$langSet="zh-cn";
		}else{
			$langSet=$langSet;
		}
		if(strtolower($langSet)=="zh-cn"){
			$this->langFlag=1;
		}else{
			$this->langFlag=2;
		}
		$select="<select id='prov'>";
		$select.="<option value='0'>-----</option>";
		if($this->langFlag==2){//英文
			$opt="";
			foreach($provInfo as $key=>$value){
				$opt.="<option value='".$value['area_id']."'>".$value['area_name2']."</option>";
			}
		}else{
			$opt="";
			foreach($provInfo as $key=>$value){
				$opt.="<option value='".$value['area_id']."'>".$value['area_name']."</option>";
			}
		}
		$select.=$opt;
		$select.="</select>";
		if($method=="get"){
			return $select;
		}else{
			exit(json_encode(array("code"=>10090,"msg"=>$select)));
		}
	}
	public function getProv2($method="post"){
		$provInfo=M("Area")->where(array("area_deep"=>1,"area_parent_id"=>0))->select();
		$langSet=cookie('think_language');
		if(empty($langSet)){
			$langSet="zh-cn";
		}else{
			$langSet=$langSet;
		}
		if(strtolower($langSet)=="zh-cn"){
			$this->langFlag=1;
		}else{
			$this->langFlag=2;
		}
		$select="<select id='fwdProv'>";
		if($this->langFlag==2){//英文
			$opt="";
			foreach($provInfo as $key=>$value){
				$opt.="<option value='".$value['area_id']."'>".$value['area_name2']."</option>";
			}
		}else{
			$opt="";
			foreach($provInfo as $key=>$value){
				$opt.="<option value='".$value['area_id']."'>".$value['area_name']."</option>";
			}
		}
		$select.=$opt;
		$select.="</select>";
		if($method=="get"){
			return $select;
		}else{
			exit(json_encode(array("code"=>10090,"msg"=>$select)));
		}
	}
	public function getCity2($method="post"){
		$areaId=I("post.areaId");
		if(empty($areaId)){
			$areaId=1;
		}
		$cityInfo=M("Area")->where(array("area_parent_id"=>$areaId))->select();
		$langSet=cookie('think_language');
		if(empty($langSet)){
			$langSet="zh-cn";
		}else{
			$langSet=$langSet;
		}
		if(strtolower($langSet)=="zh-cn"){
			$this->langFlag=1;
		}else{
			$this->langFlag=2;
		}
		$select="<select id='city'>";
		$select.="<option value='0'>-----</option>";
		if($this->langFlag==2){//英文
			$opt="";
			foreach($cityInfo as $key=>$value){
				$opt.="<option value='".$value['area_id']."'>".$value['area_name2']."</option>";
			}
		}else{
			$opt="";
			foreach($cityInfo as $key=>$value){
				$opt.="<option value='".$value['area_id']."'>".$value['area_name']."</option>";
			}
		}
		$select.=$opt;
		$select.="</select>";
		if($method=="get"){
			return $select;
		}else{
			exit(json_encode(array("code"=>10090,"msg"=>$select)));
		}
	}
	public function getCity3($method="post"){
		$areaId=I("post.areaId");
		if(empty($areaId)){
			$areaId=1;
		}
		$cityInfo=M("Area")->where(array("area_parent_id"=>$areaId))->select();
		$langSet=cookie('think_language');
		if(empty($langSet)){
			$langSet="zh-cn";
		}else{
			$langSet=$langSet;
		}
		if(strtolower($langSet)=="zh-cn"){
			$this->langFlag=1;
		}else{
			$this->langFlag=2;
		}
		$select="<select id='fwdCity'>";
		if($this->langFlag==2){//英文
			$opt="";
			foreach($cityInfo as $key=>$value){
				$opt.="<option value='".$value['area_id']."'>".$value['area_name2']."</option>";
			}
		}else{
			$opt="";
			foreach($cityInfo as $key=>$value){
				$opt.="<option value='".$value['area_id']."'>".$value['area_name']."</option>";
			}
		}
		$select.=$opt;
		$select.="</select>";
		if($method=="get"){
			return $select;
		}else{
			exit(json_encode(array("code"=>10090,"msg"=>$select)));
		}
	}
	public function switchAddr(){
		$countryId=I("post.countryId");
		if($countryId==1){//1为中国
			$htl1=$this->getProv("get");
			$htl2=$this->getCity2("get");
		}else{
			$htl1='<input id="prov" placeholder="'.L('_PUBLIC_PROVINCE_').'"/>';
			$htl2='<input id="city" placeholder="'.L('_PUBLIC_CITY_').'"/>';
		}
		exit(json_encode(array("code"=>10090,"msg"=>$htl1." ".$htl2)));
	}
	public function switchAddr2(){
		$countryId=I("post.countryId");
		if($countryId==1){//1为中国
			$htl1=$this->getProv2("get");
			$htl2=$this->getCity3("get");
		}else{
			$htl1='<input id="fwdProv" placeholder="'.L('_PUBLIC_PROVINCE_').'"/>';
			$htl2='<input id="fwdCity" placeholder="'.L('_PUBLIC_CITY_').'"/>';
		}
		exit(json_encode(array("code"=>10090,"msg"=>$htl1." ".$htl2)));
	}
}