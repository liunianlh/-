<?php
namespace Admin\Controller;

use Think\Controller;
use Libs\Upload;
use Libs\Authen;

class ProductController extends Controller
{
    public function add()
    {
        if (!isset($_SESSION['adminid'])||empty($_SESSION['adminid'])) {
            $this->redirect("Login/index");
        }
        $rightList=Authen::checkRight();
        if (!IS_POST) {
            if (empty($rightList['m'.MODULE_NAME]['c'.CONTROLLER_NAME]['a'.ACTION_NAME])) {
                $this->error("您没有权限进行此操作");
            }
        } else {
            if (empty($rightList['m'.MODULE_NAME]['c'.CONTROLLER_NAME]['a'.ACTION_NAME])) {
                exit(json_encode(array("code"=>-10000,'msg'=>"您没有权限进行此操作")));
            }
        }
        if (!IS_POST) {
            $productId=I("get.id");
            if (empty($productId)) {
                $productId=0;
            }
            $productInfo=D("Products", "Logic")->getProductInfo('', '', $productId);
            $this->assign("product_info", $productInfo);
            $this->assign("progress_bar", $uploadProgress);
            $this->display();
        } else {
//            var_dump($_POST);
//            die();
            $result=D("Products")->addOrSaveProduct($_POST);
            if (!empty($result[2])) {
                session("product_id", $result[2]);
            }
            switch ($result[1]) {
                case 10090:
                case 10092:
                    exit(json_encode(array("code"=>$result[1],"msg"=>$result[0],"url"=>U('Product/view'))));
                default:
                    exit(json_encode(array("code"=>$result[1],"msg"=>$result[0])));
            }
        }
    }
    public function view()
    {
        if (!isset($_SESSION['adminid'])||empty($_SESSION['adminid'])) {
            $this->redirect("Login/index");
        }
        $rightList=Authen::checkRight();
        if (!IS_POST) {
            if (empty($rightList['m'.MODULE_NAME]['c'.CONTROLLER_NAME]['a'.ACTION_NAME])) {
                $this->error("您没有权限进行此操作");
            }
        } else {
            if (empty($rightList['m'.MODULE_NAME]['c'.CONTROLLER_NAME]['a'.ACTION_NAME])) {
                exit(json_encode(array("code"=>-10000,'msg'=>"您没有权限进行此操作")));
            }
        }
        $pageSize=setProductPageSize();//设置页大小
        $where=setProductWhere();
        $condition=I("post.where");
        if (is_array($condition)) {
            $searchCategory=$condition["searchCategory"];
            $searchJoint=$condition["searchJoint"];
        } else {
            $searchCategory=0;
            $searchJoint=0;
        }
        $category=D("Category")->getAllSerials();
        if (empty($searchCategory)) {
            $joint=D("Category")->getAllSmallJoints();
        } else {
            $joint=D("Category")->getAllSmallJointsBySerialId($searchCategory);
        }

        $productsInfoArray=D("Products")->getAllProducts($pageSize, $where);
        $gradeId=I("request.gradeId");
        $userId=I("request.userId");
        $grade=D("Grade")->getAllGrade();
        if (empty($gradeId)) {
            $gradeId=$grade[0]["grade_id"];
        }
        $userModel=D("User");
        $user=$userModel->getUserByGradeId($gradeId);
        $isExists=$userModel->checkUserIdIsExists($user, $userId);
        if (empty($userId)||$isExists===false) {
            $userId=$user[0]["user_id"];
        }
        $productsInfo=D("Products", "Logic")->getAllSpecificationProductsByCondition($productsInfoArray[0], $gradeId, $userId);
        $info['grade']=$grade;
        $info['grade_id']=$gradeId;
        $info['user']=$user;
        $info['user_id']=$userId;
        $info['spec']=$productsInfo;
        $info['category']=$category;
        $info['joint']=$joint;
        $info['categoryId']=$searchCategory;
        $info['jointId']=$searchJoint;
        $info['page']=$productsInfoArray[1]->show();
        if (!IS_POST) {
            $this->assign("product_info", $info);
            $this->display();
        } else {
            foreach ($info['spec'] as $k=>$v) {//添加链接,以应对URL模式调整
                $info['spec'][$k]["url"]=U("Product/add", array('id'=>$v['products_id']));
            }
            exit(json_encode(array("code"=>10102,"msg"=>$info)));
        }
    }
    public function saveInventory()
    {
        if (!isset($_SESSION['adminid'])||empty($_SESSION['adminid'])) {
            $this->redirect("Login/index");
        }
        $rightList=Authen::checkRight();
        if (!IS_POST) {
            if (empty($rightList['m'.MODULE_NAME]['c'.CONTROLLER_NAME]['a'.ACTION_NAME])) {
                $this->error("您没有权限进行此操作");
            }
        } else {
            if (empty($rightList['m'.MODULE_NAME]['c'.CONTROLLER_NAME]['a'.ACTION_NAME])) {
                exit(json_encode(array("code"=>-10000,'msg'=>"您没有权限进行此操作")));
            }
        }
        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $data=I("post.data");
            foreach ($data as $key=>$value) {
                $data2=array(
                    "inventory"=>$value['inventory'],
                    "spec_img"=>$value['specImg']
                );
                M("Specification")->where(array("specification_id"=>$value['specId']))->save($data2);
            }
            exit(json_encode(array("code"=>10103,"msg"=>"保存数据成功")));
        }
    }
    public function upload()
    {
        $upload=new Upload();
        $picInfo=$upload->upload($_FILES['file'], "products");
        $miniPic=$upload->create_miniimage($picInfo, 200, 200);
        exit(json_encode(array("code"=>0,"msg"=>"上传成功","picPath"=>$miniPic)));
    }
    public function getProductByGradeId()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $gradeId=I("post.gradeId");
            $userId=I("post.userId");
            $productId=I("post.productId");
            $productInfo=D("Products", "Logic")->getProductInfo($gradeId, $userId, $productId);
            exit(json_encode(array("code"=>10102,"msg"=>$productInfo)));
        }
    }
    public function batchSet()
    {
        if (!isset($_SESSION['adminid'])||empty($_SESSION['adminid'])) {
            $this->redirect("Login/index");
        }
        $rightList=Authen::checkRight();
        if (!IS_POST) {
            if (empty($rightList['m'.MODULE_NAME]['c'.CONTROLLER_NAME]['a'.ACTION_NAME])) {
                $this->error("您没有权限进行此操作");
            }
        } else {
            if (empty($rightList['m'.MODULE_NAME]['c'.CONTROLLER_NAME]['a'.ACTION_NAME])) {
                exit(json_encode(array("code"=>-10000,'msg'=>"您没有权限进行此操作")));
            }
        }
        $searchModel=I("post.searchModel");
        $where=array();
        if (empty($searchModel)) {
            $where="1=1";
        } else {
            $where['model_name']=array("like","%".$searchModel."%");
        }
        $specInfo=D("Specification")->getAllSpecification($where);
        $gradeInfo=D("GradeProduct")->getAllGradeProduct();
        $groupGrade=D("GradeProduct")->groupGradeProduct($gradeInfo);

        $afterAdjustGroupGrade=D("Specification", "Logic")->adjustSpecification($specInfo, $groupGrade);
        foreach ($afterAdjustGroupGrade as $k=>$v) {
            foreach ($v['value'] as $kk=>$vv) {
                $checkKey=$vv['gps_id'].":".$vv['grade_id'].":".$vv['specification_id'];
                $afterAdjustGroupGrade[$k]['value'][$kk]["_hashKey"]=md5($checkKey).":".$checkKey;
            }
        }
        if (!IS_POST) {
            //print_r($afterAdjustGroupGrade);die;
            $this->assign("spec_info", $specInfo);
            $this->assign("grade_info", $afterAdjustGroupGrade);
            $this->display();
        } else {
            exit(json_encode(array("code"=>10104,"msg"=>array("spec_info"=>$specInfo,"grade_info"=>$afterAdjustGroupGrade))));
        }
    }
    public function batchSetSave()
    {
        if (!IS_POST) {
            exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
        } else {
            $gps=I("post.gps");
            D("GradeProduct")->saveGradeProduct($gps);
            exit(json_encode(array("code"=>10104,"msg"=>"保存数据成功")));
        }
    }
}
