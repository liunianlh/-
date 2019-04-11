<?php
/**
 *  设置分页大小
 *
 *	@param Int $pageSize 页大小
 *  @return Int
 */
function setProductPageSize(){
	$pageSize=I("post.pageSize");
	if(empty($pageSize)){
		if(isset($_SESSION['pageSize'])&&!empty($_SESSION['pageSize'])){
			$pageSize=$_SESSION['pageSize'];
		}else{
			$pageSize=20;
		}
	}else{
		$_SESSION['pageSize']=$pageSize;
	}
	return $pageSize;
}
/**
 *  设置产品查询条件
 *
 *  @return Int
 */
function setProductWhere(){
	$where=array();
	$condition=I("post.where");
	if(is_array($condition)){
		$searchModel=$condition["searchModel"];
		$searchKeyWord=$condition["searchKeyWord"];
		$searchCategory=$condition["searchCategory"];
		$searchJoint=$condition["searchJoint"];
		if(!empty($searchModel)){
			$where['www_specification.model_name']=array("like","%".$searchModel."%");
		}
		if(!empty($searchKeyWord)){
			$where['www_specification.model_name|www_specification.serial_name|www_specification.joint_name|www_products.products_chinese_name|www_specification.color_name']=array("like","%".$searchKeyWord."%");
		}
		if(!empty($searchCategory)){
			$where['www_specification.serial_id']=$searchCategory;
		}
		if(!empty($searchJoint)){
			$where['www_specification.joint_id']=$searchJoint;
		}
	}
	if(empty($where)){
		$where="1=1";
	}
	return $where;
}
/**
 *  设置用户查询条件
 *
 *  @return Int
 */
function setUserWhere(){
	$where=array();
	$condition=I("post.condition");
	if(is_array($condition)){
		$UID=$condition["UID"];
		$customerName=$condition["customerName"];
		$adminId=$condition["adminId"];
		$gradeId=$condition["gradeId"];
		$tianxin=$condition["tianxin"];
		if(!empty($UID)){
			$where['www_user.user_uid']=array("like","%".$UID."%");
		}
		if(!empty($customerName)){
			$where['www_company.company_name']=array("like","%".$customerName."%");
		}
		if(!empty($adminId)){
			$where['www_user.admin_id']=$adminId;
		}
		if(!empty($gradeId)){
			$where['www_user.grade_id']=$gradeId;
		}
		if(!empty($tianxin)){
			$where['www_user.tianxin_code']=array("like","%".$tianxin."%");;
		}
	}
	if(empty($where)){
		$where="1=1";
	}
	return $where;
}
/**
 *  设置订单查询条件
 *
 *  @return Int
 */
function setOrderWhere(){
	$where=array();
	$condition=I("post.where");
	if(is_array($condition)){
		$start=$condition["start"];
		$end=$condition["end"];
		$orderStatus=$condition["orderStatus"];
		$PONumber=$condition["PONumber"];
		$serialNumber=$condition["serialNumber"];
		$adminId=$condition["adminId"];
		$UID=$condition["UID"];
		$tianxinCode=$condition["tianxinCode"];
		$companyName=$condition["companyName"];
		$customerName=$condition["customerName"];
		if(!empty($start)){
			$where['www_order.order_time']=array("egt",strtotime($start));
		}
		if(!empty($end)){
			$where['www_order.order_time']=array("elt",strtotime($end)+86400);
		}
		if(!empty($start)&&!empty($end)){
			$where['www_order.order_time']=array(array("egt",strtotime($start)),array("elt",strtotime($end)+86400));
		}
		if(!empty($orderStatus)){
			$where['www_order.order_status']=$orderStatus;
		}
		if(!empty($PONumber)){
			$where['www_order.order_ponumber']=array("like","%".$PONumber."%");
		}
		if(!empty($serialNumber)){
			$where['www_order.order_serial_number']=array("like","%".$serialNumber."%");
		}
		if(!empty($UID)){
			$where['www_order.user_uid']=array("like","%".$UID."%");
		}
		if(!empty($customerName)){
			$where['www_order.user_name']=array("like","%".$customerName."%");
		}
		if(!empty($tianxinCode)){
			$where['www_order.tianxin_code']=array("like","%".$tianxinCode."%");
		}
		if(!empty($companyName)){
			$where['www_order.company_name']=array("like","%".$companyName."%");
		}
		if(!empty($adminId)){
			$where['www_order.admin_id']=$adminId;
		}
	}
	
	if(empty($where)){
		$where="1=1";
	}
	return $where;
}
/**
 *  将时间戳转成年月日时分秒
 *
 *	@Int 	$timestamp
 *  @return Array
 */
function convertTimestampToDate($timestamp){
	$str=date("Y:m:d:H:i:s",$timestamp);
	return explode(":",$str);
}
/**
 *  将年月日时分秒转成时间戳
 *
 *	@String 	$year
 *	@String 	$month
 *	@String 	$day
 *	@String 	$hour
 *	@String 	$minute
 *	@String 	$second
 *  @return Int
 */
function convertDateToTimestamp($year,$month,$day,$hour,$minute,$second){
	$timestamp=mktime($hour,$minute,$second,$month,$day,$year);
	return $timestamp;
}
/**
 *  缓存订单数据
 *
 *	
 *	@Array 	$orderInfo
 *	@Array 	$orderDetailInfo
 *	@Int 	$userId
 */
function orderEditCacheData($orderInfo,$orderDetailInfo,$userId){
	$data=array(
		"order"=>$orderInfo,
		"detail"=>$orderDetailInfo
	);
	$rnd=md5($userId."_".rand(10000,78969));
	session("order_edit",$rnd);
	S($rnd,$data,3600);
}
/**
 *  清除缓存订单数据
 *
 *	
 *	@Array 	$orderInfo
 *	@Array 	$orderDetailInfo
 *	@Int 	$userId
 */
function orderEditClearCacheData(){
	$rnd=session("order_edit");
	if(!empty($rnd)){
		S($rnd,null);
	}
}
/**
 *  获取缓存订单数据
 *
 *	
 *	@Array 	$orderInfo
 *	@Array 	$orderDetailInfo
 *	@Int 	$userId
 */
function orderEditGetCacheData(){
	$data=array();
	$rnd=session("order_edit");
	if(!empty($rnd)){
		$data=S($rnd);
	}
	return $data;
}
?>