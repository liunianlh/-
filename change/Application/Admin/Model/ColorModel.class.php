<?php
namespace Admin\Model;
use Think\Model;
class ColorModel extends Model {
    public function getAllColor(){
		$info=array();
		$info=$this->select();
		return $info;
	}
	public function getColorInfoById($colorId){
		$info=array();
		$info=$this->where(array("color_id"=>$colorId))->find();
		return $info;
	}
	public function getColorNameById($colorId){
		$info=$this->getColorInfoById($colorId);
		$name='';
		if(!empty($info)){
			$name=$info['color_chinese_name'];
		}
		return $name;
	}
	public function getColorName2ById($colorId){
		$info=$this->getColorInfoById($colorId);
		$name='';
		if(!empty($info)){
			$name=$info['color_english_name'];
		}
		return $name;
	}
	public function checkColorByName($colorName){
		$count=$this->where(array("color_chinese_name"=>$colorName))->count();
		return $count;
	}
}