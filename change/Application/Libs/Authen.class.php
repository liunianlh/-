<?php
namespace Libs;
class Authen{
    public static function checkRight(){
		$roleIds=D("RoleAdmin")->getRoleIdByAdminId($_SESSION['adminid']);
		$rightId=D("RoleRight")->getRightIdByRoleId($roleIds);
		$rightIds=array();
		foreach($rightId as $key=>$value){
			$rightIds=array_merge(explode(",",$value),$rightIds);
		}
		$rightIds=array_unique($rightIds);
		$rightInfo=D("Right")->getRightInfoByRightId($rightIds);
		
		$rightList=array();
		foreach($rightInfo as $kk=>$vv){
			if(!empty($vv['module'])){
				$rightList["m".$vv['module']]["c".$vv['controller']]["a".$vv['action']]="a".$vv['action'];
			}
		}
		return $rightList;
	}
}