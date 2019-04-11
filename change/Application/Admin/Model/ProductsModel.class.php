<?php
namespace Admin\Model;

use Think\Model;

class ProductsModel extends Model
{
    private $msg='';
    private $code='';
    public function addOrSaveProduct($post)
    {
        $proId=$post['proId'];
        $chinese=$post['chinese'];
        $english=$post['english'];
        $modellist=$post['modellist'];
        $productsId=$post['dataId'];
        $productsLink=$post['productsLink'];
        $productseLink=$post['productseLink'];
        $data=array(
            "products_img"=>$proId,
            "products_chinese_name"=>$chinese,
            "products_english_name"=>$english,
            "products_link"=>$productsLink,
            "productse_link"=>$productseLink,
        );
//        var_dump($data);
//die();
        if (empty($productsId)) {
            $data['products_time']=time();
            $productsId=$this->add($data);
            if ($productsId) {
                $this->msg="添加成功";
                $this->code=10090;
            } else {
                $this->msg="添加失败,请稍后重试...";
                $this->code=10091;
            }
        } else {
            $res=$this->where(array("products_id"=>$productsId))->save($data);
            if ($res!==false) {
                $this->msg=$modellist[0]['usd1']."更新成功".$modellist['usd1'];
                $this->code=10092;
            } else {
                $this->msg="更新失败,请稍后重试...";
                $this->code=10093;
            }
        }
        D("specification")->addOrSaveProduct($productsId, $modellist, $post);
        return array($this->msg,$this->code,$productsId);
    }
    public function getProductInfoById($productsId)
    {
        $productInfo=$this->where(array("products_id"=>$productsId))->find();
        $specInfo=D("Specification")->getProductSpecificationInfoByProductId($productsId);
        $productInfo['spec']=$specInfo;
        return $productInfo;
    }
    public function getAllProducts($pageSize, $where)
    {
        $count=M("Products")
                ->join("left join __SPECIFICATION__ on __SPECIFICATION__.products_id=__PRODUCTS__.products_id")
                ->where($where)->count();
        $page=new \Think\Page($count, $pageSize);
        $productsInfo=M("Products")
                    ->join("left join __SPECIFICATION__ on __SPECIFICATION__.products_id=__PRODUCTS__.products_id")
                    ->order("serial_id asc,joint_id asc")
                    ->where($where)->limit($page->firstRow.",".$page->listRows)->select();
        return array($productsInfo,$page);
    }
    public function getProductsList($productsInfo)
    {
        $productsIds=array();
        foreach ($productsInfo as $key=>$value) {
            $productsIds[]=$value['products_id'];//储存id，减少查询数据库次数
        }
        $specificationInfo=D("Admin/Specification")->getProductSpecificationInfoByProductId($productsIds);
        $productsInfo=D("Admin/Products", "Logic")->getProductsList($productsInfo, $specificationInfo);
        return $productsInfo;
    }
    public function getOnlyProductInfoById($productsId)
    {
        return $this->where(array("products_id"=>$productsId))->find();
    }
}
