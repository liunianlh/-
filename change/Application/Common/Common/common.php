<?php
/**
 *  公共函数文件
 *	
 *	@create 2017/08/23 11:27:25
 */

 
/**
 *  生成随机码
 *
 *	@param String $type 生成类型 ‘number’ 数字 ‘en’ 英文 ‘all’ 数字和英文
 *  @param Int	  $length  随机长度
 *  @param Int 	  $sensitive  大小写  1 大写  2 小写 
 *  @return String
 */
function generateRandCode($type='all',$length=8,$sensitive=NULL){
	$number=array(0,1,2,3,4,5,6,7,8,9);
	$en=array(
		"a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p",
		"q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F",
		"G","H","J","K","L","M","N","O","P","Q","R","S","T","U","V","W",
		"X","Y","Z","Z"
	);
	switch($type){
		case "number":
			$mix=$number;
			break;
		case "en":
			$mix=$en;
			break;
		default:
			$mix=array_merge($number,$en);
			break;
	}
	$str='';
	$flag=shuffle($mix);
	if($flag===true){
		if((is_numeric($length)||is_numeric(intval($length)))&&$length>0){
			$len=count($mix)-1;
			for($i=0;$i<$length;$i++){
				$str.=$mix[rand(0,$len)];
			}
		}
	}
	if($sensitive==1){
		$str=strtoupper($str);
	}elseif($sensitive==2){
		$str=strtolower($str);
	}
	return $str;
}

/**
 *  加密函数
 *
 *	@param String $str 要加密的字符串
 *  @return String
 */
function encode($str){
	$key=C("ENC_KEY");
	return str_replace("=","",base64_encode($key^$str));
}
/**
 *  解密函数
 *
 *	@param String $str 密文
 *  @return String
 */
function decode($str){
	$str=base64_decode($str);
	$key=C("ENC_KEY");
	return $key^$str;
}
/**
 *  人民币转美金
 *
 *	@param Decimal $RMB 密文
 *  @return Decimal
 */
function RMB2USD($RMB){
	$rmbRate=5;
	if(empty($_SESSION['rate_rmb'])){
		$rmbRate=M("Config")->where(array("config_name"=>"rate_rmb"))->getField("config_value");
		session("rate_rmb",$rmbRate);
	}else{
		$rmbRate=$_SESSION['rate_rmb'];
	}
	return sprintf("%.2f",$RMB/1.17/$rmbRate);
}
/**
 *  美金转人民币
 *
 *	@param Decimal $USD 密文
 *  @return Decimal
 */
function USD2RMB($USD){
	$usdRate=5;
	if(empty($_SESSION['rate_usd'])){
		$usdRate=M("Config")->where(array("config_name"=>"rate_usd"))->getField("config_value");
		session("rate_usd",$usdRate);
	}else{
		$usdRate=$_SESSION['rate_usd'];
	}
	return sprintf("%.2f",$USD*1.17*$usdRate);
}
/**
 *  根据订单Id生成字符串
 *
 *	@param Int $orderId 订单id
 *  @return String
 */
function convertOrderIdToString($orderId){
	$orderLength=strlen($orderId);
	$orderIdMD5=md5($orderId);
	
	$_map=array(//置换映射
		"s0"=>2,
		"s1"=>3,
		"s2"=>5,
		"s3"=>7,
		"s4"=>11,
		"s5"=>13,
		"s6"=>17,
		"s7"=>19,
		"s8"=>23,
		"s9"=>31
	);
	$orderIdMD5Array=str_split($orderIdMD5);
	$_result=array();
	$_index=array();
	for($i=0;$i<$orderLength;$i++){
		$str=substr($orderId,$i,1);
		$index=$_map["s".$str];
		$_result[]=$orderIdMD5Array[$index];
		$orderIdMD5Array[$index]=$str;
		$_index[]=$index;
	}
	return implode("",$orderIdMD5Array).":".implode("",$_result).":".implode(":",$_index);
}
/**
 *  根据订单字符串生成Id
 *
 *	@param String $orderStr 订单
 *  @return Int
 */
