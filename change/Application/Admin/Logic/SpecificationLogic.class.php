<?php
namespace Admin\Logic;
use Think\Model;
class SpecificationLogic extends Model {
	public function adjustSpecification($specInfo,$groupGrade){//用于校准数组
		$afterAdjustGroupGrade=array();
		$flag=false;
		$assumeValue=array(//模拟一条数据
			"gps_id"=>0,
			"specification_id"=>0,
			"rmb"=>'0.00',
			"products_status_id"=>2
		);
		foreach($specInfo as $k=>$v){
			foreach($groupGrade as $kk=>$vv){
				$flag=false;
				foreach($vv['value'] as $kkk=>$vvv){
					if($vvv['specification_id']==$v['specification_id']){
						$vvv['serial_id']=$v['serial_id'];
						$afterAdjustGroupGrade[$kk]['value'][]=$vvv;
						$afterAdjustGroupGrade[$kk]['name']=$vv['name'];
						$afterAdjustGroupGrade[$kk]['gradeId']=$vvv['grade_id'];
						$flag=true;
						break;
					}
				}
				if($flag===false){
					$assumeValue['specification_id']=$v['serial_id'];
					$assumeValue['specification_id']=$v['specification_id'];
					$assumeValue['grade_id']=str_replace("g","",$kk);
					$afterAdjustGroupGrade[$kk]['value'][]=$assumeValue;
					$afterAdjustGroupGrade[$kk]['name']=$vv['name'];
					$afterAdjustGroupGrade[$kk]['gradeId']=str_replace("g","",$kk);
				}
			}
		}
		return $afterAdjustGroupGrade;
	}
	
	public function adjustSpecificationByUserProduct($specInfo,$userProduct,$id){
		
		//$userId=$userProduct[0]['user_id'];
		if(!empty($id)){
			$gradeId=M("User")->where(array("user_id"=>$id))->getField("grade_id");
			$gpsInfo=M("Grade_product")->where(array("grade_id"=>$gradeId))->select();
		}
		
		foreach($specInfo as $key=>$value){
			$flag=false;
			foreach($userProduct as $k=>$v){
				if($value['specification_id']==$v['specification_id']){
					$flag=true;
					$specInfo[$key]['rmb']=$v['rmb'];
					$specInfo[$key]['status']=$v['products_status_id'];
					$specInfo[$key]['upp_id']=$v['upp_id'];
					break;
				}
			}
			
			//用户没有被授权过
			if($flag===false){
				$flag2=false;
				
				//读取用户等级状态
				foreach($gpsInfo as $kk=>$vv){
					if($value['specification_id']==$vv['specification_id']){
						$flag2=true;
						$specInfo[$key]['rmb']=$vv['rmb'];
						$specInfo[$key]['status']=$vv['products_status_id'];
						break;
					}
				}
				
				//等级也没有授权情况？
				if($flag2===false){
					$specInfo[$key]['rmb']="0.00";
					$specInfo[$key]['status']=2;
				}
				$specInfo[$key]['upp_id']=0;
			}
		}
		return $specInfo;
	}
	
	public function getAllSpecification($userId){
		$userInfo=D("User")->getUserInfoByUserId($userId);
		$gradeId=$userInfo["grade_id"];
		
		$specInfo=D("Specification")->getAllSpecification2();
		$userProduct=D("UserProduct")->getUserProductInfoByUserId($userId);
		$gradeProduct=D("GradeProduct")->getAllGradeProductByGradeId($gradeId);
		
		$uppLogic=D("UserProduct","Logic");
		$gpsLogic=D("GradeProduct","Logic");
		$productModel=D("Products");
		
		$_temp=array();
		foreach($specInfo as $key=>$value){
			$uppInfo=$uppLogic->getUPByUserIdAndSpecId($userProduct,$userId,$value['specification_id']);
			if(empty($uppInfo)||(floatval($uppInfo['rmb'])<=0)){
				$gpsInfo=$gpsLogic->getGPByGradeIdAndSpecId($gradeProduct,$gradeId,$value['specification_id']);
				if(empty($gpsInfo)||(floatval($gpsInfo['rmb'])<=0)){
					$specInfo[$key]['rmb']=0;
				}else{
					if($gpsInfo['products_status_id']==2){
						unset($specInfo[$key]);
						continue;
					}else{
						$specInfo[$key]['rmb']=$gpsInfo['rmb'];
					}
				}
			}else{
				if($uppInfo['products_status_id']==2){
					unset($specInfo[$key]);
					continue;
				}else{
					$specInfo[$key]['rmb']=$uppInfo['rmb'];
				}
			}
			// if(empty($_temp[md5($specInfo['products_id'])])){
				// $productInfo=$productModel->getOnlyProductInfoById($specInfo['products_id']);
				// $_temp[md5($specInfo['products_id'])]=$productInfo;//临时缓存
			// }else{
				// $productInfo=$_temp[md5($specInfo['products_id'])];
			// }
			// $specInfo[$key]['products_img']=$productInfo['products_img'];
			// $specInfo[$key]['products_chinese_name']=$productInfo['products_chinese_name'];
			// $specInfo[$key]['products_english_name']=$productInfo['products_english_name'];
		}
		return $specInfo;
	}
}