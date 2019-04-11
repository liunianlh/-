$(function(){
	layui.use("layer");
	/*登陆界面图片（判断屏幕高度）*/
	var whole=$(document).height();
//	$(".left").css("height",whole+"px");
//	console.log("whole"+whole);
	autoCheck();
	$("#password,#password_sure").bind('input propertychange',function(e){
		autoCheck();
	});
	function autoCheck(){
		var reg=/\s+/;
		var password=$("#password").val();
		var password_sure=$("#password_sure").val();
		password=password.replace(reg,'');
		password_sure=password_sure.replace(reg,'');
		$("#password").val(password);
		$("#password_sure").val(password_sure);
		if(password&&password_sure){
			$("#register").addClass("active").attr("data-qualified","yes");
		}else{
			$("#register").removeClass("active").attr("data-qualified","no");
		}
	}
	$("#register").click(function(){
		var isQualified=$(this).attr("data-qualified");
		if(isQualified=="no")return;
		var password=$("#password").val();
		var password_sure=$("#password_sure").val();
		var url=$(this).attr("data-url");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{password:password,password_sure:password_sure},
			success:function(res){
				if(res.code=="10813"){
					layer.msg(res.msg);
					location.href=res.url;
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
})

