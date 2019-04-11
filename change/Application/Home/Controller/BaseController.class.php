<?php
namespace Home\Controller;

use Think\Controller;

class BaseController extends Controller
{
    public function _initialize()
    {
        if (!isset($_SESSION['userid'])||empty($_SESSION['userid'])) {
            $this->redirect("Login/index");
        }
        $cartNum=$this->getCartNum();
        $shoucang=$this->getCollect();

        $userInfoTop=D("User")
                ->join("left join __COMPANY__ on __COMPANY__.user_id=__USER__.user_id")
                ->field(array(
                    "www_user.*",
                    "www_company.company_contacts"
                ))
                ->where(array("www_user.user_id"=>$_SESSION['userid']))
                ->find();
        $userInfoTop['contract_flag']=0;//正常
        if (empty($userInfoTop['contract_time'])) {
            $userInfoTop['contract_flag']=1;//没有审核
        } else {
            if ($userInfoTop['contract_time']<=strtotime("-1 month")) {
                $userInfoTop['contract_flag']=2;//快到期
            }
            if (time()>$userInfoTop['contract_time']) {
                $userInfoTop['contract_flag']=3;//已到期
            }
        }
        $langSet=cookie('think_language');
        if (empty($langSet)) {
            $langSet="zh-cn";
        } else {
            $langSet=$langSet;
        }
        if (strtolower($langSet)=="zh-cn") {
            $this->langFlag=1;
        } else {
            $this->langFlag=2;
        }
        $msgCount=$this->getMessage();
        $this->assign("msgCount", $msgCount);
        $this->assign("user_info_top", $userInfoTop);
        $this->assign("cartNum", $cartNum);

        $this->assign("shoucang", $shoucang);

        $test = D("config")->where(array("www_config.config_id"=>9))->find();
        $this->assign("logo", $test);
        $test2 = D("config")->where(array("www_config.config_id"=>10))->find();
        $this->assign("logo_botton", $test2);
    }
    public function getCartNum()
    {
        $cartNum=0;
        if (isset($_SESSION['cart'])&&!empty($_SESSION['cart'])) {
            $cartNum=count($_SESSION['cart']);
        }
        return $cartNum;
    }


    public function getCollect()
    {
        $collect=0;
        if (isset($_SESSION['collect'])&&!empty($_SESSION['collect'])) {
            $collect=count($_SESSION['collect']);
        }
        return $collect;
    }


    public function getMessage()
    {
        $where=array();
        $where['msg_to']=$_SESSION['userid'];
        $where['is_read']=1;
        $count=M("Message")->where($where)->count();
        return $count;
    }
}
