$(function(){
	layui.use("layer");
	$("#saveEditBtn").click(function(){
		var url=$(this).attr("data-url");
		var rid=$(this).attr("data-id");
		var selNodes=zTree.getCheckedNodes();
		var sels=[];
		for(var k in selNodes){
			sels.push(selNodes[k].id);
		}
		
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{rid:rid,sels:sels},
			success:function(res){
				if(res.code==10103){
					layer.msg(res.msg);
					location.reload();
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
});
