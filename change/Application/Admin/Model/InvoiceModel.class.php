<?php
namespace Admin\Model;
use Think\Model;
class InvoiceModel extends Model {
    public function getAllInvoicesByUserId($userId){
		$info=$this->where(array("user_id"=>$userId))->select();
		foreach($info as $key=>$value){
			$info[$key]["checkKey"]=md5($value['invoice_id'].$value['user_id']);
			if($value['invoice_type_id']==2){
				if(!empty($value['invoice_addon'])){
					$invoiceAddon=json_decode($value['invoice_addon'],true);
					$info[$key]["addon"]=$invoiceAddon;
				}
			}
		}
		return $info;
	}
	public function getInvoiceByInvoiceId($userId,$invoiceId){
		$info=$this->getInvoiceInfo($userId,$invoiceId);
		if($info['invoice_type_id']==2){
			if(!empty($info['invoice_addon'])){
				$invoiceAddon=json_decode($info['invoice_addon'],true);
				$info["addon"]=$invoiceAddon;
			}
		}
		return $info;
	}
	public function getInvoiceCountByUserId($userId){
		$count=$this->where(array("user_id"=>$userId))->count();
		return $count;
	}
	public function delInvoiceByInvoiceId($userId,$invoiceId){
		$info=$this->getInvoiceInfo($userId,$invoiceId);
		if($info['invoice_type_id']==2){
			if(!empty($info['invoice_addon'])){
				$invoiceAddon=json_decode($info['invoice_addon'],true);
				$data=$invoiceAddon;
				foreach($data as $key=>$value){
					$picPath=$value["name"];
					$filePath=$_SERVER['DOCUMENT_ROOT']."/Public/".$picPath;
					if(file_exists($filePath)){
						@unlink($filePath);
					}
				}
			}
		}
		$res=$this->where(array("user_id"=>$userId,"invoice_id"=>$invoiceId))->delete();
		return $res;
	}
	public function getInvoiceInfo($userId,$invoiceId){
		$info=$this->where(array("user_id"=>$userId,"invoice_id"=>$invoiceId))->find();
		return $info;
	}
}