<?php
namespace Admin\Logic;
use Think\Model;
class CategoryLogic extends Model {
	public function getProductInfoByUserAndGrade($categoryInfo,$serialId){
		$categoryArray=array();
		$bigJointArray=array();
		foreach($categoryInfo as $key=>$value){
			if($value['cat_pid']==$serialId){
				$bigJointArray[]=$value['category_id'];
				unset($categoryInfo[$key]);
			}
		}
		foreach($categoryInfo as $k=>$v){
			if(false!==array_search($v['cat_pid'],$bigJointArray)){
				$categoryArray[]=$v;
			}
		}
		return $categoryArray;
	}
}