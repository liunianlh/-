$(function(){
	layui.use("layer");
	$("#saveBtn").click(function(){
		var url=$(this).attr("data-url");
		var roleName=$("#roleName").val();
		var group=$("#group").val();
		var roleRemark=$("#roleRemark").val();
		var reg=/^(\s+)|(\s+)$/;
		roleName=roleName.replace(reg,"");
		group=group.replace(reg,"");
		roleRemark=roleRemark.replace(reg,"");
		$("#roleName").val(roleName);
		$("#group").val(group);
		$("#roleRemark").val(roleRemark);
		if(!roleName){
			return layer.msg("角色名称不能为空");
		}
		if(!group){
			return layer.msg("没有可以选用的组，请联系管理员");
		}
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{roleName:roleName,group:group,roleRemark:roleRemark},
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
		var roleName=$("#roleName").val();
		var group=$("#group").val();
		var roleRemark=$("#roleRemark").val();
		var reg=/^(\s+)|(\s+)$/;
		roleName=roleName.replace(reg,"");
		group=group.replace(reg,"");
		roleRemark=roleRemark.replace(reg,"");
		$("#roleName").val(roleName);
		$("#group").val(group);
		$("#roleRemark").val(roleRemark);
		if(!roleName){
			return layer.msg("角色名称不能为空");
		}
		if(!group){
			return layer.msg("没有可以选用的组，请联系管理员");
		}
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{id:id,roleName:roleName,group:group,roleRemark:roleRemark},
			success:function(res){
				if(res.code==10125){
					layer.msg(res.msg);
					location.href=res.url;
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	$(".delRoleBtn").click(function(e){
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
