<?php
namespace Libs;
class PDF{
    public static function createPDF($orderInfo,$orderDetail,$isFile=0){
		
		Vendor("Tcpdf.tcpdf");
		$tcpdf=new \TCPDF("P","mm","A4", true, 'UTF-8', false);
		
		$tcpdf->SetCreator('Tonetron');
		$tcpdf->SetAuthor('Tonetron');
		$tcpdf->SetTitle($orderInfo['order_serial_number']);
		$tcpdf->SetSubject('Tonetron');
		$tcpdf->SetKeywords('Tonetron');

		$tcpdf->SetHeaderData('tcpdf_logo.jpg',30,'','');
		$tcpdf->setHeaderFont(Array("droidsansfallback",'',10));
		$tcpdf->setFooterFont(Array("droidsansfallback",'',8));
		$tcpdf->SetDefaultMonospacedFont("courier");
		
		$tcpdf->SetMargins(10,27,10);
		$tcpdf->SetHeaderMargin(5);
		$tcpdf->SetFooterMargin(10);
		
		$tcpdf->SetAutoPageBreak(TRUE,25);
		
		$tcpdf->setImageScale(1.25);
		$tcpdf->SetFont('droidsansfallback', '', 8);
		$tcpdf->AddPage();
		
		$logisticsInfo=M("Order_logistics")->where(array("order_id"=>$orderInfo['order_id']))->find();
		$userInfo=M("User")->join("left join __COMPANY__ on __COMPANY__.user_id=__USER__.user_id")->where(array("www_user.user_id"=>$orderInfo['user_id']))->field(array("www_user.*","www_company.company_name"))->find();
		$adminInfo=M("Admin")->where(array("admin_id"=>$orderInfo['admin_id']))->find();
		
		$orderTime=date("Y-m-d H:i:s",$orderInfo['order_time']);
		$baseHtml=<<<BBB
<table cellspacing="0" cellpadding="5" border="1">
	<tr>
		<td colspan="4" align="center" style="background-color:#333333;color:#ffffff;"  height="30"><h1>Tonetron</h1></td>
		<td colspan="3" align="center"><h1>订单系统订单</h1></td>
		<td align="center" style="background-color:#333333;color:#ffffff;">PO单号</td>
		<td style="color:#ff0000;">{$orderInfo['order_ponumber']}</td>
	</tr>
	<tr>
		<td rowspan="6" align="center" style="background-color:#333333;color:#ffffff;">基础资料</td>
		<td style="background-color:#333333;color:#ffffff;">公司名称</td>
		<td colspan="2">{$userInfo['company_name']}</td>
		<td style="background-color:#333333;color:#ffffff;">UID</td>
		<td colspan="6">{$orderInfo['user_uid']}</td>
	</tr>
	<tr>
		<td style="background-color:#333333;color:#ffffff;">流水单号</td>
		<td colspan="2">{$orderInfo['order_serial_number']}</td>
		<td rowspan="2" style="background-color:#333333;color:#ffffff;">物流地址</td>
		<td rowspan="2" colspan="6">{$logisticsInfo['logistics_country']}{$logisticsInfo['logistics_province']}{$logisticsInfo['logistics_city']}{$logisticsInfo['logistics_dist']}{$logisticsInfo['logistics_address']}</td>
	</tr>
	<tr>
		<td style="background-color:#333333;color:#ffffff;">下单时间</td>
		<td colspan="2">{$orderTime}</td>
	</tr>
	<tr>
		<td style="background-color:#333333;color:#ffffff;">业务员</td>
		<td colspan="2">{$orderInfo['admin_name']}</td>
		<td style="background-color:#333333;color:#ffffff;">收件人名称</td>
		<td colspan="6">{$logisticsInfo['logistics_receiver']}</td>
	</tr>
	<tr>
		<td style="background-color:#333333;color:#ffffff;">邮箱地址</td>
		<td colspan="2">{$adminInfo['admin_email']}</td>
		<td style="background-color:#333333;color:#ffffff;">收件人电话</td>
		<td colspan="6">{$logisticsInfo['logistics_receiver_phone']}</td>
	</tr>
	<tr>
		<td style="background-color:#333333;color:#ffffff;">销售条款</td>
		<td colspan="2">{$orderInfo['sales_terms']}</td>
		<td style="background-color:#333333;color:#ffffff;">收件人邮箱</td>
		<td colspan="6">{$logisticsInfo['logistics_receiver_email']}</td>
	</tr>
</table>
BBB;
		
		$tcpdf->writeHTML($baseHtml, true, false, false, false, '');
		$tcpdf->Ln();
		$detailHtml='<table cellspacing="0" cellpadding="5" border="1">
	<tr>
		<td align="center" style="background-color:#333333;color:#ffffff;">项目</td>
		<td align="center" style="background-color:#333333;color:#ffffff;">产品图片</td>
		<td align="center" style="background-color:#333333;color:#ffffff;">产品型号</td>
		<td align="center" colspan="2" style="background-color:#333333;color:#ffffff;">产品规格</td>
		<td align="center" style="background-color:#333333;color:#ffffff;">长度</td>
		<td align="center" style="background-color:#333333;color:#ffffff;">颜色</td>
		<td align="center" colspan="2" style="background-color:#333333;color:#ffffff;">订单数量</td>
		<td align="center" style="background-color:#333333;color:#ffffff;">单价</td>
		<td align="center" style="background-color:#333333;color:#ffffff;">小计</td>
	</tr>';
	
	foreach($orderDetail as $key=>$value){
		$detailHtml.='<tr>
			<td align="center">'.($key+1).'</td>
			<td><img src="/Public/'.$value['products_img'].'"/></td>
			<td>'.$value['model_name'].'</td>
			<td colspan="2">'.$value['products_chinese_name'].'</td>
			<td>'.$value['length'].'m</td>
			<td>'.$value['color_name'].'</td>
			<td colspan="2">'.$value['total_number'].'</td>
			<td>'.$value['price'].'</td>
			<td style="color:#ff0000;">'.$value['amount'].'</td>
		</tr>';
	}
	if($orderInfo['service_fee']>=0){
		if($orderInfo['order_currency']=="RMB"){
			$detailHtml.='<tr>
				<td colspan="8" align="right">物流费用</td>
				<td colspan="3" align="left">'.$orderInfo['service_fee'].'</td>
			</tr>';
		}else{
			$detailHtml.='<tr>
				<td colspan="8" align="right">报关手续费</td>
				<td colspan="3" align="left">'.$orderInfo['service_fee'].'</td>
			</tr>';
		}
		
	}
	
	$detailHtml.='<tr>
		<td colspan="8" align="right">总计</td>
		<td colspan="3">'.($orderInfo['order_total_price']+$orderInfo['service_fee']).'</td>
	</tr>
	<tr>
		<td rowspan="2" align="center" style="background-color:#333333;color:#ffffff;">备注</td>
		<td align="left" style="background-color:#333333;color:#ffffff;">币别</td>
		<td colspan="9" align="left">'.$orderInfo['order_currency'].'</td>
	</tr>
	<tr>
		<td align="left" style="background-color:#333333;color:#ffffff;">备注</td>
		<td colspan="9" align="left">'.$orderInfo['order_remark'].'</td>
	</tr>
	<tr>
		<td colspan="11">本报价单为东莞台昶电子有限公司与其客户的机密文件，第三方单位在未经我司许可情况下请勿查阅、复制及修改，即需即时删除此文件并通知我司人员删除。</td>
	</tr>
	<tr>
		<td colspan="11">©东莞台昶电子有限公司版权所有，并保留最终解释权。</td>
	</tr>
</table>';
		$tcpdf->writeHTML($detailHtml, true, false, false, false, '');
		$documentRoot=$_SERVER["DOCUMENT_ROOT"];
		if($isFile==0){
			$tcpdf->Output($orderInfo['order_serial_number'].'.pdf', 'I');
		}else{
			$fileRoot=$_SERVER["DOCUMENT_ROOT"];
			$filename=$fileRoot."Public/Uploads/PDF/".$orderInfo['order_serial_number'].".pdf";
			$tcpdf->Output($filename, 'F');
			return filesize($filename);
		}
	}
	
	
	public static function createPDF2($orderInfo,$orderDetail,$isFile=0){
		
		Vendor("Tcpdf.tcpdf");
		$tcpdf=new \TCPDF("P","mm","A4", true, 'UTF-8', false);
		
		$tcpdf->SetCreator('Tonetron');
		$tcpdf->SetAuthor('Tonetron');
		$tcpdf->SetTitle($orderInfo['order_serial_number']);
		$tcpdf->SetSubject('Tonetron');
		$tcpdf->SetKeywords('Tonetron');

		$tcpdf->SetHeaderData('tcpdf_logo.jpg',30,'','');
		$tcpdf->setHeaderFont(Array("droidsansfallback",'',10));
		$tcpdf->setFooterFont(Array("droidsansfallback",'',8));
		$tcpdf->SetDefaultMonospacedFont("courier");
		
		$tcpdf->SetMargins(10,27,10);
		$tcpdf->SetHeaderMargin(5);
		$tcpdf->SetFooterMargin(10);
		
		$tcpdf->SetAutoPageBreak(TRUE,25);
		
		$tcpdf->setImageScale(1.25);
		$tcpdf->SetFont('droidsansfallback', '', 8);
		$tcpdf->AddPage();
		
		$logisticsInfo=M("Order_logistics")->where(array("order_id"=>$orderInfo['order_id']))->find();
		$userInfo=M("User")->join("left join __COMPANY__ on __COMPANY__.user_id=__USER__.user_id")->where(array("www_user.user_id"=>$orderInfo['user_id']))->field(array("www_user.*","www_company.company_name"))->find();
		$adminInfo=M("Admin")->where(array("admin_id"=>$orderInfo['admin_id']))->find();
		
		$orderTime=date("Y-m-d H:i:s",$orderInfo['order_time']);
		$baseHtml='
<table cellspacing="0" cellpadding="5" border="1">
	<tr>
		<td colspan="4" align="center" style="background-color:#333333;color:#ffffff;"  height="30"><h1>Tonetron</h1></td>
		<td colspan="3" align="center"><h1>'.L('_PUBLIC_ORDER_SYSTEM_ORDER_').'</h1></td>
		<td align="center" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_PONUMBER_').'</td>
		<td style="color:#ff0000;">'.$orderInfo['order_ponumber'].'</td>
	</tr>
	<tr>
		<td rowspan="6" align="center" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_BASIC_DATA_').'</td>
		<td style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_COMPANY_NAME_').'</td>
		<td colspan="2">'.$userInfo['company_name'].'</td>
		<td style="background-color:#333333;color:#ffffff;">UID</td>
		<td colspan="6">'.$orderInfo['user_uid'].'</td>
	</tr>
	<tr>
		<td style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_SERIAL_NUMBER_').'</td>
		<td colspan="2">'.$orderInfo['order_serial_number'].'</td>
		<td rowspan="2" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_LOGISTICS_ADDRESS_').'</td>
		<td rowspan="2" colspan="6">'.$logisticsInfo['logistics_country'].$logisticsInfo['logistics_province'].$logisticsInfo['logistics_city'].$logisticsInfo['logistics_dist'].$logisticsInfo['logistics_address'].'</td>
	</tr>
	<tr>
		<td style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_ORDER_TIME_').'</td>
		<td colspan="2">'.$orderTime.'</td>
	</tr>
	<tr>
		<td style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_SALESMAN_').'</td>
		<td colspan="2">'.$orderInfo['admin_name'].'</td>
		<td style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_RECIPIENT_NAME_').'</td>
		<td colspan="6">'.$logisticsInfo['logistics_receiver'].'</td>
	</tr>
	<tr>
		<td style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_EMAIL_ADDRESS_').'</td>
		<td colspan="2">'.$adminInfo['admin_email'].'</td>
		<td style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_RECIPIENT_PHONE_').'</td>
		<td colspan="6">'.$logisticsInfo['logistics_receiver_phone'].'</td>
	</tr>
	<tr>
		<td style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_SALES_TERMS_').'</td>
		<td colspan="2">'.$orderInfo['sales_terms'].'</td>
		<td style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_RECIPIENT_EMAIL_').'</td>
		<td colspan="6">'.$logisticsInfo['logistics_receiver_email'].'</td>
	</tr>
</table>';
		
		$tcpdf->writeHTML($baseHtml, true, false, false, false, '');
		$tcpdf->Ln();
		$detailHtml='<table cellspacing="0" cellpadding="5" border="1">
	<tr>
		<td align="center" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_TERMS_').'</td>
		<td align="center" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_PRODUCT_IMG_').'</td>
		<td align="center" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_PRODUCT_MODEL_').'</td>
		<td align="center" colspan="2" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_PRODUCT_SPECIFICATION_').'</td>
		<td align="center" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_PRODUCT_LENGTH_').'</td>
		<td align="center" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_PRODUCT_COLOR_').'</td>
		<td align="center" colspan="2" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_ORDER_NUMBER_').'</td>
		<td align="center" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_PRODUCT_SINGLE_PRICE_').'</td>
		<td align="center" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_PRODUCT_AMOUNT_').'</td>
	</tr>';
	
	foreach($orderDetail as $key=>$value){
		$detailHtml.='<tr>
			<td align="center">'.($key+1).'</td>
			<td><img src="/Public/'.$value['products_img'].'"/></td>
			<td>'.$value['model_name'].'</td>
			<td colspan="2">'.$value['products_chinese_name'].'</td>
			<td>'.$value['length'].'m</td>
			<td>'.$value['color_name'].'</td>
			<td colspan="2">'.$value['total_number'].'</td>
			<td>'.$value['price'].'</td>
			<td style="color:#ff0000;">'.$value['amount'].'</td>
		</tr>';
	}
	if($orderInfo['service_fee']>=0){
		if($orderInfo['order_currency']=="RMB"){
			$detailHtml.='<tr>
				<td colspan="8" align="right">'.L('_PUBLIC_LOGISTICS_FEE_').'</td>
				<td colspan="3" align="left">'.$orderInfo['service_fee'].'</td>
			</tr>';
		}else{
			$detailHtml.='<tr>
				<td colspan="8" align="right">'.L('_PUBLIC_SERVICE_FEE_').'</td>
				<td colspan="3" align="left">'.$orderInfo['service_fee'].'</td>
			</tr>';
		}
		
	}
	
	$detailHtml.='<tr>
		<td colspan="8" align="right">'.L('_PUBLIC_ORDER_TOTAL_').'</td>
		<td colspan="3">'.($orderInfo['order_total_price']+$orderInfo['service_fee']).'</td>
	</tr>
	<tr>
		<td rowspan="2" align="center" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_REMARK_').'</td>
		<td align="left" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_ORDER_CURRENCY_').'</td>
		<td colspan="9" align="left">'.$orderInfo['order_currency'].'</td>
	</tr>
	<tr>
		<td align="left" style="background-color:#333333;color:#ffffff;">'.L('_PUBLIC_REMARK_').'</td>
		<td colspan="9" align="left">'.$orderInfo['order_remark'].'</td>
	</tr>
	<tr>
		<td colspan="11">'.L('_PUBLIC_STATUTE_').'</td>
	</tr>
	<tr>
		<td colspan="11">'.L('_PUBLIC_RIGHT_').'</td>
	</tr>
</table>';
		$tcpdf->writeHTML($detailHtml, true, false, false, false, '');
		$documentRoot=$_SERVER["DOCUMENT_ROOT"];
		if($isFile==0){
			$tcpdf->Output($orderInfo['order_serial_number'].'.pdf', 'I');
		}else{
			$fileRoot=$_SERVER["DOCUMENT_ROOT"];
			$filename=$fileRoot."Public/Uploads/PDF/".$orderInfo['order_serial_number'].".pdf";
			$tcpdf->Output($filename, 'F');
			return filesize($filename);
		}
	}
}