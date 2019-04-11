<?php
namespace Admin\Controller;
use Think\Controller;
class SpecificationController extends BaseController {
    public function del(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10100,"msg"=>"非法操作")));
		}else{
			$specId=I("post.specId");
			if(empty($specId)){
				exit(json_encode(array("code"=>10101,"msg"=>"删除失败")));
			}else{
				$where=array();
				$where['specification_id']=$specId;
				$specInfo=M("Specification")->where($where)->find();
				$res=D("Specification")->delSpecificationById($specId);
				if(false!==$res){
					if(!empty($specInfo['products_id'])){
						$where2=array();
						$where2['products_id']=$specInfo['products_id'];
						$count=M("Specification")->where($where2)->count();
						if($count==0){
							M("Products")->where($where2)->delete();
							exit(json_encode(array("code"=>10103,"msg"=>"删除成功")));
						}else{
							exit(json_encode(array("code"=>10102,"msg"=>"删除成功")));
						}
					}
				}else{
					exit(json_encode(array("code"=>10101,"msg"=>"删除失败")));
				}
			}
		}
	}
}