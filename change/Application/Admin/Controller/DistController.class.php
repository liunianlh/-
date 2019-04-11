<?php
namespace Admin\Controller;
use Think\Controller;
class DistController extends BaseController {
    public function index(){
		$count=M("Area")->count();
		$page=new \Think\Page($count,50);
		$page->rollPage=7;
		$distInfo=M("Area")
					->limit($page->firstRow.",".$page->listRows)
					->select();
		$this->assign("page",$page->show());
		$this->assign("dist_info",$distInfo);
		$this->display();
	}
	public function edit(){
		if(!IS_POST){
			$id=I("get.id");
			$where=array();
			$where["area_id"]=$id;
			$distInfo=M("Area")->where($where)->find();;
			$this->assign("dist_info",$distInfo);
			$this->display();
		}else{
			$id=I("post.id");
			$chineseName=I("post.chineseName");
			$englishName=I("post.englishName");
			$chineseName=trim($chineseName);
			$englishName=trim($englishName);
			if(empty($chineseName)){
				exit(json_encode(array("code"=>10120,'msg'=>"中文名称不能为空")));
			}
			if(empty($englishName)){
				exit(json_encode(array("code"=>10121,'msg'=>"英文名称不能为空")));
			}
			$data=array(
				"area_name"=>$chineseName,
				"area_name2"=>$englishName
			);
			$res=M("Area")->where(array('area_id'=>$id))->save($data);
			if($res!==false){
				exit(json_encode(array("code"=>10125,'msg'=>"更新成功","url"=>U('Dist/index'))));
			}else{
				exit(json_encode(array("code"=>10126,'msg'=>"更新失败,请稍后重试...")));
			}
		}
	}
}