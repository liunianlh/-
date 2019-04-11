<?php
namespace Admin\Controller;
use Think\Controller;
class FobController extends BaseController {
    public function index(){
        $gradeInfo=M("Fob")->select();
        $this->assign("grade_info",$gradeInfo);
        $this->display();
    }
    public function edit(){
        if(!IS_POST){
            $id=I("get.id");
            $gradeInfo=M("Fob")->where("id=$id")->find();
            $this->assign("grade_info",$gradeInfo);
            $this->display();
        }else{
            $id=I("post.id");
            $name=I("post.name");
            $fob=I("post.fob");
            $name=trim($name);
            $fob=trim($fob);
            if(empty($name)){
                exit(json_encode(array("code"=>10120,'msg'=>"系统设置不能为空")));
            }
            if(empty($fob)){
                exit(json_encode(array("code"=>10121,'msg'=>"FOB不能为空")));
            }
            $data=array(
                "name"=>$name,
                "fob"=>$fob
            );
            $res=M("fob")->where(array('id'=>$id))->save($data);
            if($res!==false){
                exit(json_encode(array("code"=>10125,'msg'=>"更新成功","url"=>U('fob/index'))));
            }else{
                exit(json_encode(array("code"=>10126,'msg'=>"更新失败,请稍后重试...")));
            }
        }
    }

}