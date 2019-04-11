$(function(){
	layui.use("layer");
	autoCheck();
	$("#pwd1,#pwd2,#pwd3").bind('input propertychange',function(e){
		autoCheck();
	});
	function autoCheck(){
		var reg=/\s+/;
		var pwd1=$("#pwd1").val();
		var pwd2=$("#pwd2").val();
		var pwd3=$("#pwd3").val();
		pwd1=pwd1.replace(reg,'');
		pwd2=pwd2.replace(reg,'');
		pwd3=pwd3.replace(reg,'');
		$("#pwd1").val(pwd1);
		$("#pwd2").val(pwd2);
		$("#pwd3").val(pwd3);
		if(pwd1&&pwd2&&pwd3){
			$("#pwdBtn").removeClass("disabled").attr("data-qualified","yes");
		}else{
			$("#pwdBtn").addClass("disabled").attr("data-qualified","no");
		}
	}
	$("#pwdBtn").click(function(){
		var isQualified=$(this).attr("data-qualified");
		if(isQualified=="no")return;
		var pwd1=$("#pwd1").val();
		var pwd2=$("#pwd2").val();
		var pwd3=$("#pwd3").val();
		if(pwd2!=pwd3){
			$(".errortip").show();return;
		}else{
			$(".errortip").hide();
		}
		var url=$(this).attr("data-url");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{pwd1:pwd1,pwd2:pwd2,pwd3:pwd3},
			success:function(res){
				var code=res.code;
				switch(code){
					case 10041:
						layer.msg(res.info);
						$(".errortip").show();
						break;
					default:
						layer.msg(res.info);
				}
			}
		});
	});
})

