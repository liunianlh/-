$(function(){
	layui.use("layer");
	/*登陆界面图片（判断屏幕高度）*/
	var whole=$(document).height();
//	$(".left").css("height",whole+"px");
//	console.log("whole"+whole);
	autoCheck();
	$("#UID,#email").bind('input propertychange',function(e){
		autoCheck();
	});
	function autoCheck(){
		var reg=/\s+/;
		var UID=$("#UID").val();
		var email=$("#email").val();
		UID=UID.replace(reg,'');
		email=email.replace(reg,'');
		$("#UID").val(UID);
		$("#email").val(email);
		if(UID&&email){
			$("#register").addClass("active").attr("data-qualified","yes");
		}else{
			$("#register").removeClass("active").attr("data-qualified","no");
		}
	}
	$("#register").click(function(){
		var isQualified=$(this).attr("data-qualified");
		if(isQualified=="no")return;
		var UID=$("#UID").val();
		var email=$("#email").val();
		var url=$(this).attr("data-url");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{UID:UID,email:email},
			success:function(res){
				layer.msg(res.msg);
			}
		});
	});
})

