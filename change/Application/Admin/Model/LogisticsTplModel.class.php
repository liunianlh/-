<?php
namespace Admin\Model;
use Think\Model;
class LogisticsTplModel extends Model {
    public function getAllLogisticsTpl(){
		$logisticsTpl=$this->distinct(true)->field("logistics_tpl_flag,logistics_tpl_name")->select();
		return $logisticsTpl;
	}
	public function getAllLogisticsTplInfosByFlag($logisticsTplFlag){
		$logisticsTplInfo=$this->where(array("logistics_tpl_flag"=>$logisticsTplFlag))->select();
		return $logisticsTplInfo;
	}
}