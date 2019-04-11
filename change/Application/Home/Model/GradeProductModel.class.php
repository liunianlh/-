<?php
namespace Home\Model;
use Think\Model;
class GradeProductModel extends Model {
    public function getGradeProductInfoBySG($specId,$gradeId){// SG===specification_id  grade_id
		$where=array();
		$where['specification_id']=$specId;
		$where['grade_id']=$gradeId;
		$gradeProduct=$this->where($where)->find();
		return $gradeProduct;
	}
}