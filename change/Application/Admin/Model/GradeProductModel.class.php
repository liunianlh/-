<?php
namespace Admin\Model;
use Think\Model;
class GradeProductModel extends Model {
    public function addOrSaveProduct($specId,$data,$post){
		if(!empty($data['rmb1'])){
			$grade=$post['grade'];
			$data=array(
				"rmb"=>$data['rmb1'],
				"products_status_id"=>$data['status1']
			);
			$gpsId=$this->isExists($grade,$specId);
			if(!$gpsId){
				$data["grade_id"]=$grade;
				$data["specification_id"]=$specId;
				$gpsId=$this->add($data);
			}else{
				$res=$this->where(array("grade_id"=>$grade,"specification_id"=>$specId))->save($data);
			}
			return $gpsId;
		}else{
			return false;
		}
	}
	public function isExists($grade,$specId){
		$info=$this->where(array("grade_id"=>$grade,"specification_id"=>$specId))->find();
		if(!empty($info)){
			return $info['gps_id'];
		}else{
			return false;
		}
	}
	public function getAllPricesBySpecificationId($specId){
		$uppInfo=$this->where(array("specification_id"=>$specId))->select();
		return $uppInfo;
	}
	public function delGradeProductBySpecificationId($specId){
		$res=$this->where(array("specification_id"=>$specId))->delete();
		return $res;
	}
	public function getAllGradeProduct(){
		$gradeInfo=$this->order("grade_id asc")->select();
		return $gradeInfo;
	}
	public function groupGradeProduct($gradeInfo){
		$group=array();
		$keys=array();
		$gradeModel=D("Grade");
		$grades=$gradeModel->getAllGrade();
		foreach($gradeInfo as $key=>$value){
			$gk="g".$value['grade_id'];
			$group[$gk]['value'][]=$value;
			if(empty($group[$gk]['name'])){// 减少执行次数，提高速度
				$group[$gk]['name']=$gradeModel->getGradeName($grades,$value['grade_id']);
			}
			$keys[]=$gk;
		}
		$assumeValue=array(//模拟一条数据
			"gps_id"=>0,
			"specification_id"=>0,
			"rmb"=>500000,
			"products_status_id"=>2
		);
		
		foreach($grades as $k=>$v){
			$gk2="g".$v['grade_id'];
			if(false===array_search($gk2,$keys)){
				$assumeValue['grade_id']=$v['grade_id'];
				$group[$gk2]['value'][]=$assumeValue;
				$group[$gk2]['name']=$v['gr_chinese_name'];
				$keys[]=$gk2;
			}
		}
		return $group;
	}
	public function saveGradeProduct($gradeProduct){
		foreach($gradeProduct as $k=>$v){
			$hashArray=explode(":",$v['hash']);
			if($hashArray[0]==md5($hashArray[1].":".$hashArray[2].":".$hashArray[3])){//校验
				$data=array(
					"rmb"=>$v['rmb'],
					"products_status_id"=>$v['status']
				);
				if(empty($hashArray[1])){//添加数据
					$data['grade_id']=$hashArray[2];
					$data['specification_id']=$hashArray[3];
					$this->add($data);
				}else{
					$res=$this->where(array("gps_id"=>$hashArray[1]))->save($data);
				}
			}
		}
	}
	public function getAllGradeProductByGradeId($gradeId){
		return $this->where(array("grade_id"=>$gradeId))->select();
	}
	public function getGradeProductInfoBySGId($specId,$gradeId){
		$where=array();
		$where['specification_id']=$specId;
		$where['grade_id']=$gradeId;
		return $this->where($where)->find();
	}
}