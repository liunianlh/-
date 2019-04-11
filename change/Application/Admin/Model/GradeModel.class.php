<?php
namespace Admin\Model;
use Think\Model;
class GradeModel extends Model {
    public function getAllGrade(){
		$info=array();
		$info=$this->select();
		return $info;
	}
	public function getGradeName($grades,$gradeId){
		$name='';
		foreach($grades as $key=>$value){
			if($value['grade_id']==$gradeId){
				$name=$value['gr_chinese_name'];
				break;
			}
		}
		return $name;
	}
	public function getGradeNameById($gradeId){
		$gradeInfo=$this->where(array("grade_id"=>$gradeId))->find();
		return $gradeInfo['gr_chinese_name'];
	}
	public function checkGradeByName($gradeName){
		$count=$this->where(array("gr_chinese_name"=>$gradeName))->count();
		return $count;
	}
	public function getGradeInfoById($gradeId){
		$info=array();
		$info=$this->where(array("grade_id"=>$gradeId))->find();
		return $info;
	}
}