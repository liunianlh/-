<?php
namespace Admin\Logic;
use Think\Model;
class GradeProductLogic extends Model {
    public function getGPByGradeId($gpsInfo,$gradeId){
		$GP=array("rmb"=>0.00,"products_status_id"=>1);
		foreach($gpsInfo as $key=>$value){
			if($value['grade_id']==$gradeId){
				$GP['rmb']=$value['rmb'];
				$GP['products_status_id']=$value['products_status_id'];
				break;
			}
		}
		return $GP;
	}
	public function getGPByGradeIdAndSpecId($gpsInfo,$gradeId,$specId){
		$GP=array("rmb"=>0.00,"products_status_id"=>1);
		foreach($gpsInfo as $key=>$value){
			if(($value['grade_id']==$gradeId)&&($value['specification_id']==$specId)){
				$GP['rmb']=$value['rmb'];
				$GP['products_status_id']=$value['products_status_id'];
				break;
			}
		}
		return $GP;
	}
}