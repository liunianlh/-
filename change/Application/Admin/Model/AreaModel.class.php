<?php
namespace Admin\Model;
use Think\Model;
class AreaModel extends Model {
    public function getSpecificateAreasByLevel($level){
		$info=$this->where(array("area_deep"=>$level))->select();
		return $info;
	}
	public function getDistInfoByCityId($cityId){
		$info=$this->where(array("area_parent_id"=>$cityId))->select();
		return $info;
	}
	public function getAreaNameByAreaId($areaId){
		$info=$this->getAreaInfoByAreaId($areaId);
		return $info['area_name'];
	}
	public function getParentAreaNameByAreaId($areaId){
		$info=$this->getAreaInfoByAreaId($areaId);
		return $this->getAreaNameByAreaId($info['area_parent_id']);
	}
	public function getAreaInfoByAreaId($areaId){
		$info=$this->where(array("area_id"=>$areaId))->find();
		return $info;
	}
	public function getAllArea(){
		$regionArray=array(
			"华北","华东","华南","华中","东北","西南","西北","港澳台"
		);
		$areaArray=array();
		foreach($regionArray as $key=>$value){
			$areaArray["area".$key]=$this->where(array("area_region"=>$value))->select();
		}
		return $areaArray;
	}
}