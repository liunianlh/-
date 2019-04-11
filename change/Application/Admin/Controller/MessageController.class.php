<?php
namespace Admin\Controller;
use Think\Controller;
use Libs\PDF;
use Libs\Upload;

class MessageController extends BaseController {
    public function index(){
		session("fz",null);
		// $count=M("User")->count();
		// $page=new \Think\Page($count,2);
		$userInfo=M("User")
					->join("left join __COMPANY__ on __COMPANY__.user_id=__USER__.user_id")
					// ->limit($page->firstRow.",".$page->listRows)
					->select();
		// $this->assign("page",$page->show());
		$this->assign("user_info",$userInfo);
		$this->display();
	}
	public function send(){
		if(!IS_POST){
			exit(json_encode(array("code"=>5,"msg"=>"非法操作")));
		}else{
			$to=I("post.to");
			$title=I("post.title");
			$content=I("post.content",'','');
			$date=I("post.date");
			$isAttach=I("post.isAttach");
			$files=I("post.paths");
			
			if(empty($to)){
				exit(json_encode(array("code"=>10124,'msg'=>"收件人不能为空")));
			}
			if(empty($title)){
				exit(json_encode(array("code"=>10124,'msg'=>"主旨不能为空")));
			}
			if(empty($content)){
				exit(json_encode(array("code"=>10124,'msg'=>"内容不能为空")));
			}
			$msg_to=D("User")->getUserInfosByUserUID($to);
			if(empty($msg_to)){
				exit(json_encode(array("code"=>10124,'msg'=>"发送失败,不存在的用户")));
			}
			foreach($msg_to as $key=>$value){
				if(empty($value['user_id'])){
					continue;
				}
				$data=array(
					"msg_title"=>$title,
					"msg_content"=>$content,
					"msg_from"=>$_SESSION['adminid'],
					"msg_to"=>$value['user_id']
				);
				if(!empty($date)){
					$data['msg_time']=strtotime($date);
				}else{
					$data['msg_time']=time();
				}
				if($isAttach=="yes"){
					$data['has_attachment']=2;
				}
				$msg_id=M("Message")->add($data);
				if($isAttach=="yes"){
					$idStr=I("post.ids");
					if(!empty($idStr)){
						$ids=explode(",",$idStr);
						$orderInfo=M("Order")->where(array('order_id'=>$ids[0]))->find();
						$data2=array(
							"msg_id"=>$msg_id,
							"xls"=>$orderInfo['order_serial_number']."xls",
							"pdf"=>$orderInfo['order_serial_number']."pdf",
							"order_id"=>$ids[0],
							"attach_type"=>1
						);
						M("Attach")->add($data2);
					}
					if(!empty($files)){
						foreach($files as $key=>$value){
							if(!empty($value)){
								$data2=array(
									"msg_id"=>$msg_id,
									"xls"=>$value[0],
									"pdf"=>$value[1],
									"order_id"=>0,
									"attach_type"=>2
								);
								M("Attach")->add($data2);
							}
						}
					}
				}
			}
			exit(json_encode(array("code"=>10123,'msg'=>"发送成功")));
		}
	}
	
	public function search(){
		if(!IS_POST){
			exit(json_encode(array("code"=>5,"msg"=>"非法操作")));
		}else{
			$keyword=I("post.keyword");
			$where=array();
			$where['order_serial_number']=array("like","%".$keyword."%");
			$result=M("Order")->where($where)->limit(6)->select();
			exit(json_encode(array("code"=>10123,'msg'=>$result)));
		}
	}
	
	public function insertO(){
		if(!IS_POST){
			exit(json_encode(array("code"=>5,"msg"=>"非法操作")));
		}else{
			$ddsId=I("post.ddsId");
			$where=array();
			$where['order_id']=array("in",implode($ddsId));
			$result=M("Order")->where($where)->select();
			$filesize=0;
			$ids=array();
			foreach($result as $key=>$value){
				$ids[]=$value['order_id'];
				$fz1=$this->ERPOrder($value['order_id']);
				$fz2=$this->downloadOrder($value['order_id']);
				$filesize+=($fz1+$fz2);
				$result[$key]['erp']=U("Order/ERPOrder",array('id'=>$value['order_id']));
				$result[$key]['dow']=U("Order/downloadOrder",array('id'=>$value['order_id']));
			}
			if(isset($_SESSION['fz'])&&!empty($_SESSION['fz'])){
				$filesize+=$_SESSION['fz'];
			}
			session("fz",$filesize);
			$fz=$filesize/1024;
			if($fz>1024){
				$fz=sprintf("%.2f",$fz/1024)."M";
			}else{
				$fz=ceil($fz)."KB";
			}
			exit(json_encode(array("code"=>10123,'msg'=>$result,"fz"=>$fz,"ids"=>implode(",",$ids))));
		}
	}
	
	public function ERPOrder($id){
		$orderInfo=M("Order")->where(array('order_id'=>$id))->find();
		$orderDetail=D("OrderDetail")->getOrderDetailByOrderId($id);
		
		$fileRoot=$_SERVER["DOCUMENT_ROOT"];
		$filename=$fileRoot."/tonetron/Public/Uploads/Excell/".$orderInfo['order_serial_number'].".xls";
		//引入文件
	    Vendor('PHPExcel.PHPExcel');
	    $objPHPExcel = new \PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A1","型號")
					->setCellValue("B1","訂單總數量");
		foreach($orderDetail as $k => $v){
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A".($k+2),$v['model_name'])
					->setCellValue("B".($k+2),$v['total_number']);
        }
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($filename);
		return filesize($filename);
	}
	
	public function downloadOrder($id){
		$orderInfo=M("Order")->where(array('order_id'=>$id))->find();
		$orderDetail=D("OrderDetail")->getOrderDetailByOrderId($id);
		return PDF::createPDF($orderInfo,$orderDetail,1);
	}
	
	public function upload(){
		$type=I("get.type");
		$upload=new Upload();
		$fileInfo=$upload->upload($_FILES['file'],"Attach",array('jpg',"png","jpeg","gif","doc","txt","docx","pdf","xls","xlsx"),5);
		if($fileInfo=="上传文件后缀不允许"){
			exit(json_encode(array("code"=>2,"msg"=>"上传文件后缀不允许")));
		}
		if(trim($fileInfo)=="上传文件大小不符！"){
			exit(json_encode(array("code"=>3,"msg"=>"请上传文件大小不超过5M")));
		}
		$filepath="./Public/".$fileInfo['savepath'].$fileInfo['savename'];
		
		$fzs=filesize($filepath);
		if(isset($_SESSION['fz'])&&!empty($_SESSION['fz'])){
			$fzs+=$_SESSION['fz'];
		}
		session("fz",$fzs);
		$fz=$fzs/1024;
		if($fz>1024){
			$fz=sprintf("%.2f",$fz/1024)."M";
		}else{
			$fz=ceil($fz)."KB";
		}
		
		
		if(empty($filepath)){
			exit(json_encode(array("code"=>4,"msg"=>"上传失败")));
		}else{
			exit(json_encode(array("code"=>0,"msg"=>"上传成功","picPath"=>$fileInfo['savepath'].$fileInfo['savename'],"fz"=>$fz,"fin"=>$fileInfo['name'])));
		}
		
	}
}