<?php
namespace Admin\Controller;
use Think\Controller;
use Libs\Upload;

class ComController extends Controller {
    public function getAllSmallJoints(){
		if(!IS_POST){
			exit(json_encode(array("status"=>0,"info"=>"fail")));
		}
		$serialId=I("post.sid");
		$info=D('Category')->getAllSmallJointsBySerialId($serialId);
		exit(json_encode(array("status"=>1,"info"=>$info)));
	}
	public function getProductByGradeId(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$gradeId=I("post.gradeId");
			$userId=I("post.userId");
			$productId=I("post.productId");
			$productInfo=D("Products","Logic")->getProductInfo($gradeId,$userId,$productId);
			exit(json_encode(array("code"=>10102,"msg"=>$productInfo)));
		}
	}
	public function upload(){
		$upload=new Upload();
		$picInfo=$upload->upload($_FILES['file'],"products");
		$miniPic=$upload->create_miniimage($picInfo,200,200);
		exit(json_encode(array("code"=>0,"msg"=>"上传成功","picPath"=>$miniPic)));
	}
	
	/**************物流，货代，开票*****************/
	public function getLogistics(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$logistics=M("Logistics")->where(array("logistics_id"=>$dataId))->find();
			exit(json_encode(array("code"=>10161,"msg"=>$logistics)));
		}
	}
	public function getForworder(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$forworder=M("Forworder")->where(array("forworder_id"=>$dataId))->find();
			exit(json_encode(array("code"=>10161,"msg"=>$forworder)));
		}
	}
	public function getInvoice(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$invoice=M("Invoice")->where(array("invoice_id"=>$dataId))->find();
			if($invoice['invoice_type_id']==2){
				$invoice['invoice_addon']=json_decode($invoice['invoice_addon'],true);
			}
			exit(json_encode(array("code"=>10161,"msg"=>$invoice)));
		}
	}
	
	/**************计算**************/
	public function calculatePriceByNumber(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$userId=I("post.userId");
			$number=I("post.number",0,'intval');
			if($number<=0){
				$number=0;
			}
			
			if(isset($_SESSION['S_C'])&&!empty($_SESSION['S_C'])){
				$verifyKey=$_SESSION['S_C'];
			}else{
				$this->redirect("Order/createOrder");
			}
			$cache=S($verifyKey);
			
			$res=$cache['order_detail'][md5($dataId)];
			$res['total_number']=$number;
			$res['amount']=sprintf("%.2f",$number*$res['rmb']);
			$cache['order_detail'][md5($dataId)]=$res;
			$total_price=$this->calcTotalPrice($cache['order_detail']);
			$cache['total_price']=$total_price;
			$res['total_price']=sprintf("%.2f",$total_price+$cache['service_fee']);
			$cache['total_price2']=$res['total_price'];
			S($verifyKey,$cache,1000);
			
			exit(json_encode(array("code"=>10161,"msg"=>$res)));
		}
	}
	public function calculatePriceByPrice(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$userId=I("post.userId");
			$price=I("post.price",0,'floatval');
			if($price<=0){
				$price=0;
			}
			
			if(isset($_SESSION['S_C'])&&!empty($_SESSION['S_C'])){
				$verifyKey=$_SESSION['S_C'];
			}else{
				$this->redirect("Order/createOrder");
			}
			$cache=S($verifyKey);
			
			$res=$cache['order_detail'][md5($dataId)];
			$res['price']=$res['rmb']=sprintf("%.2f",$price);
			$res['amount']=sprintf("%.2f",$res['total_number']*$res['rmb']);
			$cache['order_detail'][md5($dataId)]=$res;
			$total_price=$this->calcTotalPrice($cache['order_detail']);
			$cache['total_price']=sprintf("%.2f",$total_price);
			$res['total_price']=sprintf("%.2f",$total_price+$cache['service_fee']);
			$cache['total_price2']=$res['total_price'];
			S($verifyKey,$cache,1000);
			
			exit(json_encode(array("code"=>10161,"msg"=>$res)));
		}
	}
	
	public function calcServicePrice(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$userId=I("post.userId");
			$price=I("post.price",0,'floatval');
			if($price<=0){
				$price=0;
			}
			
			if(isset($_SESSION['S_C'])&&!empty($_SESSION['S_C'])){
				$verifyKey=$_SESSION['S_C'];
			}else{
				$this->redirect("Order/createOrder");
			}
			$cache=S($verifyKey);
			
			$cache['service_fee']=sprintf("%.2f",$price);
			$res['total_price']=sprintf("%.2f",$cache['total_price']+$cache['service_fee']);
			$res['price']=sprintf("%.2f",$cache['service_fee']);
			$cache['total_price2']=$res['total_price'];
			S($verifyKey,$cache,1000);
			
			exit(json_encode(array("code"=>10161,"msg"=>$res)));
		}
	}
	
	//添加产品并缓存
	public function addSpecToCache(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			$userId=I("post.userId");
			$currency=I("post.currency");
			
			$res=D("Specification")->getOneSpecInfoBySUID($dataId,$userId);
			$res['buy_number']=1;
			$res['total_number']=$res['loading'];
			if($currency=="USD"){
				$res['price']=$res['rmb']=RMB2USD($res['rmb']);
			}else{
				$res['price']=sprintf("%.2f",$res['rmb']);
			}
			$res['order_detail_id']=0;
			$res['amount']=sprintf("%.2f",$res['total_number']*$res['rmb']);
			
			if(isset($_SESSION['S_C'])&&!empty($_SESSION['S_C'])){
				$verifyKey=$_SESSION['S_C'];
			}else{
				$verifyKey=md5($userId.rand(17487,98962));//key
				session("S_C",$verifyKey);
			}
			
			$cache=S($verifyKey);
			if(empty($cache)){
				$data=array();
			}else{
				$data=$cache;
			}
			$data['order_detail'][md5($res['specification_id'])]=$res;
			$data['user_id']=$userId;
			$total_price=$this->calcTotalPrice($data['order_detail']);
			$data['total_price']=sprintf("%.2f",$total_price);
			if(empty($data['service_fee'])){
				$data['service_fee']=0.00;
				$res['total_price']=sprintf("%.2f",$total_price);
			}else{
				$res['total_price']=sprintf("%.2f",$total_price+$data['service_fee']);
			}
			$data['total_price2']=sprintf("%.2f",$res['total_price']);
			S($verifyKey,$data,1000);
			
			exit(json_encode(array("code"=>10161,"msg"=>$data)));
		}
	}
	
	//删除添加的产品
	public function deleteProduct(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$dataId=I("post.dataId");
			
			if(isset($_SESSION['S_C'])&&!empty($_SESSION['S_C'])){
				$verifyKey=$_SESSION['S_C'];
			}else{
				$verifyKey=md5($userId.rand(17487,98962));//key
				session("S_C",$verifyKey);
			}
			
			$cache=S($verifyKey);
			if(empty($cache)){
				$data=array();
			}else{
				$data=$cache;
				unset($data['order_detail'][md5($dataId)]);
				$total_price=$this->calcTotalPrice($data['order_detail']);
				$data['total_price']=sprintf("%.2f",$total_price);
				if(empty($data['service_fee'])){
					$data['service_fee']=0.00;
				}
				$data['total_price2']=sprintf("%.2f",$total_price+$data['service_fee']);
				S($verifyKey,$data,1000);
			}
			exit(json_encode(array("code"=>10161,"msg"=>$data)));
		}
	}
	
	public function loadRequiredByUID(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$userId=I("post.userId");
			$where2=array("user_id"=>$userId);
			$allLogistics=M("Logistics")->where($where2)->select();
			$allForworder=M("Forworder")->where($where2)->select();
			$allInvoice=M("Invoice")->where($where2)->select();
			$res=array(
				"logistics"=>$allLogistics,
				"forworder"=>$allForworder,
				"invoice"=>$allInvoice
			);
			exit(json_encode(array("code"=>10161,"msg"=>$res)));
		}
	}
	public function loadProductByUID(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$userId=I("post.userId");
			
			if(isset($_SESSION['S_C'])&&!empty($_SESSION['S_C'])){
				$verifyKey=$_SESSION['S_C'];
				$cache=S($verifyKey);
			}else{
				$cache=array();
			}
			
			
			$specIds=array();
			if(!empty($cache)){
				$orderDetailInfo=$cache['order_detail'];
				foreach($orderDetailInfo as $key=>$value){
					$specIds[]=$value['specification_id'];
				}
			}
			$res=$this->getProductInfo($userId);
			$res['specIds']=$specIds;
			exit(json_encode(array("code"=>10161,"msg"=>$res)));
		}
	}
	public function flushCacheData(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$currency=I("post.currency");
			if(isset($_SESSION['S_C'])&&!empty($_SESSION['S_C'])){
				$verifyKey=$_SESSION['S_C'];
				$cache=S($verifyKey);
				
				if(!empty($cache)){
					$orderDetailInfo=$cache['order_detail'];
					$currency=trim($currency);
					if($currency=="USD"){
						foreach($orderDetailInfo as $key=>$value){
							if(!empty($value['rmb'])){
								$orderDetailInfo[$key]['rmb']=RMB2USD($value['rmb']);
							}
							if(!empty($value['price'])){
								$orderDetailInfo[$key]['price']=RMB2USD($value['price']);
							}
							if(!empty($value['amount'])){
								$orderDetailInfo[$key]['amount']=RMB2USD($value['amount']);
							}
						}
						$cache['total_price']=RMB2USD($cache['total_price']);
						$cache['service_fee']=RMB2USD($cache['service_fee']);
						$cache['total_price2']=RMB2USD($cache['total_price2']);
					}
					if($currency=="RMB"){
						foreach($orderDetailInfo as $key=>$value){
							if(!empty($value['rmb'])){
								$orderDetailInfo[$key]['rmb']=USD2RMB($value['rmb']);
							}
							if(!empty($value['price'])){
								$orderDetailInfo[$key]['price']=USD2RMB($value['price']);
							}
							if(!empty($value['amount'])){
								$orderDetailInfo[$key]['amount']=USD2RMB($value['amount']);
							}
						}
						$cache['total_price']=USD2RMB($cache['total_price']);
						$cache['service_fee']=USD2RMB($cache['service_fee']);
						$cache['total_price2']=USD2RMB($cache['total_price2']);
					}
					$cache['order_detail']=$orderDetailInfo;
					S($verifyKey,$cache,1000);
					exit(json_encode(array("code"=>10161,"msg"=>$cache)));
				}
			}else{
				$cache=array();
				exit(json_encode(array("code"=>10160,"msg"=>$res)));
			}
		}
	}
	public function getProductInfo($userId){
		$specInfo=D("Specification","Logic")->getAllSpecification($userId);
		$count=count($specInfo);
		$page=new \Think\Page($count,4);
		if(!isset($_GET['p'])||empty($_GET['p'])){
			$curPage=1;
		}else{
			$curPage=$_GET['p'];
		}
		$specInfo=array_slice($specInfo,4*($curPage-1),4);
		return array("spec_info"=>$specInfo,"page"=>$page->show());
	}
	private function calcTotalPrice($orderDetail){
		$total_price=0.00;
		foreach($orderDetail as $key=>$value){
			$total_price+=$value['amount'];
		}
		return $total_price;
	}
	
	/*************************************/
	public function getAllSmallJointsBy(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10150,"msg"=>"非法操作")));
		}else{
			$sid=I("post.sid");
			$categoryModel=D("Admin/Category");
			if(empty($sid)||intval($sid)==0){
				$smallJointsInfo=$categoryModel->getAllSmallJoints();
			}else{
				$smallJointsInfo=$categoryModel->getAllSmallJointsBySerialId($sid);
			}
			
			$opts='';
			$opts.='<option value="0">全部</option>';
			if($this->langFlag==1){
				foreach($smallJointsInfo as $key=>$value){
					$opts.='<option value="'.$value['category_id'].'">'.$value['cat_chinese_name'].'</option>';
				}
			}else{
				foreach($smallJointsInfo as $key=>$value){
					$opts.='<option value="'.$value['category_id'].'">'.$value['cat_english_name'].'</option>';
				}
			}
			exit(json_encode(array("code"=>10151,"msg"=>$opts)));
		}
	}
}