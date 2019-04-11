<?php
namespace Admin\Controller;
use Think\Controller;
use Libs\Upload;
class CategoryController extends BaseController {
    public function getAllSmallJoints(){
		if(!IS_POST){
			exit(json_encode(array("status"=>0,"info"=>"fail")));
		}
		$serialId=I("post.sid");
		$info=D('Category')->getAllSmallJointsBySerialId($serialId);
		exit(json_encode(array("status"=>1,"info"=>$info)));
	}
	public function index(){
		$categoryModel=D("Category");
		// $categoryInfo=$categoryModel->getAllCategory();
		$categoryInfo=M("Category")->order(array("cat_sort asc"))->select();
		foreach($categoryInfo as $key=>$value){
			$categoryInfo[$key]['cat_parent']=$categoryModel->getCategoryNameFromCategoryInfoById($categoryInfo,$value['cat_pid']);
			switch($value['cat_level']){
				case 1:
					$categoryInfo[$key]['cat_level']="系列";
					break;
				case 2:
					$categoryInfo[$key]['cat_level']="接头大类";
					break;
				case 3:
					$categoryInfo[$key]['cat_level']="接头小类";
					break;
				default:
					$categoryInfo[$key]['cat_level']="未知";
					break;
			}
		}
		$info=deal_array($categoryInfo);
		$this->assign("category_info",$info);
		$this->display();
	}
	public function add(){
		if(!IS_POST){
			$categoryInfo=D("Category")->getAllCategory();
			$categoryInfo=deal_array($categoryInfo);
			$this->assign("category_info",$categoryInfo);
			$this->display();
		}else{
			$chineseName=I("post.chineseName");
			$englishName=I("post.englishName");
			$catImg=I("post.catImg");
			$catLink=I("post.catLink");
			$catPid=I("post.catPid");
			if(empty($chineseName)){
				exit(json_encode(array("code"=>10120,'msg'=>"名称不能为空")));
			}
			$categoryModel=D("Category");
			$count=$categoryModel->checkCategory($chineseName,$catPid);
			if($count>0){
				exit(json_encode(array("code"=>10122,'msg'=>"此中文名称已经存在，不要重复添加")));
			}
			$data=array(
				"cat_chinese_name"=>$chineseName,
				"cat_english_name"=>$englishName,
				"cat_img"=>trim($catImg),
				"cat_link"=>$catLink,
				"cat_pid"=>$catPid,
				"cat_level"=>$categoryModel->getCatLevelByCatPid($catPid)
			);
			$categoryId=M("Category")->add($data);
			if($categoryId){
				exit(json_encode(array("code"=>10123,'msg'=>"添加成功","url"=>U('Category/index'))));
			}else{
				exit(json_encode(array("code"=>10124,'msg'=>"添加失败,请稍后重试...")));
			}
		}
	}
	public function edit(){
		if(!IS_POST){
			$id=I("get.id");
			$categoryModel=D("Category");
			$categoryInfo=$categoryModel->getSerialBySerialId($id);
			$categoryInfos=$categoryModel->getAllCategory();
			$categoryInfos=deal_array($categoryInfos);
			$this->assign("category_info",$categoryInfo);
			$this->assign("category_infos",$categoryInfos);
			$this->display();
		}else{
			$id=I("post.id");
			$chineseName=I("post.chineseName");
			$englishName=I("post.englishName");
			$catImg=I("post.catImg");
			$catLink=I("post.catLink");
			$catPid=I("post.catPid");
			if(empty($chineseName)){
				exit(json_encode(array("code"=>10120,'msg'=>"名称不能为空")));
			}
			$categoryModel=D("Category");
			$categoryInfo=$categoryModel->getSerialBySerialId($id);
			if($categoryInfo['cat_chinese_name']!=$chineseName){
				$count=$categoryModel->checkCategory($chineseName,$catPid);
				if($count>0){
					exit(json_encode(array("code"=>10122,'msg'=>"此中文名称已经存在，不要重复添加")));
				}
			}
			$data=array(
				"cat_chinese_name"=>$chineseName,
				"cat_english_name"=>$englishName,
				"cat_img"=>trim($catImg),
				"cat_link"=>$catLink,
				"cat_pid"=>$catPid,
				"cat_level"=>$categoryModel->getCatLevelByCatPid($catPid)
			);
			$res=M("Category")->where(array('category_id'=>$id))->save($data);
			if($res!==false){
				$catInfo=M("Category")->where(array('category_id'=>$id))->find();
				if($catInfo['cat_level']==1){//系列
					$data2=array(
						"serial_name"=>$chineseName,
						"serial_name2"=>$englishName
					);
					M("Specification")->where(array('serial_id'=>$id))->save($data2);
				}
				if($catInfo['cat_level']==3){//接头
					$data3=array(
						"joint_name"=>$chineseName,
						"joint_name2"=>$englishName
					);
					M("Specification")->where(array('joint_id'=>$id))->save($data3);
				}
				
				exit(json_encode(array("code"=>10125,'msg'=>"更新成功","url"=>U('Category/index'))));
			}else{
				exit(json_encode(array("code"=>10126,'msg'=>"更新失败,请稍后重试...")));
			}
		}
	}
	public function del(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10127,'msg'=>"非法操作")));
		}else{
			$id=I("get.id");
			$key=I("post.key");
			if(md5($id)!=$key){
				exit(json_encode(array("code"=>10128,'msg'=>"删除失败，请稍后重试...")));
			}
			
			$count=M("Category")->where(array('cat_pid'=>$id))->count();
			if($count<=0){
				$count2=M("Specification")->where(array("joint_id"=>$id))->count();
				if($count2<=0){
					$res=M("Category")->where(array('category_id'=>$id))->delete();
					if($res!==false){
						exit(json_encode(array("code"=>10129,'msg'=>"删除成功","url"=>U('Category/index'))));
					}else{
						exit(json_encode(array("code"=>10128,'msg'=>"删除失败，请稍后重试...")));
					}
				}else{
					exit(json_encode(array("code"=>10128,'msg'=>"此分类下还有产品，不能删除")));
				}
			}else{
				exit(json_encode(array("code"=>10128,'msg'=>"此系列下还有子分类,不能删除")));
			}
		}
	}
	public function order(){
		if(!IS_POST){
			exit(json_encode(array("code"=>10127,'msg'=>"非法操作")));
		}else{
			$sort=I("post.sort");
			foreach($sort as $key=>$value){
				$where=array("category_id"=>$value['name']);
				$data=array("cat_sort"=>$value['value']);
				M("Category")->where($where)->save($data);
			}
			exit(json_encode(array("code"=>10129,'msg'=>"排序成功","url"=>U('Category/index'))));
		}
	}
	public function upload(){
		$upload=new Upload();
		$picInfo=$upload->upload($_FILES['file'],"Category",array('jpg',"png","jpeg","gif"),5);
		if($picInfo=="上传文件后缀不允许"){
			exit(json_encode(array("code"=>2,"msg"=>"请上传jpg或者jpeg图片")));
		}
		if(trim($picInfo)=="上传文件大小不符！"){
			exit(json_encode(array("code"=>3,"msg"=>"请上传文件大小不超过5M")));
		}
		$miniPic=$upload->create_miniimage($picInfo,190,126);
		if(empty($miniPic)){
			exit(json_encode(array("code"=>4,"msg"=>"上传失败")));
		}else{
			exit(json_encode(array("code"=>0,"msg"=>"上传成功","picPath"=>$miniPic)));
		}
		
	}
}