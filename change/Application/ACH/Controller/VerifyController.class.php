<?php
namespace ACH\Controller;
use Think\Controller;
use	Libs\Verification;
class VerifyController extends Controller {
    public function index(){
        $verifyCode=new Verification();
		$verifyCode->createVerifyCode();
	}
}