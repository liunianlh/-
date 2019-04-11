<?php
namespace Home\Controller;

use Think\Controller;
use Libs\Logger;

class LoginController extends Controller
{
    public function index()
    {
//        return json_encode(array("code"=>10032,"info"=>"登陆成功","url"=>U('Index/index')));
        if (!IS_POST) {
            $duid=cookie("duid");
            $dpwd=cookie("dpwd");
            if (!empty($duid)) {
                $this->assign("duid", decode($duid));
            }
            if (!empty($dpwd)) {
                $this->assign("dpwd", decode($dpwd));
            }
            $this->display();
        } else {
            $loginError=Logger::loginErrorNumber();
            // if($loginError>=5){
            //    exit(json_encode(array("code"=>10033,"info"=>"错误次数过多，今日已禁止登陆！或者找回密码登陆")));
            // }
            $uid=I("post.email");
            $pwd=I("post.pwd");
            $duid=I("post.duid");
            $dpwd=I("post.dpwd");
            if (empty($uid)) {
                exit(json_encode(array("code"=>10030,"info"=>"请输入UID或者邮箱")));
            }
            if (empty($pwd)) {
                exit(json_encode(array("code"=>10030,"info"=>"请输入密码")));
            }
            if (strlen($pwd)<6||strlen($pwd)>12) {
                $loginError=Logger::loginError();
                exit(json_encode(array("code"=>10031,"info"=>"UID/邮箱或者密码错误","loginerror"=>$loginError)));
            }
            $user=D("Admin/User");
            $userInfo=$user->getUserInfoByUidOrEmail($uid);
            if (empty($userInfo)) {
                $loginError=Logger::loginError();
                exit(json_encode(array("code"=>10031,"info"=>"UID/邮箱或者密码错误","loginerror"=>$loginError)));
            }
            $result=$user->checkLogin($userInfo, $pwd);
            if ($result) {
                if ($result['account_status_id']==2) {
                    exit(json_encode(array("code"=>10034,"info"=>"账号正在审核中，请耐心等待")));
                }
                if ($result['account_status_id']==3) {
                    exit(json_encode(array("code"=>10035,"info"=>"账号已失效，请联系管理员")));
                }
                if ($result['account_status_id']==4) {
                    $rnd=str_replace("=", "", base64_encode(md5($result['user_verify'].$result['user_uid'])));
                    session("uuu", array($result['user_verify'],$result['user_uid'],$result['user_id']));
                    exit(json_encode(array("code"=>10036,"info"=>"账号审核未通过","url"=>U('Register/fail', array("rnd"=>$rnd)))));
                }
                session("userid", $userInfo['user_id']);
                if (!empty($duid)) {
                    cookie("duid", encode($uid), 7*24*3600);
                } else {
                    cookie("duid", null, time()-7*24);
                }
                if (!empty($dpwd)) {
                    cookie("dpwd", encode($pwd), 7*24*3600);
                } else {
                    cookie("dpwd", null, time()-7*24);
                }
                exit(json_encode(array("code"=>10032,"info"=>"登陆成功","url"=>U('Index/index'))));
            } else {
                $loginError=Logger::loginError();
                exit(json_encode(array("code"=>10031,"info"=>"UID/邮箱或者密码错误","loginerror"=>$loginError)));
            }
        }
        
//        $test2 = D("config")->where(array("www_config.config_id"=>10))->find();
//        $this->assign("logo_botton", $test2);
    }
}
