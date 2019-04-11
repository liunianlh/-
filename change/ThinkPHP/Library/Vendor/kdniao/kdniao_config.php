<?php
/*
快递鸟申请地址：http://www.kdniao.com/ServiceApply.aspx
生产环境地址：http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx
*/
//快递鸟申请的商户ID
defined('EBusinessID') or define('EBusinessID', '1307587');
//电商加密私钥，快递鸟提供，注意保管，不要泄漏
defined('AppKey') or define('AppKey', '845191b0-fb75-4921-a82f-973f5f641e4e');
//请求url
defined('ReqURL') or define('ReqURL', 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx');

