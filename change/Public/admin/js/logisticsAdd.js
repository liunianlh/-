$(function(){
	layui.use("layer");
	$("#closeArea").click(function(){
		$("#areaSelectBox").hide();
	});
	$("#openArea").click(function(){
		$("#areaSelectBox").show();
	});
	$(".diqu .sheng .areaCBX").click(function(){
		var isChecked=$(this).attr('checked');
		var areaId=$(this).val();
		if(isChecked!="checked"){
			$(this).parents(".sheng").find(".shi").find('input[data-id='+areaId+']').parent().remove();
			return;
		}
		
		var that=$(this);
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{areaId:areaId},
			success:function(res){
				if(res.code=="10090"){
					var areaInfo=res.msg;
					var lis='';
					for(var i=0,j=areaInfo.length;i<j;i++){
						var temp=areaInfo[i];
						lis+='<li><input data-id="'+temp.area_parent_id+'" class="areaCityCBX" value="'+temp.area_name+'" type="checkbox"/><span>'+temp.area_name+'<span></span></span></li>';
					}
					that.parents(".sheng").find(".shi").append(lis);
				}
			}
		});
	});
	
	$(".diqu .bigDist").click(function(){
		var isChecked=$(this).attr('checked');
		if(isChecked=="checked"){
			$(this).parents("li").next(".sheng").find('.areaCBX').attr("checked",true);
			var ts=$(this).parents("li").next(".sheng").find('.areaCBX');
			for(var k=0,m=ts.size();k<m;k++){
				var areaId=ts.eq(k).val();
				var that=ts.eq(k);
				$.ajax({
					url:url,
					type:"post",
					dataType:"json",
					data:{areaId:areaId},
					success:function(res){
						if(res.code=="10090"){
							var areaInfo=res.msg;
							var lis='';
							for(var i=0,j=areaInfo.length;i<j;i++){
								var temp=areaInfo[i];
								lis+='<li><input data-id="'+temp.area_parent_id+'" class="areaCityCBX" value="'+temp.area_name+'" type="checkbox"/><span>'+temp.area_name+'<span></span></span></li>';
							}
							that.parents(".sheng").find(".shi").append(lis);
						}
					}
				});
			}
		}else{
			$(this).parents("li").next(".sheng").find('.areaCBX').attr("checked",false);
			var ts=$(this).parents("li").next(".sheng").find('.areaCBX');
			for(var k=0,m=ts.size();k<m;k++){
				var areaId=ts.eq(k).val();
				ts.eq(k).parents(".sheng").find(".shi").find('input[data-id='+areaId+']').parent().remove();
			}
		}
	});
	
	$(".addTransAreaBtn").click(function(){
		var logisticsData={};
		logisticsData.id=$(this).attr("data-id");
		logisticsData.logisticsTplName=$("#logisticsTplName").val();
		logisticsData.transportArea=$("#transportArea").val();
		logisticsData.priceWay=$(".priceWay:checked").eq(0).val();
		logisticsData.transCurrency=$(".transCurrency:checked").eq(0).val();
		logisticsData.firstWeight=$("#firstWeight").val();
		logisticsData.firstFee=$("#firstFee").val();
		logisticsData.secondWeight=$("#secondWeight").val();
		logisticsData.secondFee=$("#secondFee").val();
		
		$.ajax({
			url:url2,
			type:"post",
			dataType:"json",
			data:{logisticsData:logisticsData},
			success:function(res){
				if(res.code=="10090"){
					layer.msg(res.msg);
					location.href=res.url;
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	$(".delTpl").click(function(){
		var url=$(this).attr("data-url");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{},
			success:function(res){
				if(res.code=="10091"){
					layer.msg(res.msg);
					location.reload();
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	$(".tplDel").click(function(){
		var url=$(this).attr("data-url");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{},
			success:function(res){
				if(res.code=="10091"){
					layer.msg(res.msg);
					location.reload();
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	$("#logisticsTplList .setDefault").click(function(){
		if($(this).attr("checked")=="checked"){
			var id=$(this).attr("data-id");
			$.ajax({
				url:url2,
				type:"post",
				dataType:"json",
				data:{id:id},
				success:function(res){
					if(res.code=="10091"){
						layer.msg(res.msg);
						location.reload();
					}else{
						layer.msg(res.msg);
					}
				}
			});
		}
	});
	$(".tplEdit").click(function(){
		var url=$(this).attr("data-url");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{},
			success:function(res){
				if(res.code=="10091"){
					var info=res.msg;
					$("#logisticsTplName").val(info.logistics_tpl_flag);
					$("#transportArea").val(info.logistics_tpl_area);
					$(".priceWay").val(info.logistics_tpl_price_way);
					$(".transCurrency").val(info.logistics_tpl_currency);
					$("#firstWeight").val(info.logistics_tpl_first_weight);
					$("#firstFee").val(info.logistics_tpl_first_fee);
					$("#secondWeight").val(info.logistics_tpl_second_weight);
					$("#secondFee").val(info.logistics_tpl_second_fee);
					$(".addTransAreaBtn").attr("data-id",info.logistics_tpl_id);
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	$(".diqu").on("click",".areaCityCBX",function(){
		var transportArea=$("#transportArea").val();
		var isChecked=$(this).attr('checked');
		
		var curSelectValue=$(this).val();
		var tas=transportArea.split(",");
		for(var i=0,j=tas.length;i<j;i++){
			if(tas[i]==curSelectValue){
				tas.splice(i,1);
				break;
			}
		}
		if(isChecked=="checked"){
			tas.push(curSelectValue);
		}
		$("#transportArea").val(tas.join(',').replace(/^\,/,''));
	});
	$(".priceWay").click(function(){
		var wayValue=$(this).val();
		if(wayValue==1){
			$(".firstWeightTitle").html("首重（件）");
			$(".secondWeightTitle").html("次重（件）");
		}
		if(wayValue==2){
			$(".firstWeightTitle").html("首重（KG）");
			$(".secondWeightTitle").html("次重（KG）");
		}
		if(wayValue==3){
			$(".firstWeightTitle").html("首重（材积）");
			$(".secondWeightTitle").html("次重（材积）");
		}
	});
});
