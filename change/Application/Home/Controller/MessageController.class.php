<?php
namespace Home\Controller;
class MessageController extends BaseController {
    public function mlist(){
		$where=setSearchMsg();
		$keyword=I("get.keyword");
		if(!empty($keyword)){
			$where['msg_title|msg_content']=array('like',"%".$keyword."%");
		}
		$count=M("Message")->where($where)->count();
		$page=new \Think\Page($count,20);
		$msgInfo=M("Message")
				->where($where)
				->order("is_read asc,msg_time desc")
				->limit($page->firstRow.",".$page->listRows)
				->select();
		if(!empty($keyword)){
			foreach($msgInfo as $key=>$value){
				$msgInfo[$key]['msg_title']=str_replace($keyword,"<font color='red'>".$keyword."</font>",$value['msg_title']);
			}
			$this->assign("keyword",$keyword);
		}
		$this->assign("msg_info",$msgInfo);
		$this->assign("page",$page->show());
		$this->display();
	}
	public function mdetail(){
		$id=I("get.id");
		$where=array();
		$where['message_id']=$id;
		$msgInfo=M("Message")->where($where)->find();
		if($msgInfo['is_read']==1){
			$data=array(
				"is_read"=>2
			);
			$res=M("Message")->where($where)->save($data);
			$this->msgCount=$this->msgCount-1;
		}
		if($msgInfo['has_attachment']==2){
			$attachInfo=M("Attach")->where(array("msg_id"=>$msgInfo['message_id'],"attach_type"=>1))->find();
			$msgInfo['xls']=$attachInfo["xls"];
			$msgInfo['pdf']=$attachInfo["pdf"];
			$msgInfo['order_id']=$attachInfo["order_id"];
			
			$attachInfo2=M("Attach")->where(array("msg_id"=>$msgInfo['message_id'],"attach_type"=>2))->select();
			$msgInfo['attach']=$attachInfo2;
		}
		$this->assign("msg_info",$msgInfo);
		$this->display();
	}
	public function download(){
		$id=I("get.id");
		$info=M("Attach")->where(array("attach_id"=>$id))->find();
		header("Content-type:text/html;charset=utf-8");
		$fileName=$_SERVER['DOCUMENT_ROOT']."/tonetron/Public/".$info['xls']; 
		if(!file_exists($fileName)){ 
			return ; 
		} 
		$fp=fopen($fileName,"r"); 
		$file_size=filesize($fileName);
		header("Content-type: application/octet-stream");
		header("Accept-Ranges: bytes");
		header("Accept-Length:".$file_size);
		header("Content-Disposition: attachment; filename=".str_replace(" ","_",$info['pdf'])); 
		$buffer=1024; 
		$file_count=0; 
		while(!feof($fp) && $file_count<$file_size){ 
			$file_con=fread($fp,$buffer); 
			$file_count+=$buffer; 
			echo $file_con; 
		} 
		fclose($fp);
	}
}