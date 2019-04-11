<?php
namespace Home\Controller;
class IndexController extends BaseController {


	public function emils(){
//		require_once "Smtp.class.php";
		Vendor("Mailer.class#phpmailer");
//		Vendor('PHPExcel.PHPExcel');
//		Vendor("Smtp.class#phpmailer");
		//******************** 配置信息 ********************************
		$smtpserver = "smtp.exmail.qq.com";//SMTP服务器
		$smtpserverport =465;//SMTP服务器端口
		$smtpusermail = "order@tonetron.com.cn";//SMTP服务器的用户邮箱
		$smtpemailto ="1205177965@qq.com";//发送给谁
		$smtpuser = "order@tonetron.com.cn";//SMTP服务器的用户帐号，注：部分邮箱只需@前面的用户名
		$smtppass = "Tt2019";//SMTP服务器的用户密码
		$mailtitle ="我是邮件主题";//邮件主题
		$mailcontent = "<h1>啦啦啦</h1>";//邮件内容


		$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
		//************************ 配置信息 ****************************
		$smtp = new \Smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
		$smtp->debug = false;//是否显示发送的调试信息
		$statefc=$smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);

		echo "<div style='width:300px; margin:36px auto;'>";
		if($state==""){
			echo "对不起，邮件发送失败！请检查邮箱填写是否有误。";
			echo "<a href='index.html'>点此返回</a>";
			exit();
		}
		echo "恭喜！邮件发送成功！！";
		echo "<a href='index.html'>点此返回</a>";
		echo "</div>";
	}


    public function index(){
		$adModel=D("Admin/Ad");
		$adInfo=$adModel->getAllIndexAd();
		
		$productsIds=M("Specification")->select();
		$userId=$_SESSION["userid"];
		//执行过滤
		foreach($productsIds as $kk=>$vv){
			$userProduct=D("UserProduct")->getUserProductInfoBySU($vv['specification_id'],$userId);
			if(empty($userProduct)||empty($userProduct['rmb'])||($userProduct['rmb']<=0)){
				if(empty($userProduct)||($userProduct['products_status_id']==1)){
					
					$userInfo=D("User")->getUserInfoByUserId($userId);
					$gradeProduct=D("GradeProduct")->getGradeProductInfoBySG($vv['specification_id'],$userInfo['grade_id']);
					
					if(empty($userProduct)){
						if(empty($gradeProduct)||($gradeProduct['products_status_id']==2)){
							unset($productsIds[$kk]);
						}
					}
					
				}else{
					unset($productsIds[$kk]);
				}
			}else{
				if($userProduct['products_status_id']==2){//  下架
					unset($productsIds[$kk]);
				}
			}
		}
		
		
		$specIds=array();
		foreach($productsIds as $key=>$value){
			if(false===array_search($value['serial_id'],$specIds)){
				$specIds[]=$value['serial_id'];
			}
		}
		
		
		$where=array();
		$where['cat_level']=1;
		$where['category_id']=array("in",implode(',',$specIds));
		$serialInfo=M("Category")->where($where)->order("cat_sort asc")->select();
		
		
		$this->assign("serial_info",$serialInfo);
		$this->assign("ad_info",$adInfo);
		$this->assign("view_mode",1);
		$this->display();
	}
	public function text(){
		$categoryModel=D("Admin/Category");
		// $serialInfo=$categoryModel->getAllSerials();
		// $bigJointsInfo=$categoryModel->getAllBigJoints();
		
		//所有接头小类
		$acceptJoint=filterJoint($_SESSION['userid']);
		$where['category_id']=array("in",implode(",",$acceptJoint));
		$jointsInfo=M("Category")->where($where)->order("cat_sort asc")->select();
		
		//所有接头大类
		$bigJointsInfoIds=array();
		foreach($jointsInfo as $key=>$value){
			if(false===array_search($value['cat_pid'],$bigJointsInfoIds)){
				$bigJointsInfoIds[]=$value['cat_pid'];
			}
		}
		$where2['category_id']=array("in",implode(",",$bigJointsInfoIds));
		$bigJoints=M("Category")->where($where2)->order("cat_sort asc")->select();
		
		//所有系列
		$serialIds=array();
		foreach($bigJoints as $kk=>$vv){
			if(false===array_search($vv['cat_pid'],$serialIds)){
				$serialIds[]=$vv['cat_pid'];
			}
		}
		$where3['category_id']=array("in",implode(",",$serialIds));
		$allJoints=M("Category")->where($where3)->order("cat_sort asc")->select();
		
		
		
		$adInfo=M("Ad")->where(array("ad_type"=>2))->select();
		$this->assign("ad_info",$adInfo);
		$this->assign("serial_info",$allJoints);
		// $this->assign("joint_info",$bigJoints);
		$this->assign("view_mode",2);
		$this->display();
	}
	public function getBigJoint(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10150,"msg"=>"非法操作")));
		}else{
			$sid=I("post.sid");
			$where=array();
			// if(empty($sid)||intval($sid)==0){
				// $bigJointsInfo=D("Admin/Category")->getAllBigJoints();
			// }else{
				// $where['cat_pid']=$sid;
				// $bigJointsInfo=M("Category")->where($where)->select();
			// }
			$acceptJoint=filterJoint($_SESSION['userid']);
			$where['category_id']=array("in",implode(",",$acceptJoint));
			$jointsInfo=M("Category")->where($where)->order("cat_sort asc")->select();
			
			if(empty($sid)||intval($sid)==0){
				$bigJointsInfoIds=array();
				foreach($jointsInfo as $key=>$value){
					if(false===array_search($value['cat_pid'],$bigJointsInfoIds)){
						$bigJointsInfoIds[]=$value['cat_pid'];
					}
				}
				$where2['category_id']=array("in",implode(",",$bigJointsInfoIds));
				$bigJoints=M("Category")->where($where2)->order("cat_sort asc")->select();
			}else{
				$bigJointsInfoIds=array();
				foreach($jointsInfo as $key=>$value){
					if(false===array_search($value['cat_pid'],$bigJointsInfoIds)){
						$bigJointsInfoIds[]=$value['cat_pid'];
					}
				}
				$where2['category_id']=array("in",implode(",",$bigJointsInfoIds));
				$bigJoints=M("Category")->where($where2)->order("cat_sort asc")->select();
				
				foreach($bigJoints as $kk=>$vv){
					if($vv['cat_pid']!=$sid){
						unset($bigJoints[$kk]);
					}
				}
			}
			
			
			$opts='';
			$opts.='<option value="0">'.L('_PUBLIC_PLEASE_SELECT_').'</option>';
			if($this->langFlag==1){
				foreach($bigJoints as $key=>$value){
					$opts.='<option value="'.$value['category_id'].'">'.$value['cat_chinese_name'].'</option>';
				}
			}else{
				foreach($bigJoints as $key=>$value){
					$opts.='<option value="'.$value['category_id'].'">'.$value['cat_english_name'].'</option>';
				}
			}
			exit(json_encode(array("code"=>10151,"msg"=>$opts)));
		}
	}
	public function bindUrl(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10150,"msg"=>"非法操作")));
		}else{
			$serial=I("post.serial");
			$joint=I("post.joint");
			$keywords=I("post.keywords");
			$sJointIds=array();
			if(!empty($joint)){
				$smallJoint=D("Admin/Category")->getAllChildrenByParentId($joint,3);
				foreach($smallJoint as $key=>$value){
					$sJointIds[]=$value['category_id'];
				}
			}
			if(empty($sJointIds)){
				$sJointIds=0;
			}else{
				$sJointIds=str_replace("=","",base64_encode(implode(",",$sJointIds)));
			}
			exit(json_encode(array("code"=>10151,"msg"=>U('Quick/index',array("serial"=>$serial,"joint"=>$sJointIds,"keywords"=>$keywords)))));
		}
	}
	public function getAllJoints(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10150,"msg"=>"非法操作")));
		}else{
			$id=I("post.id");
			// $where['cat_pid']=$id;
			
			$acceptJoint=filterJoint($_SESSION['userid']);
			$where['category_id']=array("in",implode(",",$acceptJoint));
			$jointsInfo=M("Category")->where($where)->order("cat_sort asc")->select();
			
			
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
				if(false===array_search($value['cat_pid'],$serialIds)){
					$serialIds[]=$value['cat_pid'];
				}
			}
			
			$catIds=array_merge($acceptJoint,$bigJointsInfoIds,$serialIds);
			
			$where3['category_id']=array("in",implode(",",$catIds));
			$allJoints=M("Category")->where($where3)->order("cat_sort asc")->select();
			
			
			$bigJointsInfo=deal_array($allJoints,$id,"");
			// $bigJointsInfo=D("Admin/Category")->getAllSmallJointsBySerialId($id);
			
			$opts='';
			$opts.='<option value="0">'.L('_PUBLIC_PLEASE_SELECT_').'</option>';
			if($this->langFlag==1){
				foreach($bigJointsInfo as $key=>$value){
					if($value['cat_level']==2){
						$opts.='<optgroup label="'.$value['cat_chinese_name'].'" style="font-weight:bold;font-style:normal;">'.$value['cat_chinese_name'].'</optgroup>';
					}else{
						$opts.='<option value="'.$value['category_id'].'">'.$value['cat_chinese_name'].'</option>';
					}
				}
			}else{
				foreach($bigJointsInfo as $key=>$value){
					if($value['cat_level']==2){
						$opts.='<optgroup label="'.$value['cat_english_name'].'" style="font-weight:bold;font-style:normal;">'.$value['cat_english_name'].'</optgroup>';
					}else{
						$opts.='<option value="'.$value['category_id'].'">'.$value['cat_english_name'].'</option>';
					}
				}
			}
			exit(json_encode(array("code"=>10151,"msg"=>$opts)));
		}
	}
	public function creatUrl(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10150,"msg"=>"非法操作")));
		}else{
			$sid=I("post.sid");
			$jid=I("post.jid");
			$jids=array();
			
			if(!empty($jid)){
				$catInfo=M("Category")->where(array("category_id"=>$jid))->find();
				if($catInfo['cat_level']==2){//接头大类
					$smallJoint=M("Category")->where(array("cat_pid"=>$jid))->select();
					foreach($smallJoint as $key=>$value){
						$jids[]=$value['category_id'];
					}
				}else{
					$jids[]=$jid;
				}
			}else{
				$bigJointsInfo=D("Admin/Category")->getAllSmallJointsBySerialId($sid);
				foreach($bigJointsInfo as $key=>$value){
					$jids[]=$value['category_id'];
				}
			}
			$joint=str_replace("=","",base64_encode(implode(",",$jids)));
			$url=U("Product/product_list",array("joint"=>$joint,"serial"=>$sid));
			exit(json_encode(array("code"=>10151,"url"=>$url)));
		}
	}
	public function logout(){
		session_unset();
		session_destroy();
		
		redirect(U("Login/index"));
	}
}