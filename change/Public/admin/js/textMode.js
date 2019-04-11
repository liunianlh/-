$(function(){
	autoCheck();
	$("#keywords").bind('input propertychange',function(e){
		autoCheck();
	});
	$("#serial,#joint").change(function(){
		autoCheck();
	});
	function autoCheck(){
		var reg=/\s+/;
		var keywords=$("#keywords").val();
		keywords=keywords.replace(reg,'');
		$("#keywords").val(keywords);
		var serial=$("#serial").val();
		var joint=$("#joint").val();
		if((keywords!='')||(serial!=0)||(joint!=0)){
			$("#searchBtn").removeClass("invalid").attr("data-qualified","yes");
		}else{
			$("#searchBtn").addClass("invalid").attr("data-qualified","no");
		}
	}
	$("#searchBtn").click(function(){
		var isQualified=$(this).attr("data-qualified");
		if(isQualified=="no")return;
		var serial=$("#serial").val();
		var joint=$("#joint").val();
		var keywords=$("#keywords").val();
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{serial:serial,joint:joint,keywords:keywords},
			success:function(res){
				var code=res.code;
				if(code=="10151"){
					location.href=res.msg;
				}
			}
		});
	});
})

