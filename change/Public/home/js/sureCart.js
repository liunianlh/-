$(function(){
	layui.use("layer");
	$(".saveBtn").click(function(){
		var url=$(this).attr("data-url");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{},
			success:function(res){
				var code=res.code;
				switch(code){
					case 10029:
						layer.msg(res.info);
						location.href=res.url;
						break;
					default:
						layer.msg(res.info);
				}
			}
		});
	});
});
