$(function(){
	layui.use("layer");
	$("#saveBtn").click(function(){
		var url=$(this).attr("data-url");
		editor.sync();
		var content=$("#editor_id").val();
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{content:content},
			success:function(res){
				if(res.code=="50001"){
					layer.msg(res.msg);
					location.reload();
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
});
