<?php
namespace ACH\Controller;
use Think\Controller;
class AreaController extends Controller {
    public function getDist(){
		if(!IS_POST){
			exit;
		}
        $cityId=I("post.city_id");
		if(empty($cityId)){
			exit;
		}
		$distInfo=D("Admin/Area")->getDistInfoByCityId($cityId);
		exit(json_encode(array("code"=>10090,"msg"=>$distInfo)));
	}
	public function getCity(){
		if(!IS_POST){
			exit;
		}
        $areaId=I("post.areaId");
		if(empty($areaId)){
			exit;
		}
		$distInfo=D("Admin/Area")->getDistInfoByCityId($areaId);
		exit(json_encode(array("code"=>10090,"msg"=>$distInfo)));
	}
}