function convertOrderStringToId($orderStr){
	// $data=explode(":",$orderStr);
	// $orderIdMD5=$data[0];
	// $result=$data[1];
	
	// $_map=array(//置换映射
		// "s0"=>2,
		// "s1"=>3,
		// "s2"=>5,
		// "s3"=>7,
		// "s4"=>11,
		// "s5"=>13,
		// "s6"=>17,
		// "s7"=>19,
		// "s8"=>23,
		// "s9"=>31
	// );
	// $orderIdMD5Array=str_split($orderIdMD5);
	// $_result=array();
	// for($i=0;$i<strlen($result);$i++){
		// $str=substr($result,$i,1);
		// $index=$data[i+2];
		
		// $index=$_map["s".$str];
		// $_result[]=$orderIdMD5Array[$index];
		// $orderIdMD5Array[$index]=$str;
	// }
	// return implode("",$orderIdMD5Array).":".implode("",$_result);
}
function getToday(){
	$date=date("Y-m-d",time());
	return strtotime($date." 00:00:00");
}
function getLastWeek(){
	return strtotime("-1 week");
}
function getLastMonth(){
	return strtotime("-1 month");
}
function getLastThreeMonth(){
	return strtotime("-3 month");
}
function getLastYear(){
	return strtotime("-1 year");
}
function getCurrentMonth(){
	$date=date("Y-m",time());
	return strtotime($date."-01 00:00:00");
}


function calculateLogistics($cityId,$specIds,$buyNum){
	$match=array();
	$areaInfo=M("Area")->where(array("area_id"=>$cityId))->find();
	$areaName=$areaInfo['area_name'];
	$where=array();
	$logisticsCompany=M("Logistics_company")->where(array("logistics_default"=>2))->find();
	if(empty($logisticsCompany)){
		$where['logistics_tpl_area']=array("like","%".$areaName."%");
		$relInfo=M("Logistics_tpl")->where($where)->select();
		foreach($relInfo as $key=>$value){
			$areaArr=explode(",",$value['logistics_tpl_area']);
			if(false!==array_search($areaName,$areaArr)){
				$match=$value;
				break;
			}
		}
	}else{
		$logisticsCompanyName=$logisticsCompany['logistics_company_name'];
		$where['logistics_tpl_name']=$logisticsCompanyName;
		$relInfo=M("Logistics_tpl")->where($where)->select();
		foreach($relInfo as $key=>$value){
			$areaArr=explode(",",$value['logistics_tpl_area']);
			if(false!==array_search($areaName,$areaArr)){
				$match=$value;
				break;
			}
		}
		if(empty($match)){
			$defaultInfo=M("Logistics_tpl")->where(array("logistics_tpl_area"=>"默认地区","logistics_tpl_name"=>$logisticsCompanyName))->find();
			if(!empty($defaultInfo)){
				$match=$defaultInfo;
			}
		}
	}
	
	if(!empty($match)){
		$where2=array();
		if(is_array($specIds)){
			$where2['specification_id']=array("in",implode(",",$specIds));
		}else{
			$where2['specification_id']=$specIds;
		}
		$specInfo=M("Specification")->where($where2)->select();
		
		$totalLoading=0;
		$totalRWeight=0;
		$totalNWeight=0;
		$totalVolume=0;
		foreach($specInfo as $kk=>$vv){
			$totalLoading+=$buyNum[$kk];
			$totalRWeight+=($buyNum[$kk]*$vv['rough_weight']);
			$totalNWeight+=($buyNum[$kk]*$vv['net_weight']);
			$totalVolume+=($buyNum[$kk]*$vv['volume']);
		}
		$pw="";
		$money=0;
		if($match['logistics_tpl_price_way']==1){//按件数计费
			$money+=$match['logistics_tpl_first_fee'];
			if($totalLoading>$match['logistics_tpl_first_weight']){
				$tt=ceil(($totalLoading-$match['logistics_tpl_first_weight'])/$match['logistics_tpl_second_weight']);
				$money+=$tt*$match['logistics_tpl_second_fee'];
			}
		}
		
		if($match['logistics_tpl_price_way']==2){//按毛重量计费
			$money+=$match['logistics_tpl_first_fee'];
			if($totalRWeight>$match['logistics_tpl_first_weight']){
				$tt=ceil(($totalRWeight-$match['logistics_tpl_first_weight'])/$match['logistics_tpl_second_weight']);
				$money+=$tt*$match['logistics_tpl_second_fee'];
			}
		}
		
		// if($match['logistics_tpl_price_way']==2){//按净重量计费
			// $money+=$match['logistics_tpl_first_fee'];
			// if($totalNWeight>$match['logistics_tpl_first_weight']){
				// $tt=ceil(($totalNWeight-$match['logistics_tpl_first_weight'])/$match['logistics_tpl_second_weight']);
				// $money+=$tt*$match['logistics_tpl_second_fee'];
			// }
		// }
		
		if($match['logistics_tpl_price_way']==3){//按材积计费
			$money+=$match['logistics_tpl_first_fee'];
			if($totalVolume>$match['logistics_tpl_first_weight']){
				$tt=ceil(($totalVolume-$match['logistics_tpl_first_weight'])/$match['logistics_tpl_second_weight']);
				$money+=$tt*$match['logistics_tpl_second_fee'];
			}
		}
		
		if($money>0){
			if($match['logistics_tpl_currency']==2){//USD
				$pw="USD";
			}else{
				$pw="RMB";
			}
		}
		
		return array("pw"=>$pw,"money"=>$money);
	}else{
		return array("pw"=>"UN","money"=>0);
	}
}

