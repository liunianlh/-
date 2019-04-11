<?php
namespace Home\Controller;
class QuickController extends BaseController {
    public function index(){
		
		if(defaultCurrency()=="USD"){
					
			$defaultCurrency="USD";
			
		}else{
			
			$defaultCurrency="RMB";
			
		}
		$this->assign("defaultCurrency",$defaultCurrency);
		
		$where=setSearchProduct();
		
		$productsInfo=M("Products")
					->join("left join __SPECIFICATION__ on __SPECIFICATION__.products_id=__PRODUCTS__.products_id")
					->where($where)
					->select();
					
		$productsInfo=D("Specification")->addPriceToProduct($productsInfo,$_SESSION["userid"]);
		foreach($productsInfo as $key=>$value){
			
			if($defaultCurrency=="USD"){
				
				$productsInfo[$key]['rmb']=RMB2USD($value['rmb']);
			
			}
			
			if($this->langFlag==2){
				$productsInfo[$key]['serial_name']=$value['serial_name2'];
				$productsInfo[$key]['joint_name']=$value['joint_name2'];
				$productsInfo[$key]['color_name']=$value['color_name2'];
				$productsInfo[$key]['products_name']=$value['products_english_name'];
			}else{
				$productsInfo[$key]['products_name']=$value['products_chinese_name'];
			}
		}
		if(!IS_POST){
			
			
			
			$categoryModel=D("Admin/Category");
			$serialInfo=$categoryModel->getAllSerials();
			$smallJointsInfo=$categoryModel->getAllSmallJoints();
			
			
			$acceptJoint=filterJoint($_SESSION['userid']);
			$where4['category_id']=array("in",implode(",",$acceptJoint));
			$jointsInfo=M("Category")->where($where4)->order("cat_sort asc")->select();
			
			
			$bigJointsInfoIds=array();
			foreach($jointsInfo as $key=>$value){
				if(false===array_search($value['cat_pid'],$bigJointsInfoIds)){
					$bigJointsInfoIds[]=$value['cat_pid'];
				}
			}
			$where2['category_id']=array("in",implode(",",$bigJointsInfoIds));
			$bigJoints=M("Category")->where($where2)->order("cat_sort asc")->select();
			
			$serialIds=array();
			foreach($bigJoints as $kk=>$vv){
				if(false===array_search($vv['cat_pid'],$serialIds)){
					$serialIds[]=$vv['cat_pid'];
				}
			}
			
			$where3['category_id']=array("in",implode(",",$serialIds));
			$allJoints=M("Category")->where($where3)->order("cat_sort asc")->select();
			
			$this->assign("serial_info",$allJoints);
			// $this->assign("joint_info",$jointsInfo);
			$this->assign("product_info",$productsInfo);
			$this->display();
		}else{
			exit(json_encode(array("code"=>"10153","msg"=>$productsInfo)));
		}
	}
	public function getAllSmallJoints(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10150,"msg"=>"非法操作")));
		}else{
			$sid=I("post.sid");
			$categoryModel=D("Admin/Category");
			// if(empty($sid)||intval($sid)==0){
				// $smallJointsInfo=$categoryModel->getAllSmallJoints();
			// }else{
				// $smallJointsInfo=$categoryModel->getAllSmallJointsBySerialId($sid);
			// }
			
			$acceptJoint=filterJoint($_SESSION['userid']);
			$where['category_id']=array("in",implode(",",$acceptJoint));
			$jointsInfo=M("Category")->where($where)->order("cat_sort asc")->select();
			
			if(empty($sid)||intval($sid)==0){
				
			}else{
				$bigJointsInfoIds=array();
				foreach($jointsInfo as $key=>$value){
					if(false===array_search($value['cat_pid'],$bigJointsInfoIds)){
						$bigJointsInfoIds[]=$value['cat_pid'];
					}
				}
				$where2['category_id']=array("in",implode(",",$bigJointsInfoIds));
				$bigJoints=M("Category")->where($where2)->order("cat_sort asc")->select();
				
				$bigIds=array();
				foreach($bigJoints as $kk=>$vv){
					if($vv['cat_pid']!=$sid){
						unset($bigJoints[$kk]);
					}else{
						$bigIds[]=$vv['category_id'];
					}
				}
				
				foreach($jointsInfo as $kkk=>$vvv){
					if(false===array_search($vvv['cat_pid'],$bigIds)){
						unset($jointsInfo[$kkk]);
					}
				}
			}
			
			
			$opts='';
			$opts.='<option value="0">'.L('_PUBLIC_PLEASE_SELECT_').'</option>';
			if($this->langFlag==1){
				foreach($jointsInfo as $key=>$value){
					$opts.='<option value="'.$value['category_id'].'">'.$value['cat_chinese_name'].'</option>';
				}
			}else{
				foreach($jointsInfo as $key=>$value){
					$opts.='<option value="'.$value['category_id'].'">'.$value['cat_english_name'].'</option>';
				}
			}
			exit(json_encode(array("code"=>10151,"msg"=>$opts)));
		}
	}
}