<?php
namespace Admin\Controller;
use Think\Controller;

class AdminController extends BaseController {
    public function index(){
		$adminInfo=D("Admin")->getAllUser();
		
		
		
		$this->assign("admin_info",$adminInfo);
		$this->display();
	}
	public function add(){
		if(!IS_POST){
			$roleInfo=D("Role")->getAllRole();
			$this->assign("role_info",$roleInfo);
			$this->display();
		}else{
			$adminName=I("post.adminName");
			$role=I("post.role");
			$adminPwd=I("post.adminPwd");
			$adminCpwd=I("post.adminCpwd");
			$adminEmail=I("post.adminEmail");
			$adminName=trim($adminName);
			$role=trim($role);
			$adminPwd=trim($adminPwd);
			$adminCpwd=trim($adminCpwd);
			$adminEmail=trim($adminEmail);
			if(empty($adminName)){
				exit(json_encode(array("code"=>10120,'msg'=>"用户名不能为空")));
			}
			if(empty($role)){
				exit(json_encode(array("code"=>10121,'msg'=>"没有创建角色，请联系管理员")));
			}
			// if(strlen($adminPwd)<6||strlen($adminPwd)>12||preg_match("/^\d+$/",$adminPwd)||preg_match("/^[a-zA-Z]+$/",$adminPwd)){
			// 	exit(json_encode(array("code"=>10022,"info"=>"密码密码必须是6-12位数字和英文混合")));
			// }
			if($adminPwd!=$adminCpwd){
				exit(json_encode(array("code"=>10023,"info"=>"两次密码不一致")));
			}
			if(!preg_match("/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/",$adminEmail)){
				exit(json_encode(array("code"=>10021,"info"=>"邮箱格式不正确")));
			}
			$count=D("Admin")->checkAdminByName($adminName);
			if($count>0){
				exit(json_encode(array("code"=>10122,'msg'=>"此用户已经存在，不要重复添加")));
			}
			$admin_verify=generateRandCode("all",8);
			$strKey=substr(md5($admin_verify),6,6);
			$data=array(
				"admin_name"=>$adminName,
				"admin_pwd"=>md5($strKey.$adminPwd),
				"admin_email"=>$adminEmail,
				"admin_verify"=>$admin_verify,
				"admin_time"=>time()
			);
			$adminId=M("Admin")->add($data);
			if($adminId){
				D("RoleAdmin")->addRA($role,$adminId);
				exit(json_encode(array("code"=>10123,'msg'=>"添加成功","url"=>U('Admin/index'))));
			}else{
				exit(json_encode(array("code"=>10124,'msg'=>"添加失败,请稍后重试...")));
			}
		}
	}
	public function edit(){
		if(!IS_POST){
			$roleInfo=D("Role")->getAllRole();
			$id=I("get.id");
			$adminInfo=D("Admin")->getAdminInfoById($id);
			$this->assign("admin_info",$adminInfo);
			$this->assign("role_info",$roleInfo);
			$this->display();
		}else{
			$id=I("post.id");
			$adminName=I("post.adminName");
			$role=I("post.role");
			$adminPwd=I("post.adminPwd");
			$adminCpwd=I("post.adminCpwd");
			$adminEmail=I("post.adminEmail");
			$adminName=trim($adminName);
			$role=trim($role);
			$adminPwd=trim($adminPwd);
			$adminCpwd=trim($adminCpwd);
			$adminEmail=trim($adminEmail);
			if(empty($adminName)){
				exit(json_encode(array("code"=>10120,'msg'=>"用户名不能为空")));
			}
			if(empty($role)){
				exit(json_encode(array("code"=>10121,'msg'=>"没有创建角色，请联系管理员")));
			}
			// if(!empty($adminPwd)){
			// 	if(strlen($adminPwd)<6||strlen($adminPwd)>12||preg_match("/^\d+$/",$adminPwd)||preg_match("/^[a-zA-Z]+$/",$adminPwd)){
			// 		exit(json_encode(array("code"=>10022,"info"=>"密码密码必须是6-12位数字和英文混合")));
			// 	}
			// }
			if($adminPwd!=$adminCpwd){
				exit(json_encode(array("code"=>10023,"info"=>"两次密码不一致")));
			}
			if(!preg_match("/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/",$adminEmail)){
				exit(json_encode(array("code"=>10021,"info"=>"邮箱格式不正确")));
			}
			$adminInfo=D("Admin")->getAdminInfoById($id);
			if($adminInfo['admin_name']!=$adminName){
				$count=D("Admin")->checkAdminByName($adminName);
				if($count>0){
					exit(json_encode(array("code"=>10122,'msg'=>"此用户已经存在，不要重复添加")));
				}
			}
			$data=array(
				"admin_name"=>$adminName,
				"admin_email"=>$adminEmail
			);
			if(!empty($adminPwd)){
				$admin_verify=generateRandCode("all",8);
				$strKey=substr(md5($admin_verify),6,6);
				$data['admin_pwd']=md5($strKey.$adminPwd);
				$data['admin_verify']=$admin_verify;
			}
			$res=M("Admin")->where(array("admin_id"=>$id))->save($data);
			if($res!==false){
				D("RoleAdmin")->saveRA($role,$id);
				exit(json_encode(array("code"=>10123,'msg'=>"更新成功","url"=>U('Admin/index'))));
			}else{
				exit(json_encode(array("code"=>10124,'msg'=>"更新失败,请稍后重试...")));
			}
		}
	}
	public function del(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10127,'msg'=>"非法操作")));
		}else{
			$id=I("get.id");
			$key=I("post.key");
			if(md5($id)!=$key){
				exit(json_encode(array("code"=>10128,'msg'=>"删除失败，请稍后重试...")));
			}
			$res=M("Admin")->where(array('admin_id'=>$id))->delete();
			if($res!==false){
				D("RoleAdmin")->delRA($id);
				exit(json_encode(array("code"=>10129,'msg'=>"删除成功","url"=>U('Admin/index'))));
			}else{
				exit(json_encode(array("code"=>10128,'msg'=>"删除失败，请稍后重试...")));
			}
		}
	}
	public function getAdminInfo(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$adminId=I("post.adminId");
			$adminInfo=D("Admin")->getAdminInfoById($adminId);
			exit(json_encode(array("code"=>10029,"msg"=>$adminInfo['admin_email'])));
		}
	}
}