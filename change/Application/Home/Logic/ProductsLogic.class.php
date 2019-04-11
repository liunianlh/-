<?php
namespace Home\Logic;
use Think\Model;
class ProductsLogic extends Model {
    public function dealProducts($products){
		foreach($products as $key=>$value){
			$model=array();//型号
			$length=array();//长度
			$color=array();//颜色
			
			$langSet=cookie('think_language');
			if(empty($langSet)){
				$langSet="zh-cn";
			}else{
				$langSet=$langSet;
			}
			if(strtolower($langSet)=="zh-cn"){
				$this->langFlag=1;
			}else{
				$this->langFlag=2;
			}
			
			foreach($value['spec'] as $k=>$v){
				
				if($this->isAuthenToUser($v['specification_id'])){
					$model[]=$v['model_name'];
					$length[]=$v['length'];
					if($this->langFlag==1){
						$color[]=$v['color_name'];
					}else{
						$color[]=$v['color_name2'];
					}
				}
			}
			$products[$key]['model']=array_flip(array_flip($model));
			$products[$key]['length']=array_flip(array_flip($length));
			sort($products[$key]['length']);
			$products[$key]['color']=array_flip(array_flip($color));
		}
		return $products;
	}
	public function calcCartTotalPrice($cart){
		$totalPrice=0.00;
		foreach($cart as $key=>$value){
			$totalPrice+=$value['subtotal'];
		}
		return $totalPrice;
	}
	
	public function isAuthenToUser($specId){
		
		//第一步：查询‘用户个人授权’
		$info1=M("User_product")->where(array("user_id"=>$_SESSION['userid'],"specification_id"=>$specId))->find();
		
		if(empty($info1)){//  没有找到数据：说明没有授权过
			
			//  第二步 ：查询‘用户等级授权’
			
			//1.  获取用户等级
			$userInfo=M("User")->where(array("user_id"=>$_SESSION['userid']))->find();
			
			if(empty($userInfo['grade_id'])){//  此用户还没有被分配等级
				return false;
			}else{
				
				//2. 查询‘用户等级授权’
				
				$info2=M("Grade_product")->where(array("grade_id"=>$userInfo['grade_id'],"specification_id"=>$specId))->find();
				
				if(empty($info2)){//  没有找到数据：说明此等级没有授权过
					
					//  此用户没有被授权，其所属等级也未被授权
					return false;
				}else{
					
					//  有数据情况？---》此用户没有被授权
					
					if($info2['products_status_id']==2){
						
						// 此用户没有被授权-->等级也未被授权（处于‘下架’状态）
						
						return false;
					}
					
					if($info2['products_status_id']==1){
						
						// 此用户没有被授权-->但是等级被授权（处于‘上架’状态）
						
						return true;
					}
				}
			}
			
		}else{
			
			//  有数据情况？
			
			if($info1['products_status_id']==1){
				// 个人 处于‘上架’状态）---》优先级高
				
				return true;
				
			}
			
			if($info1['products_status_id']==2){
				// 个人 处于‘下架’状态）---》优先级高
				
				return false;
				
			}
			
		}
		
		
	}
}