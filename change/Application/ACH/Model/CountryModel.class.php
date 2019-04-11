<?php
namespace ACH\Model;
use Think\Model;
class CountryModel extends Model {
    public function getAllCountry(){
		$info=array();
		$info=$this->select();
		return $info;
	}
}