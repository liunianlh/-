$(function(){
	layui.use("layer");
	/*登陆界面图片（判断屏幕高度）*/
	var whole=$(document).height();
//	$(".left").css("height",whole+"px");
//	console.log("whole"+whole);
	//点击刷新验证码
	$("#sendcode").click(function(e){
		e.preventDefault();
		$src=$(this).find("img").attr("src");
		$index=$src.indexOf("?");
		if($index!=-1){
			$src=$src.substr(0,$index);
		}
		$(this).find("img").attr("src",$src+"?rand="+Math.random());
		return false;
	});
	$("#email,#pwd,#cpwd,#vcode").bind('input propertychange',function(e){
		var reg=/\s+/;
		var email=$("#email").val();
		var pwd=$("#pwd").val();
		var cpwd=$("#cpwd").val();
		var vcode=$("#vcode").val();
		email=email.replace(reg,'');
		pwd=pwd.replace(reg,'');
		cpwd=cpwd.replace(reg,'');
		vcode=vcode.replace(reg,'');
		$("#email").val(email);
		$("#pwd").val(pwd);
		$("#cpwd").val(cpwd);
		$("#vcode").val(vcode);
		if(email&&pwd&&cpwd&&vcode){
			$(".reg").addClass("regBtn").attr("data-qualified","yes");
		}else{
			$(".reg").removeClass("regBtn").attr("data-qualified","no");
		}
	});
	$("#regBtn").click(function(){
		var isQualified=$(this).attr("data-qualified");
		if(isQualified=="no")return;
		var reg=/\s+/;
		var email=$("#email").val();
		var pwd=$("#pwd").val();
		var cpwd=$("#cpwd").val();
		var vcode=$("#vcode").val();
		email=email.replace(reg,'');
		pwd=pwd.replace(reg,'');
		cpwd=cpwd.replace(reg,'');
		vcode=vcode.replace(reg,'');
		$("#email").val(email);
		$("#pwd").val(pwd);
		$("#cpwd").val(cpwd);
		$("#vcode").val(vcode);
		if(email&&pwd&&cpwd&&vcode){
			$.ajax({
				url:url,
				type:"post",
				dataType:"json",
				data:{email:email,pwd:pwd,cpwd:cpwd,vcode:vcode},
				success:function(res){
					var code=res.code;
					switch(code){
						case 10026:
						window.history.go(-1);
							//layer.msg(res.info,{shade:0.3,time:3000,shadeClose:true,end:function(){window.location.href=res.url;}});
							//var userAgent = navigator.userAgent;
							// if (userAgent.indexOf("Firefox") != -1 || userAgent.indexOf("Chrome") !=-1) {
							// 	//window.location.href="about:blank";
							// 	window.close();
							// } else {
								// window.opener = null;
								// window.open("", "_self");
								// window.close();
							// }
							
							break;
						default:
							layer.msg(res.info);
					}
				}
			});
		}else{
			$(".reg").removeClass("regBtn");
		}
	});
})

