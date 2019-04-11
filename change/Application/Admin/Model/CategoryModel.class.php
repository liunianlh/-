<?php
namespace Admin\Model;
use Think\Model;
class CategoryModel extends Model {
    public function getAllCategory(){
		$info=array();
		$info=$this->select();
		return $info;
	}
	public function getAllSerials(){
		$info=array();
		$info=$this->where(array("cat_pid"=>0,"cat_level"=>1))->select();
		return $info;
	}
	public function getAllSmallJoints(){
		$info=array();
		$info=$this->where(array("cat_level"=>3))->select();
		return $info;
	}
	public function getAllBigJoints(){
		$info=array();
		$info=$this->where(array("cat_level"=>2))->select();
		return $info;
	}
	public function getSerialBySerialId($serialId){
		$info=array();
		$info=$this->where(array("category_id"=>$serialId))->find();
		return $info;
	}
	public function getAllSmallJointsBySerialId($serialId){
		$info=array();
		$bigJoints=$this->getAllBigJointsBySerialId($serialId);
		foreach($bigJoints as $key=>$value){
			$info[]=$smallJoints=$this->getAllChildrenByParentId($value['category_id'],3);
		}
		$sJoints=array();
		if(!empty($info)){
			foreach($info as $k=>$v){
				$sJoints=array_merge($sJoints,$v);
			}
		}
		return $sJoints;
	}
	public function getAllBigJointsBySerialId($serialId){
		$info=array();
		$info=$this->getAllChildrenByParentId($serialId,2);
		return $info;
	}
	public function getAllChildrenByParentId($parentId=0,$cat_level=0){
		$info=array();
		$where=array();
		$where['cat_pid']=$parentId;
		if(!empty($cat_level)){
			$where['cat_level']=$cat_level;
		}
		$info=$this->where($where)->select();
		return $info;
	}
	public function getCategoryNameById($categoryId){
		$info=$this->getSerialBySerialId($categoryId);
		$name='';
		if(!empty($info)){
			$name=$info['cat_chinese_name'];
		}
		return $name;
	}
	public function getCategoryName2ById($categoryId){
		$info=$this->getSerialBySerialId($categoryId);
		$name='';
		if(!empty($info)){
			$name=$info['cat_english_name'];
		}
		return $name;
	}
	public function getCategoryNameFromCategoryInfoById($categoryInfo,$categoryId){
		$categoryName="";
		if(empty($categoryId)){//直接返回
			$categoryName="--";
			return $categoryName;
		}
		foreach($categoryInfo as $key=>$value){
			if($value['category_id']==$categoryId){
				$categoryName=$value['cat_chinese_name'];
				break;
			}
		}
		return $categoryName;
	}
	public function checkCategory($categoryName,$catPid){
		$where=array();
		$where["cat_chinese_name"]=$categoryName;
		$where["cat_pid"]=$catPid;
		$count=$this->where($where)->count();
		return $count;
	}
	public function getCatLevelByCatPid($catPid){
		$catLevel=1;
		if(empty($catPid)){
			return $catLevel;
		}
		$catPLevel=$this->where(array("category_id"=>$catPid))->getField("cat_level");
		$catLevel=$catPLevel+1;
		return $catLevel;
	}
}