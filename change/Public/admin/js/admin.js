$(function(){
	layui.use("layer");
	$("#saveBtn").click(function(){
		var url=$(this).attr("data-url");
		var adminName=$("#adminName").val();
		var role=$("#role").val();
		var adminPwd=$("#adminPwd").val();
		var adminCpwd=$("#adminCpwd").val();
		var adminEmail=$("#adminEmail").val();
		var reg=/^(\s+)|(\s+)$/;
		adminName=adminName.replace(reg,"");
		role=role.replace(reg,"");
		adminPwd=adminPwd.replace(reg,"");
		adminCpwd=adminCpwd.replace(reg,"");
		adminEmail=adminEmail.replace(reg,"");
		$("#adminName").val(adminName);
		$("#role").val(role);
		$("#adminPwd").val(adminPwd);
		$("#adminCpwd").val(adminCpwd);
		$("#adminEmail").val(adminEmail);
		if(!adminName){
			return layer.msg("用户名不能为空");
		}
		if(!role){
			return layer.msg("没有创建角色，请联系管理员");
		}
		if(adminPwd){
			if(/^\d+$/.test(adminPwd)||/^[a-zA-Z]+$/.test(adminPwd)||adminPwd.length<6||adminPwd.length>12){
				return layer.msg("密码密码必须是6-12位数字和英文混合");
			}
		}else{
			return layer.msg("密码不能为空");
		}
		if(adminPwd!=adminCpwd){
			return layer.msg("两次密码不一致");
		}
		if(!adminEmail){
			if(!/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/.test(adminEmail)){
				return layer.msg("邮箱格式不正确");
			}
		}
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{adminName:adminName,role:role,adminPwd:adminPwd,adminCpwd:adminCpwd,adminEmail:adminEmail,},
			success:function(res){
				if(res.code==10123){
					layer.msg(res.msg);
					location.href=res.url;
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	$("#saveEditBtn").click(function(){
		var url=$(this).attr("data-url");
		var id=$(this).attr("data-id");
		var adminName=$("#adminName").val();
		var role=$("#role").val();
		var adminPwd=$("#adminPwd").val();
		var adminCpwd=$("#adminCpwd").val();
		var adminEmail=$("#adminEmail").val();
		var reg=/^(\s+)|(\s+)$/;
		adminName=adminName.replace(reg,"");
		role=role.replace(reg,"");
		adminPwd=adminPwd.replace(reg,"");
		adminCpwd=adminCpwd.replace(reg,"");
		adminEmail=adminEmail.replace(reg,"");
		$("#adminName").val(adminName);
		$("#role").val(role);
		$("#adminPwd").val(adminPwd);
		$("#adminCpwd").val(adminCpwd);
		$("#adminEmail").val(adminEmail);
		if(!adminName){
			return layer.msg("用户名不能为空");
		}
		if(!role){
			return layer.msg("没有创建角色，请联系管理员");
		}
		if(adminPwd){
			if(/^\d+$/.test(adminPwd)||/^[a-zA-Z]+$/.test(adminPwd)||adminPwd.length<6||adminPwd.length>12){
				return layer.msg("密码密码必须是6-12位数字和英文混合");
			}
		}
		if(adminPwd!=adminCpwd){
			return layer.msg("两次密码不一致");
		}
		if(!adminEmail){
			if(!/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/.test(adminEmail)){
				return layer.msg(url);
			}
		}
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{id:id,adminName:adminName,role:role,adminPwd:adminPwd,adminCpwd:adminCpwd,adminEmail:adminEmail,},
			success:function(res){
				if(res.code==10123){
					layer.msg(res.msg);
					location.href=res.url;
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	$(".delAdminBtn").click(function(e){
		e.preventDefault();
		var r=confirm("确定此信息吗？");
		if(r==true){
			var url=$(this).attr("data-url");
			var key=$(this).attr("data-key");
			$.ajax({
				url:url,
				type:"post",
				dataType:"json",
				data:{key:key},
				success:function(res){
					if(res.code==10129){
						layer.msg(res.msg);
						location.href=res.url;
					}else{
						layer.msg(res.msg);
					}
				}
			});
		}
	});
});
