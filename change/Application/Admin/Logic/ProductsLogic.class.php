<?php
namespace Admin\Logic;
use Think\Model;
use Admin\Logic\UserProductLogic;
use Admin\Logic\GradeProductLogic;
use Admin\Logic\CategoryLogic;
class ProductsLogic extends Model {
	private $upLogic;
	private $gpLogic;
	private $catLogic;
	public function __construct(){
		parent::__construct();
		$this->upLogic=new UserProductLogic();
		$this->gpLogic=new GradeProductLogic();
		$this->catLogic=new CategoryLogic();
	}
    public function getProductInfoByUserAndGrade($productInfo,$userId,$gpsId){
		$specInfo=$productInfo['spec'];
		$categoryInfo=D("Admin/Category")->getAllCategory();
		foreach($specInfo as $key=>$value){
			$uppInfo=$value['user_product'];
			$gpsInfo=$value['grade_product'];
			$UP=$this->upLogic->getUPByUserId($uppInfo,$userId);
			$GP=$this->gpLogic->getGPByGradeId($gpsInfo,$gpsId);
			$specInfo[$key]['rmb2']=$UP['rmb'];
			$specInfo[$key]['status2']=$UP['products_status_id'];
			$specInfo[$key]['rmb1']=$GP['rmb'];
			$specInfo[$key]['status1']=$GP['products_status_id'];
			$specInfo[$key]['joint']=$this->catLogic->getProductInfoByUserAndGrade($categoryInfo,$value['serial_id']);
		}
		$productInfo['spec']=$specInfo;
		return $productInfo;
	}
	/**
	 *  统一处理（获取产品信息）
	 *
	 *	@param Int $gradeId 用户等级ID
	 *	@param Int $userId 	用户ID
	 *  @return Array
	 */
	public function getProductInfo($gradeId,$userId,$productId){
		$productInfo=array();
		$category=D("Category")->getAllSerials();
		$color=D("Color")->getAllColor();
		$productStatus=D("ProductsStatus")->getAllProductStatus();
		$grade=D("Grade")->getAllGrade();
		if(empty($gradeId)){
			$gradeId=$grade[0]["grade_id"];
		}
		$user=D("User")->getUserByGradeId($gradeId);
		$productsModel=D("Products");
		if(!empty($productId)){
			// $productInfo=json_decode(S(md5($productId)),true);//影响修改效果
			if(empty($productInfo)){
				$productInfo=$productsModel->getProductInfoById($productId);
				// S(md5($productId),json_encode($productInfo));
			}
			if(empty($userId)){
				$userId=$user[0]["user_id"];
			}
			$productInfo=$this->getProductInfoByUserAndGrade($productInfo,$userId,$gradeId);
		}else{
			if(!empty($category[0])){
				$productInfo['joint']=D("Category")->getAllSmallJointsBySerialId($category[0]['category_id']);
			}
		}
		$productInfo['category']=$category;
		$productInfo['color']=$color;
		$productInfo['product_status']=$productStatus;
		$productInfo['grade']=$grade;
		$productInfo['user']=$user;
		$productInfo['gradeId']=$gradeId;
		$productInfo['userId']=$userId;
		return $productInfo;
	}
	public function getAllProducts($productsInfo,$gradeId,$userId){
		$specModel=D("Specification");
		$products=array();
		foreach($productsInfo as $key=>$value){
			$specInfo=$specModel->getProductSpecificationInfoByProductId($value['products_id']);
			//$productsInfo[$key]['spec']=$specInfo;
			foreach($specInfo as $k=>$v){//重排产品数组
				$curUserInfo=array();
				$curGradeInfo=array();
				foreach($v['user_product'] as $kk=>$vv){
					if($vv['user_id']==$userId){
						$curUserInfo=$vv;
						break;
					}
				}
				foreach($v['grade_product'] as $kkk=>$vvv){
					if($vvv['grade_id']==$gradeId){
						$curGradeInfo=$vvv;
						break;
					}
				}
				$v['rmb2']=!empty($curUserInfo['rmb'])?$curUserInfo['rmb']:"0.00";
				$v['status2']=!empty($curUserInfo['products_status_id'])?$curUserInfo['products_status_id']:2;
				$v['rmb1']=!empty($curGradeInfo['rmb'])?$curGradeInfo['rmb']:0.00;
				$v['status1']=!empty($curGradeInfo['products_status_id'])?$curGradeInfo['products_status_id']:2;
				$products[]=array_merge($value,$v);
			}
		}
		return $products;
	}
	public function getAllSpecificationProductsByCondition($productsInfo,$gradeId,$userId){
		$specModel=D("Specification");
		$uppModel=D("UserProduct");
		$gpsModel=D("GradeProduct");
		foreach($productsInfo as $key=>$value){
			$value=$specModel->checkUpdateSpecificationByInfo($value);//检查更新冗余字段
			$uppInfo=$uppModel->getAllPricesBySpecificationId($value['specification_id']);
			$gpsInfo=$gpsModel->getAllPricesBySpecificationId($value['specification_id']);
			$productsInfo[$key]=$value;
			
			$curUserInfo=array();
			$curGradeInfo=array();
			foreach($uppInfo as $kk=>$vv){
				if($vv['user_id']==$userId){
					$curUserInfo=$vv;
					break;
				}
			}
			foreach($gpsInfo as $kkk=>$vvv){
				if($vvv['grade_id']==$gradeId){
					$curGradeInfo=$vvv;
					break;
				}
			}
			$productsInfo[$key]['rmb2']=!empty($curUserInfo['rmb'])?$curUserInfo['rmb']:"0.00";
			$productsInfo[$key]['status2']=!empty($curUserInfo['products_status_id'])?$curUserInfo['products_status_id']:2;
			$productsInfo[$key]['rmb1']=!empty($curGradeInfo['rmb'])?$curGradeInfo['rmb']:0.00;
			$productsInfo[$key]['status1']=!empty($curGradeInfo['products_status_id'])?$curGradeInfo['products_status_id']:2;
		}
		return $productsInfo;
	}
	public function getProductsList($productsInfo,$specInfo){
		foreach($productsInfo as $key=>$value){
			$temp=array();
			foreach($specInfo as $k=>$v){
				if($v['products_id']==$value['products_id']){
					$temp[]=$v;
					unset($specInfo[$k]);//减少下一轮产品循环次数
				}
			}
			$productsInfo[$key]['spec']=$temp;
		}
		return $productsInfo;
	}
}