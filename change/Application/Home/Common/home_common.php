<?php
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
		$orderCurrency=$condition["orderCurrency"];
		$dataValue=$condition["dataValue"];
		if(empty($dataValue)){
			if(!empty($start)){
				$where['www_order.order_time']=array("egt",strtotime($start));
			}
			if(!empty($end)){
				$where['www_order.order_time']=array("elt",strtotime($end)+86400);
			}
			if(!empty($start)&&!empty($end)){
				$where['www_order.order_time']=array(array("egt",strtotime($start)),array("elt",strtotime($end)+86400));
			}
		}else{
			$dataValue=intval($dataValue);
			switch($dataValue){
				case 1:
					$where['www_order.order_time']=array("egt",getToday());
					break;
				case 2:
					$where['www_order.order_time']=array("egt",getLastWeek());
					break;
				case 3:
					$where['www_order.order_time']=array("egt",getLastMonth());
					break;
				case 4:
					$where['www_order.order_time']=array("egt",getLastThreeMonth());
					break;
				case 5:
					$where['www_order.order_time']=array("egt",getLastYear());
					break;
			}
		}
		
		if(!empty($orderStatus)){
			$where['www_order.order_status']=$orderStatus;
		}
		if(!empty($PONumber)){
			$where['www_order.order_ponumber']=array("like","%".$PONumber."%");
		}
		if(!empty($orderCurrency)){
			$where['www_order.order_currency']=array("like","%".$orderCurrency."%");
		}
	}
	$where['www_order.user_id']=$_SESSION['userid'];
	return $where;
}
function setSearchProduct(){
	$where=array();
	$serail=I("request.serial");
	$joint=I("request.joint");
	$sjoint=I("request.sjoint");
	$keywords=I("request.keywords");
	if(!empty($serail)){
		$where['www_specification.serial_id']=$serail;
	}
	if(!empty($joint)){
		$where['www_specification.joint_id']=array("in",base64_decode($joint));
	}
	if(!empty($sjoint)){
		$where['www_specification.joint_id']=$sjoint;
	}
	if(!empty($keywords)){
		$where['www_products.products_chinese_name|www_products.products_english_name|www_specification.model_name']=array("like","%".$keywords."%");
	}
	if(empty($where)){
		$where="1=1";
	}
	return $where;
}
function setSearchMsg(){
	$where=array();
	$type=I("get.wh");
	switch($type){
		case 1:
			$where['msg_time']=array(array('egt',getToday()),array('elt',time()));
			break;
		case 1:
			$where['msg_time']=array(array('egt',getLastWeek()),array('elt',time()));
			break;
		case 1:
			$where['msg_time']=array(array('egt',getLastMonth()),array('elt',time()));
			break;
		case 1:
			$where['msg_time']=array(array('egt',getLastThreeMonth()),array('elt',time()));
			break;
		case 1:
			$where['msg_time']=array(array('egt',getLastYear()),array('elt',time()));
			break;
		default:
			$where['msg_time']=array('elt',time());
			break;
	}
	$where['msg_to']=$_SESSION['userid'];
	return $where;
}
function setSearchP(){
	$where=array();
	if(empty($where)){
		$where="1=1";
	}
	return $where;
}
?>