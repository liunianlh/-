<?php
namespace Admin\Model;
use Think\Model;
use Libs\Logger;
class UserModel extends Model {
    public function getAllUser($page,$where){
		$info=array();
		$info=$this
			->join("left join __COMPANY__ on __COMPANY__.user_id=__USER__.user_id")
			->where($where)
			->field(array("www_user.*","www_company.company_contacts","www_company.company_name"))
			->limit($page->firstRow.','.$page->listRows)
			->select();
		return $info;
	}
	//生成用户uid(随机两码英文+3码数字)
	public function generateRandUID(){
		$uid="";
		$counter=0;//计数器
		$flag=true;
		do{
			$uid=generateRandCode("en",2,1).generateRandCode("number",3);
			$count=$this->where(array("user_uid"=>$uid))->count();
			if($count<=0){
				break;
			}else{
				if($counter>300){
					$flag=false;
					break;
				}else{
					$counter++;
				}
			}
		}while(true);
		if(!$flag){
			return false;
		}
		return $uid;
	}
	public function getUserInfoByUidOrEmail($uoe){
		$where["user_uid|user_email"]=$uoe;
		$userInfo=$this->where($where)->find();
		return $userInfo;
	}
	public function checkLogin($userInfo,$pwd){
		$strKey=substr(md5($userInfo['user_verify']),6,6);
		$user_uid=$userInfo['user_uid'];
		if(md5($strKey.$user_uid.$pwd)==$userInfo['user_password']){
			return $userInfo;
		}else{
			return false;
		}
	}
	public function getUserInfoByUserId($userId){
		$where["user_id"]=$userId;
		$userInfo=$this->where($where)->find();
		return $userInfo;
	}
	public function getUserUidByUserId($userId){
		$userInfo=$this->getUserInfoByUserId($userId);
		return $userInfo['user_uid'];
	}
	public function checkUserPwd($userId,$pwd){
		$userInfo=$this->getUserInfoByUserId($userId);
		return $this->checkLogin($userInfo,$pwd);
	}
	public function setUserPwd($userId,$pwd){
		$userInfo=$this->getUserInfoByUserId($userId);
		$strKey=substr(md5($userInfo['user_verify']),6,6);
		$user_uid=$userInfo['user_uid'];
		$pwd=md5($strKey.$user_uid.$pwd);
		$data=array(
			"user_password"=>$pwd
		);
		$res=$this->where(array("user_id"=>$userId))->save($data);
		if($res!==false){
			return true;
		}else{
			return false;
		}
	}
	public function checkNewPwdAndOldPwd($userId,$pwd){
		$userInfo=$this->getUserInfoByUserId($userId);
		$strKey=substr(md5($userInfo['user_verify']),6,6);
		$user_uid=$userInfo['user_uid'];
		$pwd=md5($strKey.$user_uid.$pwd);
		if($pwd==$userInfo['user_password']){
			return true;
		}else{
			return false;
		}
	}
	public function getUserByGradeId($gradeId){
		$userInfo=$this
				->join("left join __COMPANY__ on __COMPANY__.user_id=__USER__.user_id")
				->field(array("www_user.*","www_company.company_name"))
				->where(array("www_user.grade_id"=>$gradeId))->select();
		return $userInfo;
	}
	public function checkUserIdIsExists($userInfo,$userId){
		$flag=false;
		foreach($userInfo as $key=>$value){
			if($value['user_id']==$userId){
				$flag=true;
				break;
			}
		}
		return $flag;
	}
	public function saveUserStatus($userData,$dataValue){
		$_userIds=array();
		foreach($userData as $key=>$value){
			list($userKey,$userId)=explode(":",$value);
			if($userKey==md5($userId)){
				$_userIds[]=$userId;
			}
		}
		$data=array(
			"account_status_id"=>$dataValue
		);
		$where=array();
		$where['user_id']=array("in",implode(",",$_userIds));
		$res=$this->where($where)->save($data);
		return $res;
	}
	public function getAllUser2(){
		$info=array();
		$info=$this
			->join("left join __COMPANY__ on __COMPANY__.user_id=__USER__.user_id")
			->order("www_user.user_uid asc")
			->select();
		return $info;
	}
	public function getUserInfosByUserUID($userUID){
		$where=array();
		$where["user_uid"]=array("in",$userUID);
		$userInfo=$this->where($where)->select();
		return $userInfo;
	}
}