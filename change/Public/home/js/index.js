$(function(){
	
	$("#myscrollbox>ul>li>a").click(function(e){
		var next=$(this).parent().find(".index_popup");
		var that=$(this);
		var id=$(this).attr("data-id");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{id:id},
			success:function(res){
				next.find("select").eq(0).html(res.msg);
				that.parent().siblings("li").find(".index_popup").hide();
				next.show();
			}
		});
		$(document).click(function(e) {
			$(".index_popup").hide();
		});
		e.stopPropagation();
	});
	$('.index_popup').click(function(e) {
		e.stopPropagation();
	});
	$(".searchBtn2").click(function(){
		var jid=$(this).parent().find(".joint2").val();
		var sid=$(this).parents("li").find(".serial2").attr("data-id");
		$.ajax({
			url:url2,
			type:"post",
			dataType:"json",
			data:{sid:sid,jid:jid},
			success:function(res){
				location.href=res.url;
			}
		});
	});
})

