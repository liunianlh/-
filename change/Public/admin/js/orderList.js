$(function(){
	layui.use("layer");
	$("#orderStatus,#adminId").change(function(){
		where=wrapCondition();
		dataDeal(url,where);
	});
	$("#page").on("click","a.next,a.prev,a.num",function(e){
		e.preventDefault();
		var curPageLink=$(this).attr("href");
		$("#page").attr("data-cur-page",curPageLink);
		where=wrapCondition();
		dataDeal(curPageLink,where);
	});
	$("#PONumber,#serialNumber,#start,#end,#UID,#companyName,#tianxinCode,#customerName").bind("input propertychange",function(){
		where=wrapCondition();
		dataDeal(url,where);
	});
	$(document).on("click",".del",function(e){
		if(confirm("确定要删除吗？")==false)return;
		var id=$(this).attr("data-id");
		$.ajax({
			url:delUrl,
			type:"post",
			dataType:"json",
			data:{id:id},
			success:function(res){
				layer.msg(res.msg);
				if(res.code=='10511'){
					$("#orderStatus").change();
				}
			}
		});
	});
});
function dataDeal(url,where){
	$.ajax({
		url:url,
		type:"post",
		dataType:"json",
		data:{where:where},
		success:function(res){
			if(res.code=="10102"){
				var orderInfo=res.msg;
				var page=res.page;
				var uls='';
				for(var i=0,j=orderInfo.length;i<j;i++){
					var temp=orderInfo[i];
					var ul="<ul class='orderItem'>";
					switch(temp.order_status){
						case "1":
							ul+='<li style="width:6%;"> 待处理 </li>';
							break;
						case "2":
							ul+='<li style="width:6%;"> 已确认 </li>';
							break;
						case "3":
							ul+='<li style="width:6%;"> 已完成 </li>';
							break;
						case "4":
							ul+='<li style="width:6%;"> 已取消 </li>';
							break;
						case "5":
							ul+='<li style="width:6%;"> 已配送 </li>';
							break;
						case "6":
							ul+='<li style="width:6%;"> 待确认 </li>';
							break;
						default:
							ul+='<li style="width:6%;"> -- </li>';
							break;
					}
					ul+='<li> '+temp.order_ponumber+' </li>';
					ul+='<li style="width:15%;"> '+temp.order_serial_number+' </li>';
					ul+='<li> '+temp.order_time+' </li>';
					if(temp.company_name){
						ul+='<li> '+temp.company_name+' </li>';
					}else{
						ul+='<li> - </li>';
					}
					
					ul+='<li style="width:6%;">'+temp.order_currency+'</li>';
					ul+='<li style="width:6%;">'+temp.user_uid+'</li>';
					ul+='<li style="width:6%;">'+temp.admin_name+'</li>';
					ul+='<li style="width:8%;">'+temp.tianxin_code+'</li>';
					ul+='<li><a href="'+temp.url+'"><span>查看</span></a> <a href="'+temp.url2+'"><span>打印</span></a> <a href="'+temp.url3+'"><span>下载</span></a> <a href="'+temp.url4+'"><span>ERP</span></a> <a href="javascript:;"><span class="del" data-id="'+temp.order_id+'">删除</span></a></li>';
					ul+="</ul>";
					uls+=ul;
				}
				$(".orderList").find(".orderItem").remove();
				$(".orderList").append(uls);
				$("#page").html(page);
			}
		}
	});
}
function wrapCondition(){
	var start=$("#start").val();
	var end=$("#end").val();
	var orderStatus=$("#orderStatus").val();
	var PONumber=$("#PONumber").val();
	var serialNumber=$("#serialNumber").val();
	var adminId=$("#adminId").val();
	var UID=$("#UID").val();
	var tianxinCode=$("#tianxinCode").val();
	var companyName=$("#companyName").val();
	var customerName=$("#customerName").val();
	var where={
		start:start,
		end:end,
		orderStatus:orderStatus,
		PONumber:PONumber,
		serialNumber:serialNumber,
		adminId:adminId,
		UID:UID,
		tianxinCode:tianxinCode,
		companyName:companyName,
		customerName:customerName
	};
	return where;
}