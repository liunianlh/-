$(function(){
	layui.use("layer");
	$("#orderConfirm").click(function(){
		var oid=$(this).attr("data-id");
		var url=$(this).attr("data-url");
		$.ajax({
			url:url,
			type:'post',
			dataType:"json",
			data:{oid:oid},
			success:function(res){
				if(res.code=="10511"){
					layer.msg(res.msg);
					location.href=res.url;
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	
	$("#cancelBtn").click(function(){
		var url=$(this).attr("data-url");
		var id=$(this).attr('data-id');
		
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{id:id},
			success:function(res){
				if(res.code="10161"){
					layer.msg(res.msg);
					location.href=res.url;
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
});
