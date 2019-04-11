<?php
namespace Admin\Controller;
use Think\Controller;
class TextController extends BaseController {
    public function index(){
		$textInfo=D("Cn")->getAllText();
		$this->assign("text_info",$textInfo);
		$this->display();
	}
	public function add(){
		if(!IS_POST){
			$this->display();
		}else{
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
				"chinese"=>$chineseName,
				"english"=>$englishName,
				"fixed"=>2
			);
			$cnId=M("Cn")->add($data);
			if($cnId){
				$data2=array(
					"variable"=>md5($cnId),
				);
				$res=M("Cn")->where(array("cn_id"=>$cnId))->save($data2);
				$this->writeCn();
				exit(json_encode(array("code"=>10123,'msg'=>"添加成功","url"=>U('Text/index'))));
			}else{
				exit(json_encode(array("code"=>10124,'msg'=>"添加失败,请稍后重试...")));
			}
		}
	}
	public function edit(){
		if(!IS_POST){
			$id=I("get.id");
			$textInfo=D("Cn")->getTextInfoById($id);
			$this->assign("text_info",$textInfo);
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
				"chinese"=>$chineseName,
				"english"=>$englishName
			);
			$res=M("Cn")->where(array('cn_id'=>$id))->save($data);
			if($res!==false){
				$this->writeCn();
				exit(json_encode(array("code"=>10125,'msg'=>"更新成功","url"=>U('Text/index'))));
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
			$textInfo=D("Cn")->getTextInfoById($id);
			if($textInfo['fixed']==1){
				exit(json_encode(array("code"=>10128,'msg'=>"内置文字不可以删除")));
			}
			$res=M("Cn")->where(array('cn_id'=>$id))->delete();
			if($res!==false){
				$this->writeCn();
				exit(json_encode(array("code"=>10129,'msg'=>"删除成功","url"=>U('Text/index'))));
			}else{
				exit(json_encode(array("code"=>10128,'msg'=>"删除失败，请稍后重试...")));
			}
		}
	}
	private function writeCn(){
		$textInfo=D("Cn")->getAllText();
		
		$ctext='<'."?php\r\n return array(\r\n";
		$ntext='<'."?php\r\n return array(\r\n";
		foreach($textInfo as $key=>$value){
			$ctext.='"'.$value['variable'].'"=>'.'"'.$value['chinese'].'",'."\r\n";
			$ntext.='"'.$value['variable'].'"=>'.'"'.$value['english'].'",'."\r\n";
		}
		$ctext.=")\r\n?".">";
		$ntext.=")\r\n?".">";
		$cfile=APP_PATH.'Home/Lang/zh-cn.php';
		$nfile=APP_PATH.'Home/Lang/en-us.php';
		file_put_contents($cfile,$ctext);
		file_put_contents($nfile,$ntext);
	}
}