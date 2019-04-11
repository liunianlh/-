<?php
namespace Admin\Model;
use Think\Model;
class CnModel extends Model {
    public function getAllText(){
		$info=array();
		$info=$this->select();
		return $info;
	}
	public function getTextInfoById($cnId){
		$info=array();
		$info=$this->where(array("cn_id"=>$cnId))->find();
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
	public function checkColorByName($colorName){
		$count=$this->where(array("color_chinese_name"=>$colorName))->count();
		return $count;
	}
}