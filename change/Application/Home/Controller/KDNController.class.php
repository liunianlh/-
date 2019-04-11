<?php
namespace Home\Controller;
use Think\Controller;
use Libs\KDN;
class KDNController extends Controller {
    public function index(){
		if(!IS_POST){
			exit(json_encode(array("code"=>"10152","msg"=>"非法请求")));
		}else{
			$postCom = trim($_POST["com"]);
			$postNu = trim($_POST["nu"]);
			$res=KDN::getOrderTracesByJson($postNu,$postCom);
			exit(json_encode(array("code"=>"10153","msg"=>json_decode($res,true))));
		}
	}
}