/**
 *  递归处理系列分类
 *
 *	
 *	@Array 	$array
 *  @return Int
 */
function deal_array($array,$type_id=0,$style='|__'){
	$temp=array();
	$len=count($array);
	$flag=true;
	$counter=0;//初始化计数器
	$deep=0;//查找深度
	$index[$deep]=0;//索引，记录查找到的位置
	$k=0;
	$old_type_id=array();
	while($flag){
		for($i=$index[$deep];$i<$len;$i++){
			if($array[$i]['cat_pid']==$type_id){
				$str="";
				for($j=0;$j<$deep;$j++){
					if($j==$deep-1){
						$str.=$style;
					}else{
						$str.='&nbsp;&nbsp;&nbsp;&nbsp;';
					}
				}
				$old_type_id[]=$type_id;//记录旧的id
				$type_id=$array[$i]['category_id'];
				$array[$i]['cat_chinese_name']=$str.$array[$i]['cat_chinese_name'];
				$temp[]=$array[$i];
				$counter++;//计数器+1
				$index[$deep]=$i;//记录当前层级循环到的位置
				break;//退出循环，开始下一轮
			}
		}
		if($counter>0){
			$deep++;//深度+1
			$index[$deep]=0;
		}else{
			//返回到上一层级
			if($deep==0){
				//已经返回到最顶层时，退出while循环
				$flag=false;
			}else{
				$deep--;
				$index[$deep]=$index[$deep]+1;//从下一个位置开始循环
				$type_id=array_pop($old_type_id);//还原新的id
			}
		}
		if($k>2000){
			$flag=false;//次数太多，退出
		}
		$k++;
		$counter=0;//计数器重置
	}
	return $temp;
}

/**
 *  获取当前用户的可用接头
 *
 *	
 *	@Int 	$userId
 *  @return Array
 */
