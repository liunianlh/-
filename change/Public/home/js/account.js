$(function(){
	layui.use("layer");
	autoCheck();
	$("#contacts,#email,#phone").bind('input propertychange',function(e){
		autoCheck();
	});
	function autoCheck(){
		var reg=/\s+/;
		var contacts=$("#contacts").val();
		var email=$("#email").val();
		var phone=$("#phone").val();
		contacts=contacts.replace(reg,'');
		email=email.replace(reg,'');
		phone=phone.replace(reg,'');
		$("#contacts").val(contacts);
		$("#email").val(email);
		$("#phone").val(phone);
		if(contacts&&email&&phone){
			$("#saveBtn").addClass("active").attr("data-qualified","yes");
		}else{
			$("#saveBtn").removeClass("active").attr("data-qualified","no");
		}
	}
	$("#saveBtn").click(function(){
		var isQualified=$(this).attr("data-qualified");
		if(isQualified=="no")return;
		var country=$("#country").val();
		var contacts=$("#contacts").val();
		var email=$("#email").val();
		var phone=$("#phone").val();
		var url=$(this).attr("data-url");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{contacts:contacts,email:email,phone:phone,country:country},
			success:function(res){
				layer.msg(res.info);
				setTimeout(function () {
                    location.href=res.url;
                },1000)

			}
		});
	});
})

