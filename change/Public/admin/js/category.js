$(function(){
	layui.use("layer");
	$("#saveBtn").click(function(){
		var url=$(this).attr("data-url");
		var chineseName=$("#chineseName").val();
		var englishName=$("#englishName").val();
		var catImg=$("#catImg").val();
		var catLink=$("#catLink").val();
		var catPid=$("#catPid").val();
		var reg=/^\s+|\s+$/;
		chineseName=chineseName.replace(reg,"");
		englishName=englishName.replace(reg,"");
		catImg=catImg.replace(reg,"");
		catLink=catLink.replace(reg,"");
		$("#chineseName").val(chineseName);
		$("#englishName").val(englishName);
		$("#catImg").val(catImg);
		$("#catLink").val(catLink);
		if(chineseName==''){
			return layer.msg("名称不能为空");
		}
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{chineseName:chineseName,englishName:englishName,catImg:catImg,catLink:catLink,catPid:catPid},
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
		var catImg=$("#catImg").val();
		var catLink=$("#catLink").val();
		var catPid=$("#catPid").val();
		var reg=/^\s+|\s+$/;
		chineseName=chineseName.replace(reg,"");
		englishName=englishName.replace(reg,"");
		catImg=catImg.replace(reg,"");
		catLink=catLink.replace(reg,"");
		$("#chineseName").val(chineseName);
		$("#englishName").val(englishName);
		$("#catImg").val(catImg);
		$("#catLink").val(catLink);
		if(chineseName==''){
			return layer.msg("名称不能为空");
		}
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{id:id,chineseName:chineseName,englishName:englishName,catImg:catImg,catLink:catLink,catPid:catPid},
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
	$(".delCatBtn").click(function(e){
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
	
	$("#sortBtn").click(function(){
		var url=$(this).attr("data-url");
		var sort_r=$(".sort");
		var sorts=[];
		for(var i=0,j=sort_r.size();i<j;i++){
			var temp={};
			temp.name=sort_r.eq(i).attr("data-id");
			temp.value=sort_r.eq(i).val();
			sorts.push(temp);
		}
		$.ajax({
				url:url,
				type:"post",
				dataType:"json",
				data:{sort:sorts},
				success:function(res){
					if(res.code==10129){
						layer.msg(res.msg);
						location.href=res.url;
					}else{
						layer.msg(res.msg);
					}
				}
			});
	});
});
layui.use('upload', function(){
	var $ = layui.jquery;
	upload = layui.upload;
	var uploadInst=upload.render({
			elem: "#uploadBtn",
			url: url,
			accept:"file",
			exts:'jpg|jpeg|png|gif',
			before: function(obj){
				
			},
			done: function(res){
				if(res.code>0){
					return layer.msg(res.msg);
				}
				$("#catImg").val(res.picPath);
				return layer.msg(res.msg);
			},
			error: function(){
				
			}
	});
});
