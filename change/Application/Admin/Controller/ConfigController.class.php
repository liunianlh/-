<?php
namespace Admin\Controller;

use Think\Controller;

class ConfigController extends BaseController
{
    public function update()
    {
        $config=I("post.title_value");
        $config2=I("post.botton_value");
        $type=I("post.type");
        $size=I("post.size");
        $data['config_value'] = $config;
        $data['typeface']=$type;
        $data['fontsize']=$size;
        $data2['config_value'] = $config2;
        $test = M("config")->where(array("www_config.config_id"=>9))->save($data);
        $test = M("config")->where(array("www_config.config_id"=>10))->save($data2);
    }
}
