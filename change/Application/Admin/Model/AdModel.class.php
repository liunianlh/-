<?php
namespace Admin\Model;
use Think\Model;
class AdModel extends Model {
    public function getAllIndexAd(){
		$adInfo=$this->where(array("ad_type"=>1))->select();
		return $adInfo;
	}
}