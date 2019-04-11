$(function(){
	layui.use("layer");
	$("#company_name,#contacts,#phone,#website").bind('input propertychange',function(e){
		checkInput();
	});
	/*登陆界面图片（判断屏幕高度）*/
	var whole=$(document).height();
//	$(".left").css("height",whole+"px");
//	console.log("whole"+whole);
	checkInput();
	function checkInput(){
		var reg=/\s+/;
		var company_name=$("#company_name").val();
		var contacts=$("#contacts").val();
		var phone=$("#phone").val();
		var website=$("#website").val();
		company_name=company_name.replace(reg,'');
		contacts=contacts.replace(reg,'');
		phone=phone.replace(reg,'');
		website=website.replace(reg,'');
		$("#company_name").val(company_name);
		$("#contacts").val(contacts);
		$("#phone").val(phone);
		$("#website").val(website);
		if(company_name&&contacts&&phone){
			if($("#cbx").attr("checked")=="checked"){
				if($("#belong_country").val()!="所在国家"){
					$(".comBtn").removeClass("invalid").attr("data-qualified","yes");
				}else{
					$(".comBtn").addClass("invalid").attr("data-qualified","no");
				}
			}else{
				$(".comBtn").addClass("invalid").attr("data-qualified","no");
			}
		}else{
			$(".comBtn").addClass("invalid").attr("data-qualified","no");
		}
	}
	$("#cbx").click(function(){
		checkInput();
	});
	$("#belong_country").change(function(){
		checkInput();
	});
	$("#comBtn").click(function(){
		var isQualified=$(this).attr("data-qualified");
		if(isQualified=="no")return;
		var reg=/^(\s+)|(\s+)$/;
		var company_name=$("#company_name").val();
		var contacts=$("#contacts").val();
		var phone=$("#phone").val();
		var website=$("#website").val();
		company_name=company_name.replace(reg,'');
		contacts=contacts.replace(reg,'');
		phone=phone.replace(reg,'');
		website=website.replace(reg,'');
		$("#company_name").val(company_name);
		$("#contacts").val(contacts);
		$("#phone").val(phone);
		$("#website").val(website);
		var belong_country=$("#belong_country").val();
		var key=$("#key").val();
		if(company_name&&contacts&&phone){
			// if(!/^[0-9\-]{7,15}$/.test(phone)){
				// return layer.msg("");
			// }
			$.ajax({
				url:url,
				type:"post",
				dataType:"json",
				data:{company_name:company_name,contacts:contacts,phone:phone,website:website,belong_country:belong_country,key:key},
				success:function(res){
					var code=res.code;
					switch(code){
						case 10029:
							layer.msg(res.info);
							location.href=res.url;
							break;
						default:
							layer.msg(res.info);
					}
				}
			});
		}else{
			$(".comBtn").addClass("invalid").attr("data-qualified","no");
		}
	});
})

