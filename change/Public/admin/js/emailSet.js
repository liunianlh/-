$(function(){
	layui.use("layer");
	$("#sendBtn").click(function(){
		var url=$(this).attr("data-url");
		var es={};
		es.host=$("#host").val();
		es.port=$("#port").val();
		es.from=$("#from").val();
		es.user=$("#user").val();
		es.pwd=$("#pwd").val();
		es.to=$("#to").val();
		$.ajax({
			url:url,
			type:'post',
			dataType:"json",
			data:{es:es},
			success:function(res){
				if(res.code=="10123"){
					layer.msg(res.msg);
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	$("#saveBtn").click(function(){
		var url=$(this).attr("data-url");
		var es={};
		es.host=$("#host").val();
		es.port=$("#port").val();
		es.from=$("#from").val();
		es.user=$("#user").val();
		es.pwd=$("#pwd").val();
		$.ajax({
			url:url,
			type:'post',
			dataType:"json",
			data:{es:es},
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
