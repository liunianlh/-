$(function(){
	layui.use("layer");
	$("#saveBtn").click(function(){
		var oid=$(this).attr("data-id");
		var url=$(this).attr("data-url");
		var lg={};
		lg.logisticsCompany=$("#logisticsCompany").val();
		lg.logisticsNo=$("#logisticsNo").val();
		lg.year=$("#year").val();
		lg.month=$("#month").val();
		lg.day=$("#day").val();
		$.ajax({
			url:url,
			type:'post',
			dataType:"json",
			data:{oid:oid,lg:lg,type:2},
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
