<?php
namespace Libs;
class KDN{
    public static function getOrderTracesByJson($shipperCode, $logisticCode){
       Vendor("kdniao.class#kdniao");
	   $kdniao=new \Kdniao();
	   return $kdniao->getOrderTracesByJson($shipperCode, $logisticCode);
	}
}