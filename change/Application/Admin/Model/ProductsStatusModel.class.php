<?php
namespace Admin\Model;
use Think\Model;
class ProductsStatusModel extends Model {
    public function getAllProductStatus(){
		$info=array();
		$info=$this->select();
		return $info;
	}
}