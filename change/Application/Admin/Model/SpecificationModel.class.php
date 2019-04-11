<?php
namespace Admin\Model;

use Think\Model;

class SpecificationModel extends Model
{
    public function addOrSaveProduct($productsId, $data, $post)
    {
        $data=$this->checkData($data);
        $categoryModel=D("Category");
        $colorModel=D("Color");
        foreach ($data as $key=>$value) {
            $temp=array(
                "serial_id"=>$value['protype'],
                "serial_name"=>$categoryModel->getCategoryNameById($value['protype']),
                "serial_name2"=>$categoryModel->getCategoryName2ById($value['protype']),
                "joint_id"=>$value['jietou'],
                "joint_name"=>$categoryModel->getCategoryNameById($value['jietou']),
                "joint_name2"=>$categoryModel->getCategoryName2ById($value['jietou']),
                "model_name"=>$value['model'],
                "color_id"=>$value['color'],
                "color_name"=>$colorModel->getColorNameById($value['color']),
                "color_name2"=>$colorModel->getColorName2ById($value['color']),
                "length"=>$value['length'],
                "rough_weight"=>$value['mao_weight'],
                "net_weight"=>$value['jing_weight'],
                "volume"=>$value['cai_volume'],
                "loading"=>$value['zx_number'],
                "inventory"=>$value['kc_number'],
                "products_id"=>$productsId
            );
            if (empty($value['specId'])) {
                $temp['time']=time();
                $specId=$this->add($temp);
            } else {
                $specId=$value['specId'];
                $res=$this->where(array("specification_id"=>$value['specId']))->save($temp);
            }
            $gpsId=D("GradeProduct")->addOrSaveProduct($specId, $value, $post);
            $uppId=D("UserProduct")->addOrSaveProduct($specId, $value, $post);
        }
    }
    public function checkData($data)
    {
        foreach ($data as $key=>$value) {
            if (empty($value['protype'])) {
                unset($data[$key]);
                continue;
            }
            if (empty($value['jietou'])) {
                unset($data[$key]);
                continue;
            }
            if (empty($value['model'])) {
                unset($data[$key]);
                continue;
            }
            if (empty($value['rmb1'])&&empty($value['usd1'])) {
                $data[$key]['rmb1']=0;
                $data[$key]['usd1']=0;
            }
            if (empty($value['rmb2'])&&empty($value['usd2'])) {
                $data[$key]['rmb2']=0;
                $data[$key]['usd2']=0;
            }
        }
        return $data;
    }
    public function getProductSpecificationInfoByProductId($productId)
    {
        $where=array();
        if (is_array($productId)) {
            $where['products_id']=array("in",implode(',', $productId));
        } else {
            $where['products_id']=$productId;
        }
        $specInfo=$this->where($where)->order("serial_id asc,joint_id asc")->select();
        $categoryModel=D("Admin/Category");
        $uppModel=D("Admin/UserProduct");
        $gpsModel=D("Admin/GradeProduct");
        foreach ($specInfo as $key=>$value) {
            $specInfo[$key]=$this->checkUpdateSpecificationByInfo($value);
            $uppInfo=$uppModel->getAllPricesBySpecificationId($value['specification_id']);
            $gpsInfo=$gpsModel->getAllPricesBySpecificationId($value['specification_id']);
            $specInfo[$key]['user_product']=$uppInfo;
            $specInfo[$key]['grade_product']=$gpsInfo;
        }
        return $specInfo;
    }
    public function delSpecificationById($specId)
    {
        $res=$this->where(array("specification_id"=>$specId))->delete();
        if ($res!==false) {
            D("UserProduct")->delUserProductBySpecificationId($specId);
            D("GradeProduct")->delGradeProductBySpecificationId($specId);
        }
        return $res;
    }
    public function checkUpdateSpecificationByInfo($specInfo)
    {
        $categoryModel=D("Admin/Category");
        $serialId=$specInfo['serial_id'];
        $jointId=$specInfo['joint_id'];
        $serialName=$categoryModel->getCategoryNameById($serialId);
        $jointName=$categoryModel->getCategoryNameById($jointId);
        if (($serialName!=$specInfo['serial_name'])||($jointName!=$specInfo['joint_name'])) {//更新冗余信息
            $data=array(
                "serial_name"=>$serialName,
                "joint_name"=>$jointName
            );
            $this->where(array("specification_id"=>$value['specification_id']))->save($data);
        }
        $specInfo['serial_name']=$serialName;
        $specInfo['joint_name']=$jointName;
        return $specInfo;
    }
    public function getAllSpecification($where)
    {
        $specInfo=$this->where($where)->order("serial_id asc,joint_id asc")->select();
        return $specInfo;
    }
    public function getAllSpecification2()
    {
        $where=setProductWhere();
        $specInfo=M("Specification")
                ->join("left join __PRODUCTS__ on __SPECIFICATION__.products_id=__PRODUCTS__.products_id")
                ->where($where)->order("serial_id asc,joint_id asc")->select();
        return $specInfo;
    }
    public function getOneSpecInfoBySUID($specId, $userId)
    {
        $where=array();
        $where['specification_id']=$specId;
        $specInfo=$this->where($where)->find();
        $productsId=$specInfo["products_id"];
        $productInfo=M("Products")->where(array("products_id"=>$productsId))->find();
        $specInfo['products_img']=$productInfo['products_img'];
        $specInfo['products_chinese_name']=$productInfo['products_chinese_name'];
        $specInfo['products_english_name']=$productInfo['products_english_name'];
        $userInfo=D("User")->getUserInfoByUserId($userId);
        $gradeId=$userInfo['grade_id'];
        $uppInfo=D("UserProduct")->getUserProductInfoBySUId($specId, $userId);
        if (empty($uppInfo)||$uppInfo['rmb']<=0) {
            $gpsInfo=D("GradeProduct")->getGradeProductInfoBySGId($specId, $gradeId);
            if (empty($gpsInfo)||$gpsInfo['rmb']<=0) {
                $specInfo['rmb']=9999999;
            } else {
                $specInfo['rmb']=$gpsInfo['rmb'];
            }
        } else {
            $specInfo['rmb']=$uppInfo['rmb'];
        }
        return $specInfo;
    }
}
