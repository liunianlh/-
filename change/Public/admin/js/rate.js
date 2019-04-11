$(function(){
	layui.use("layer");
	$("#saveBtn").click(function(){
		var url=$(this).attr("data-url");
		var rmb=$("#RMB").val();
		var usd=$("#USD").val();
		$.ajax({
			url:url,
			type:'post',
			dataType:"json",
			data:{rmb:rmb,usd:usd},
			success:function(res){
				if(res.code=="10123"){
					layer.msg(res.msg);
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
});
