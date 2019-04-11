$(function(){
	layui.use("layer");
	var ajaxCount=0;
	// layui.use('upload', function(){
		// var $ = layui.jquery;upload = layui.upload;
		// var uploadInst =upload.render({
				// elem: "#importInventory",
				// url: importUrl,
				// accept:"file",
				// exts:'xls|xlsx',
				// before: function(obj){
					// msgTip("正在上传数据");
				// },
				// done: function(res){
					// msgTip(res.msg);
					// if(res.code<=res.row){
						// getMsg(res);
					// }else{
						// $("#msgTip").fadeOut(2000,"swing",function(){
							// $("#flushInventory").click();
						// });
					// }
				// }
		// });
	// });
	
	$("#importInventory").click(function(){
		$.ajax({
			url:importUrl,
			type:"post",
			dataType:"json",
			data:{},
			beforeSend:function(){
				msgTip("正在获取数据...");
			},
			success:function(res){
				msgTip(res.msg);
				if(res.row>0){
					getMsg(res);
				}else{
					msgTip("没有数据");
					$("#msgTip").fadeOut(2000,"swing",function(){
						$("#flushInventory").click();
					});
				}
			}
		});
	});
	
	function msgTip(msg){
		$("#msgTip").show();
		$("#screen").show();
		$("#msgTip").html(msg);
		var h=$(window).height();
		var w=$(window).width();
		var selfW=$("#msgTip").width();
		$("#msgTip").css({"top":(h/2-25)+"px","left":(w-selfW)/2+"px"});
	}
	
	function getMsg(res2){
		ajaxCount++;
		$.ajax({
			url:importUrl,
			type:"post",
			dataType:"json",
			data:{"row":res2.row,"post":res2.post,"success":res2.success},
			success:function(res){
				msgTip(res.msg);
				if(ajaxCount<5000){
					if(res.row>0){
						getMsg(res);
					}else{
						msgTip("数据导入完成");
						$("#msgTip").fadeOut(2000,"swing",function(){
							$("#screen").hide();
							updateTime(res.success);
							$("#flushInventory").click();
						});
					}
				}else{
					msgTip("数据导入完成");
					if((ajaxCount>5000)){
						$("#msgTip").fadeOut(2000,"swing",function(){
							$("#screen").hide();
							updateTime(res.success);
							$("#flushInventory").click();
						});
					}
				}
			}
		});
	}
	
	function updateTime(success2){
		
		$.ajax({
			url:updateTimeUrl,
			type:"post",
			dataType:"json",
			data:{"success":success2},
			success:function(res){
				
			}
		});
		
	}
	// $("#importInventory").click(function(){
		// $.ajax({
			// url:importUrl,
			// type:"post",
			// dataType:"json",
			// data:{"code":res2.code,"row":res2.row,"post":res2.post},
			// success:function(res){
				// layer.msg(res.msg);
			// }
		// });
	// });
});
