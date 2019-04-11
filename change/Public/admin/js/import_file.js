$(function(){
	var ajaxCount=0;
	layui.use('upload', function(){
		var $ = layui.jquery;upload = layui.upload;
		var uploadInst =upload.render({
				elem: "#insertAttach",
				url: importUrl,
				accept:"file",
				before: function(obj){
					msgTip("正在上传数据");
				},
				done: function(res){
					msgTip(res.msg);
					alinks="<a href='javascript:;' data-path='"+res.picPath+"' data-path2='"+res.fin+"' data-type='2'>"+res.fin+"</a>";
					$("#addonOrderList").append(alinks);
					$("#addonOrderLi").show();
					$("#addonFz").html(res.fz);
					$("#addonFz").attr("data-has","yes");
					$("#addonMessageFz").show();
					var addon_order = $(".addon-order").height();
					$(".addon-img").css({"height":addon_order+"px"});
					var align_=(addon_order-20)/2;
					$(".addon-img>img").css({"padding-top":align_+"px"}).show();
					$("#msgTip").fadeOut(2000);
				}
		});
	});
	
	function msgTip(msg){
		$("#msgTip").show();
		$("#msgTip").html(msg);
		var h=$(window).height();
		var w=$(window).width();
		var selfW=$("#msgTip").width();
		$("#msgTip").css({"top":(h/2-25)+"px","left":(w-selfW)/2+"px"});
	}
});
