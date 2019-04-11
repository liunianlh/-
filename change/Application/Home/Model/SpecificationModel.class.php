<?php
namespace Home\Model;
use Think\Model;
class SpecificationModel extends Model {
    public function getSpecificationInfoByPLC($productId,$length,$color){//  PLC==  productId length color
		$where=array();
		$where['products_id']=$productId;
		$where['length']=$length;
		$where['color_name|color_name2']=$color;
		$specInfo=$this->where($where)->find();
		return $specInfo;
	}
	public function getProductsInfoBySpecId($specId){
		$where=array();
		if(is_array($specId)){
			$where['specification_id']=array("in",implode(",",$specId));
		}else{
			$where['specification_id']=$specId;
		}
		$specInfo=$this->where($where)->select();
		$products=M("Products");
		$tempProducts=array();//临时存储产品
		foreach($specInfo as $key=>$value){
			$proId=$value['products_id'];
			if(!empty($tempProducts[md5($proId)])){//直接读取缓存数据
				$productInfo=$tempProducts[md5($proId)];
			}else{
				$productInfo=$products->where(array('products_id'=>$proId))->find();
				$tempProducts[md5($proId)]=$productInfo;
			}
			$specInfo[$key]['product']=$productInfo;
		}
		return $specInfo;
	}
	public function getWholeSpecificationInfo($specId,$userId){
		$specInfo=$this->getProductsInfoBySpecId($specId);
		$userProductModel=D("UserProduct");
		$userModel=D("User");
		$gradeProductModel=D("GradeProduct");
		foreach($specInfo as $key=>$value){
			$userProduct=$userProductModel->getUserProductInfoBySU($value['specification_id'],$userId);
			if(empty($userProduct)||empty($userProduct['rmb'])||($userProduct['rmb']<=0)){
				$userInfo=$userModel->getUserInfoByUserId($userId);
				$gradeProduct=$gradeProductModel->getGradeProductInfoBySG($value['specification_id'],$userInfo['grade_id']);
				$specInfo[$key]['rmb']=$gradeProduct['rmb'];
			}else{
				$specInfo[$key]['rmb']=$userProduct['rmb'];
			}
		}
		return $specInfo;
	}
	public function addPriceToProduct($products,$userId){
		$userProductModel=D("UserProduct");
		$userModel=D("User");
		$gradeProductModel=D("GradeProduct");
		foreach($products as $key=>$value){
			
			$userProduct=$userProductModel->getUserProductInfoBySU($value['specification_id'],$userId);
			
			if(empty($userProduct)){//  没有个人授权？
				
				
				$userInfo=$userModel->getUserInfoByUserId($userId);//  用户等级
				
				//  看是否等级授权
				$gradeProduct=$gradeProductModel->getGradeProductInfoBySG($value['specification_id'],$userInfo['grade_id']);
				
				if($gradeProduct["products_status_id"]==1){//  上架状态
					
					$products[$key]['rmb']=$gradeProduct['rmb'];
					
				}else{
					
					unset($products[$key]);//  未授权或者下架状态，删除产品
					
				}
				
			}else{// 有数据情况？
				
				
				if($userProduct['products_status_id']==1){// 用户处于上架状态
					
					if($userProduct['rmb']<=0){//   虽然处于上架状态，但是等于0
						
						//使用等级价格
						$userInfo=$userModel->getUserInfoByUserId($userId);//  用户等级
						$gradeProduct=$gradeProductModel->getGradeProductInfoBySG($value['specification_id'],$userInfo['grade_id']);
						
						$products[$key]['rmb']=empty($gradeProduct['rmb'])?0:$gradeProduct['rmb'];
						
					}else{//  使用此价格
						
						$products[$key]['rmb']=$userProduct['rmb'];
						
					}
					
					
				}else{//  下架状态，直接删除
					
					unset($products[$key]);
					
				}
				
				
			}
		}
		return $products;
	}
}