<?php
namespace Admin\Model;
use Think\Model;
class UserProductModel extends Model {
    public function addOrSaveProduct($specId,$data,$post){
		if(!empty($data['rmb2'])){
			$user=$post['user'];
			$data=array(
				"rmb"=>$data['rmb2'],
				"products_status_id"=>$data['status2']
			);
			$uppId=$this->isExists($user,$specId);
			if(!$uppId){
				$data["user_id"]=$user;
				$data["specification_id"]=$specId;
				$uppId=$this->add($data);
			}else{
				$this->where(array("user_id"=>$user,"specification_id"=>$specId))->save($data);
			}
			return $uppId;
		}else{
			return false;
		}
	}
	public function isExists($user,$specId){
		$info=$this->where(array("user_id"=>$user,"specification_id"=>$specId))->find();
		if(!empty($info)){
			return $info['upp_id'];
		}else{
			return false;
		}
	}
	public function getAllPricesBySpecificationId($specId){
		$uppInfo=$this->where(array("specification_id"=>$specId))->select();
		return $uppInfo;
	}
	public function delUserProductBySpecificationId($specId){
		$res=$this->where(array("specification_id"=>$specId))->delete();
		return $res;
	}
	public function getUserProductInfoByUserId($useId){
		$uppInfo=$this->where(array("user_id"=>$useId))->select();
		return $uppInfo;
	}
	public function saveUserProduct($userProduct){
		foreach($userProduct as $k=>$v){
			$hashArray=explode(":",$v['hash']);
			if($hashArray[0]==md5($hashArray[1].":".$hashArray[2].":".$hashArray[3].":".$hashArray[4])){//校验
				$data=array(
					"rmb"=>$v['rmb'],
					"products_status_id"=>$v['status']
				);
				if(empty($hashArray[4])){//添加数据
					$data['user_id']=$hashArray[3];
					$data['specification_id']=$hashArray[1];
					$this->add($data);
				}else{
					$res=$this->where(array("upp_id"=>$hashArray[4]))->save($data);
				}
			}
		}
	}
	public function getUserProductInfoBySUId($specId,$userId){
		$where=array();
		$where['specification_id']=$specId;
		$where['user_id']=$userId;
		return $this->where($where)->find();
	}
}