$(function(){
	layui.use("layer");
	$("#sendMsg").click(function(){
		sendMsg();
	});
	
	$("#sendEmail").click(function(){
		var url=$(this).attr('data-url');
		var to=$("#input_auto").val();
		var title=$("#input_title").val();
		editor.sync();
		var content=$("#editor_id").val();
		var isAttach=$("#addonFz").attr("data-has");
		var ids='';
		var paths=[];
		if(isAttach=="yes"){
			var oids=$("#addonOrderList").find("a[data-type=1]").size();
			var aid=$("#addonOrderList").find("a[data-type=2]");
			var aids=aid.size();
			if(oids>0){
				ids=$("#addonOrderList").attr("data-ids");
			}
			if(aids>0){
				for(var i=0,j=aids;i<j;i++){
					var temp=[];
					temp.push(aid.eq(i).attr("data-path"));
					temp.push(aid.eq(i).attr("data-path2"));
					paths.push(temp);
				}
			}
		}
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{to:to,title:title,content:content,isAttach:isAttach,ids:ids,paths:paths},
			success:function(res){
				if(res.code=="10123"){
					layer.msg(res.msg);
					location.reload();
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	
	$("#contacts .contacts").click(function(){
		if($(this).hasClass("active")){
			$(this).removeClass("active");
		}else{
			$(this).addClass("active");
		}
	});
	
	$("#confirmBtn").click(function(){
		var selUser=$("#contacts").find(".active");
		var len=selUser.size();
		if(len<=0){
			return layer.msg("请选择用户");
		}
		var uids=[];
		for(var i=0;i<len;i++){
			uids.push(selUser.eq(i).attr("data-id"));
		}
		$("#input_auto").val(uids.join(","));
		$(".modal_01").removeClass('in_01');
		$(".modal_01").css('z-index','-1');
		$(".modal-backup_01").css('display','none');
	});
	
	$("#sou1").bind("input propertychange",function(){
		var keyword=$(this).val();
		if(keyword==''){
			$("#searchResultList").html('');
			$("#searchResult").hide();
			return;
		}
		$.ajax({
			url:searchUrl,
			type:"post",
			dataType:"json",
			data:{keyword:keyword},
			success:function(res){
				if(res.code=="10123"){
					var info=res.msg;
					var dds='';
					for(var i=0,j=info.length;i<j;i++){
						var temp=info[i];
						dds+="<dd data-id="+temp.order_id+">"+temp.order_serial_number+"</dd>"
					}
					$("#searchResultList").html(dds);
					$("#searchResult").show();
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	
	$("#searchResultList").on("click","dd",function(){
		// if($(this).hasClass("active")){
			// $(this).removeClass("active");
		// }else{
			// $(this).addClass("active");
		// }
		var ddsId=[];
		ddsId.push($(this).attr("data-id"));
		$.ajax({
			url:insertUrl,
			type:"post",
			dataType:"json",
			data:{ddsId:ddsId},
			success:function(res){
				if(res.code=="10123"){
					var info=res.msg;
					var alinks='';
					for(var i=0,j=info.length;i<j;i++){
						var temp=info[i];
						alinks+="<a href="+temp.erp+" data-type='1'>"+temp.order_serial_number+".xsl</a>";
						alinks+="<a target='_blank' data-type='1' href="+temp.dow+">"+temp.order_serial_number+".pdf</a>"
					}
					$("#addonOrderList").append(alinks);
					$("#addonOrderLi").show();
					$("#addonOrderList").attr("data-ids",res.ids);
					$("#addonFz").html(res.fz);
					$("#addonFz").attr("data-has","yes");
					$("#addonMessageFz").show();
					var addon_order = $(".addon-order").height();
					$(".addon-img").css({"height":addon_order+"px"});
					var align_=(addon_order-20)/2;
					$(".addon-img>img").css({"padding-top":align_+"px"}).show();
					$(".modal_02").removeClass('in_02');
					$(".modal_02").css('z-index','-1');
					$(".modal-backup_02").css('display','none');
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	
	$("#insertResult").click(function(e){
		e.preventDefault();
		var dds=$("#searchResultList").children(".active");
		var ddsId=[];
		for(var i=0,j=dds.length;i<j;i++){
			ddsId.push(dds.eq(i).attr("data-id"));
		}
		$.ajax({
			url:insertUrl,
			type:"post",
			dataType:"json",
			data:{ddsId:ddsId},
			success:function(res){
				if(res.code=="10123"){
					var info=res.msg;
					var alinks='';
					for(var i=0,j=info.length;i<j;i++){
						var temp=info[i];
						alinks+="<a href="+temp.erp+" data-type='1'>"+temp.order_serial_number+".xsl</a>";
						alinks+="<a target='_blank' data-type='1' href="+temp.dow+">"+temp.order_serial_number+".pdf</a>"
					}
					$("#addonOrderList").append(alinks);
					var addon_order = $(".addon-order").height();
					$(".addon-img").css({"height":addon_order+"px"});
					var align_=(addon_order-20)/2;
					$(".addon-img>img").css({"padding-top":align_+"px"}).show();
					$(".modal_02").removeClass('in_02');
					$(".modal_02").css('z-index','-1');
					$(".modal-backup_02").css('display','none');
				}else{
					layer.msg(res.msg);
				}
			}
		});
		return false;
	});
});
function sendMsg(date=''){
	var url=$("#sendMsg").attr('data-url');
	var to=$("#input_auto").val();
	var title=$("#input_title").val();
	var isAttach=$("#addonFz").attr("data-has");
	var ids='';
	var paths=[];
	if(isAttach=="yes"){
		var oids=$("#addonOrderList").find("a[data-type=1]").size();
		var aid=$("#addonOrderList").find("a[data-type=2]");
		var aids=aid.size();
		if(oids>0){
			ids=$("#addonOrderList").attr("data-ids");
		}
		if(aids>0){
			for(var i=0,j=aids;i<j;i++){
				var temp=[];
				temp.push(aid.eq(i).attr("data-path"));
				temp.push(aid.eq(i).attr("data-path2"));
				paths.push(temp);
			}
		}
	}
	editor.sync();
	var content=$("#editor_id").val();
	$.ajax({
		url:url,
		type:"post",
		dataType:"json",
		data:{to:to,title:title,content:content,date:date,isAttach:isAttach,ids:ids,paths:paths},
		success:function(res){
			if(res.code=="10123"){
				layer.msg(res.msg);
				location.reload();
			}else{
				layer.msg(res.msg);
			}
		}
	});
}
