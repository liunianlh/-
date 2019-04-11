$(function(){
	layui.use("layer");
	$("#saveBtn").click(function(){
		var url=$(this).attr("data-url");
		var chineseName=$("#chineseName").val();
		var englishName=$("#englishName").val();

		var discount=$("#discount").val();
		var start=$("#start").val();
		var end=$("#end").val();


		var reg=/^\s+|\s+$/;
		chineseName=chineseName.replace(reg,"");
		englishName=englishName.replace(reg,"");

		discount=discount.replace(reg,"");
		start=start.replace(reg,"");
		end=end.replace(reg,"");

		$("#englishName").val(englishName);
		$("#chineseName").val(chineseName);

		$("#discount").val(discount);
		$("#start").val(start);
		$("#end").val(end);


		if(!chineseName){
			return layer.msg("中文名称不能为空");
		}
		if(!englishName){
			return layer.msg("英文名称不能为空");
		}
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{chineseName:chineseName,englishName:englishName,discount:discount,start:start,end:end},
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
		var chineseName=$("#chineseName").val();
		var englishName=$("#englishName").val();

		var discount=$("#discount").val();
		var start=$("#start").val();
		var end=$("#end").val();


		var reg=/^\s+|\s+$/;
		chineseName=chineseName.replace(reg,"");
		englishName=englishName.replace(reg,"");

		discount=discount.replace(reg,"");
		start=start.replace(reg,"");
		end=end.replace(reg,"");

		$("#englishName").val(englishName);
		$("#chineseName").val(chineseName);
		$("#discount").val(discount);
		$("#start").val(start);
		$("#end").val(end);

		if(!chineseName){
			return layer.msg("中文名称不能为空");
		}
		if(!englishName){
			return layer.msg("英文名称不能为空");
		}
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{id:id,chineseName:chineseName,englishName:englishName,discount:discount,start:start,end:end},
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
	$(".delGradeBtn").click(function(e){
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