function filterJoint($userId=0){
	$grade_id=M("User")->where(array("user_id"=>$_SESSION['userid']))->getField("grade_id");
			
	//接头数组
	$jointIds=array();
	
	//检索用户-产品表
	$upp=M("UserProduct")->where(array("user_id"=>$_SESSION['userid']))->select();
	//检索等级-产品表
	$gps=M("GradeProduct")->where(array("grade_id"=>$grade_id))->select();
	
	$acceptSpec=array();//接受数组
	$rejectSpec=array();//拒绝数组
	
	foreach($upp as $k=>$v){
		if($v['products_status_id']==1){//  上架
			$acceptSpec[]=$v['specification_id'];
		}
		if($v['products_status_id']==2){
			$rejectSpec[]=$v['specification_id'];
		}
	}
	
	foreach($gps as $kk=>$vv){
		if($vv['products_status_id']==1){//  上架
			if(false===array_search($vv['specification_id'],$rejectSpec)){//  不在拒绝列表
				if(false===array_search($vv['specification_id'],$acceptSpec)){
					$acceptSpec[]=$vv['specification_id'];
				}
			}
		}
	}
	
	$specInfo=M("Specification")->where(array("specification_id"=>array("in",implode(",",$acceptSpec))))->select();
	foreach($specInfo as $kkk=>$vvv){
		if(false===array_search($vvv['joint_id'],$jointIds)){
			$jointIds[]=$vvv['joint_id'];
		}
	}
	return $jointIds;
}
/**
 *  获取当前用户的可用系列
 *
 *	
 *	@Int 	$userId
 *  @return Array
 */
function filterSerial(){
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
	
	return $allJoints;
}

/**
 *  获取当前用户的制定可用系列下的所有大接头
 *
 *	
 *	@Int 	$userId
 *  @return Array
 */
function filterBigJoint($serialId){
	
	// 1.所有可用小接头（id）
	$acceptJoint=filterJoint($_SESSION['userid']);
	
	// 2.所有可用小接头（信息）
	$where4['category_id']=array("in",implode(",",$acceptJoint));
	$jointsInfo=M("Category")->where($where4)->order("cat_sort asc")->select();
	
	//3.  所有可用大接头（id）
	$bigJointsInfoIds=array();
	foreach($jointsInfo as $key=>$value){
		if(false===array_search($value['cat_pid'],$bigJointsInfoIds)){
			$bigJointsInfoIds[]=$value['cat_pid'];
		}
	}
	//4.所有可用大接头（信息）
	$where2['category_id']=array("in",implode(",",$bigJointsInfoIds));
	$bigJoints=M("Category")->where($where2)->order("cat_sort asc")->select();
	
	$specificationBigJoint=array();
	foreach($bigJoints as $key=>$value){
		if($serialId==$value['cat_pid']){//  系列
			$specificationBigJoint[]=$value;
		}
	}
	
	return $specificationBigJoint;
}

/**
 *  获取当前用户的制定可用系列下的所有小接头
 *
 *	
 *	@Int 	$userId
 *  @return Array
 */
function filterSmallJoint($serialId){
	
	// 1.所有可用小接头（id）
	$acceptJoint=filterJoint($_SESSION['userid']);
	// 2.所有可用小接头（信息）
	$where4['category_id']=array("in",implode(",",$acceptJoint));
	$jointsInfo=M("Category")->where($where4)->order("cat_sort asc")->select();
	
	
	//指定的系列下  大接头
	$bigJoints=filterBigJoint($serialId);
	
	$smallJoints=array();
	
	$bjIds=array();
	foreach($bigJoints as $k=>$v){
		$bjIds[]=$v['category_id'];
	}
	
	foreach($jointsInfo as $key=>$value){
		if(false!==array_search($value['cat_pid'],$bjIds)){
			$smallJoints[]=$value;
		}
	}
	
	return $smallJoints;
}

/**
 *  获取当前网站应用模式
 *
 *	
 *	@Int 	$userId
 *  @return Array
 */
function webMode(){
	if(LANG_SET=="zh-cn"){
		$langFlag=1;
	}else{
		$langFlag=2;
	}
	return $langFlag;
}

/**
 *  获取当前当前登录用户的默认币种
 *
 *	
 *	@Int 	$userId
 *  @return Array
 */
function defaultCurrency(){
	
	$where=array();
	$where['user_id']=session("userid");
	
	$userInfo=M("User")->where($where)->find();
	
	return $userInfo["default_currency"];
}
