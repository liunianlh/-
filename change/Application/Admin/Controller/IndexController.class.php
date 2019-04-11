<?php
namespace Admin\Controller;

use Think\Controller;

class IndexController extends BaseController
{
    public function index()
    {
        $userInfo=M("User")->select();
        $gradeInfo=M("Grade")->select();
        $dealed=array();//已处理
        foreach ($gradeInfo as $key=>$value) {
            $dealed[md5($value['grade_id'])]=array(
                "cname"=>$value['gr_chinese_name'],
                "nname"=>$value['gr_english_name'],
                "count"=>0
            );
        }

        $dealed2=0;
        $deal=0;//未处理
        $oneMonth=0;//1个月未登入
        $threeMonth=0;//3个月未登入
        foreach ($userInfo as $k=>$v) {
            if (strtotime("-3 month")>$v['current_time']) {
                $threeMonth++;
            }
            if (strtotime("-1 month")>$v['current_time']) {
                $oneMonth++;
            }
            if ($v['is_deal']==1) {//1未处理  2已处理
                $deal++;
            } else {
                $dealed2++;
                if (!empty($v['grade_id'])) {
                    $dealed[md5($v['grade_id'])]["count"]++;
                }
            }
        }

        $todayOrder=D("Order", "Logic")->getTodayOrder();
        $this->assign("today_order", $todayOrder);
        $monthOrder=D("Order", "Logic")->getMonthOrder();
        $this->assign("month_order", $monthOrder);

        $test = D("config")->where(array("www_config.config_id"=>9))->find();
        $this->assign("logo", $test);



        $test2 = D("config")->where(array("www_config.config_id"=>10))->find();
        $this->assign("logo_botton", $test2);
        $this->assign("dealed", $dealed);
        $this->assign("dealed2", $dealed2);
        $this->assign("deal", $deal);
        $this->assign("one_month", $oneMonth);
        $this->assign("three_month", $threeMonth);
        $this->display();
    }
}
