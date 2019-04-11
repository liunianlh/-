$(function(){
	layui.use("layer");
	$("#saveBtn").click(function(){
		var url=$(this).attr("data-url");
		var reg=/\s+/;
		userCompany={};

		userCompany.discount=$("#discount").val();
		userCompany.start_time=$("#kaishi").val();
		userCompany.end_time=$("#jieshu").val();

		userCompany.contacts=$("#contacts").val();
		userCompany.contactsPhone=$("#contactsPhone").val();
		userCompany.contactsEmail=$("#contactsEmail").val();
		userCompany.accountStatus=$("#accountStatus").val();
		userCompany.tianxinCode=$("#tianxinCode").val();
		userCompany.defaultCurrency=$("#defaultCurrency").val();
		userCompany.grade=$("#grade").val();
		userCompany.operator=$("#operator").val();
		userCompany.year=$("#year").val();
		userCompany.month=$("#month").val();
		userCompany.day=$("#day").val();
		userCompany.hour=$("#hour").val();
		userCompany.minute=$("#minute").val();
		userCompany.second=$("#second").val();
		userCompany.companyName=$("#companyName").val();
		userCompany.companyWebsit=$("#companyWebsit").val();
		userCompany.companyPostalcode=$("#companyPostalcode").val();
		userCompany.city=$("#city").val();
		userCompany.prov=$("#prov").val();
		userCompany.address=$("#address").val();
		for(var key in userCompany){
			$("#"+key).val(userCompany[key].replace(reg,''));
		}
		userCompany.userId=$("#userPwd").attr("data-id");
		companyArea=$(".companyArea").eq(0).val();
		companyArea=companyArea.replace(reg,'');
		$(".companyArea").val(companyArea);
		userCompany.companyArea=companyArea;
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{userCompany:userCompany},
			success:function(res){
				if(res.code=='10029'){
					layer.msg(res.msg,{shade:0.3,time:3000,shadeClose:true,end:function(){window.location.href=res.url;}});
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	$(".companyArea").change(function(){
		$(".companyArea").val($(this).val());
	});
	updateOperationEmail();
	$("#operator").change(function(){
		updateOperationEmail();
	});
	
	function updateOperationEmail(){
		var url=$("#operator").attr("data-url");
		var adminId=$("#operator").val();
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{adminId:adminId},
			success:function(res){
				if(res.code=='10029'){
					$("#adminEmail").html(res.msg);
				}
			}
		});
	}
	$("#orderStatus").change(function(){
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
	$("#PONumber,#serialNumber,#start,#end").bind("input propertychange",function(){
		where=wrapCondition();
		dataDeal(url,where);
	});
	$("#editPassword").click(function(){
		var id=$(this).attr("data-id");
		var url=$(this).attr("data-url");
		var pwd=$("#userPwd").val();
		$.ajax({
			url:url,
			type:"post",
			dataType:"Json",
			data:{id:id,pwd:pwd},
			success:function(res){
				layer.msg(res.msg);
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
							ul+='<li style="width:6%;"> 运输中 </li>';
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
					ul+='<li> 2017-07-03 </li>';
					ul+='<li>'+temp.order_currency+'</li>';
					ul+='<li>'+temp.user_uid+'</li>';
					ul+='<li style="width:6%;">'+temp.admin_name+'</li>';
					ul+='<li><a href="'+temp.url+'"><span>查看</span></a><a href="'+temp.url2+'"><span>打印</span></a><a href="'+temp.url3+'"><span>下载</span></a><a href="'+temp.url4+'"><span>ERP</span></a></li>';
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
	var where={
		start:start,
		end:end,
		orderStatus:orderStatus,
		PONumber:PONumber,
		serialNumber:serialNumber
	};
	return where;
}