$(function(){
	layui.use("layer");
	/*登陆界面图片（判断屏幕高度）*/
	var whole=$(document).height();
//	$(".left").css("height",whole+"px");
//	console.log("whole"+whole);
	autoCheck();
	$("#email,#password").bind('input propertychange',function(e){
		autoCheck();
	});
	function autoCheck(){
		var reg=/\s+/;
		var email=$("#email").val();
		var password=$("#password").val();
		email=email.replace(reg,'');
		password=password.replace(reg,'');
		$("#email").val(email);
		$("#password").val(password);
		if(email&&password){
			$("#register").removeClass("disabled").attr("data-qualified","yes");
		}else{
			$("#register").addClass("disabled").attr("data-qualified","no");
		}
	}
	$("#dpwd").click(function(){
		if($(this).attr("checked")=="checked"){
			$("#duid").attr("checked",true);
		}else{
			$("#duid").attr("checked",false);
		}
	});
	$("#duid").click(function(){
		if($("#duid").attr("checked")!="checked"){
			$("#dpwd").attr("checked",false);
		}
	});
	$("#register").click(function(){
		var isQualified=$(this).attr("data-qualified");
		if(isQualified=="no")return;
		var email=$("#email").val();
		var password=$("#password").val();
		var duid=$("#duid").attr("checked");
		var dpwd=$("#dpwd").attr("checked");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{email:email,pwd:password,duid:duid?1:0,dpwd:dpwd?1:0},
			success:function(res){
				var code=res.code;
				switch(code){
					case 10032:
					case 10036:
						layer.msg(res.info);
						location.href=res.url;
						break;
					default:
						layer.msg(res.info);
				}
			}
		});
	});
})

