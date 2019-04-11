 $(function(){
	 layui.use("layer");
	$(".productsList").on("click",".addCart",function(){
		var specId=$(this).attr("data-spec-id");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{specId:specId},
			success:function(res){
				if(res.code==10112){
					$(".cartNum").html(res.info);
				}
			}
		});
	});
	$("#serial").change(function(){
		var sid=$(this).val();
		var url=$(this).attr("data-url");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{sid:sid},
			success:function(res){
				$("#joint").html(res.msg);
			}
		});
	});
	$("#searchBtn").click(function(){
		var serial=$("#serial").val();
		var joint=$("#joint").val();
		var keywords=$("#keywords").val();
		$.ajax({
			url:url2,
			type:"post",
			dataType:"json",
			data:{serial:serial,sjoint:joint,keywords:keywords},
			success:function(res){
				var code=res.code;
				var uls="";
				if(code=="10153"){
					var info=res.msg;
					if(info.length>0){
						for(var i=0,j=info.length;i<j;i++){
							var temp=info[i];
							var ul="<ul class='productsItem'>";
							ul+='<li>'+temp.serial_name+'</li>';
							ul+='<li>'+temp.joint_name+'</li>';
							ul+='<li>'+temp.model_name+'</li>';
							ul+='<li>'+subS(temp.products_name,12)+'</li>';
							ul+='<li>'+temp.length+'m</li>';
							ul+='<li>'+temp.color_name+'</li>';
							ul+='<li>'+temp.rough_weight+'</li>';
							ul+='<li>'+temp.net_weight+'</li>';
							ul+='<li>'+temp.volume+'</li>';
							ul+='<li>'+temp.loading+'</li>';
							ul+='<li>'+temp.rmb+'</li>';
							ul+='<li><a class="add addCart" data-spec-id="'+temp.specification_id+'" href="javascript:;">'+addCart+'</a></li>';
							ul+="</ul>";
							uls+=ul;
						}
					}else{
						var uls="<ul class='productsItem'><li style='width:100%'>"+noSearchResult+"</li></ul>";
					}
					$(".productsList").find(".productsItem").remove();
					$(".productsList").append(uls);
				}
			}
		});
	});
 });
function subS(s, n){
	return s.replace(/([^x00-xff])/g, "$1a").slice(0, n).replace(/([^x00-xff])a/g, "$1");
}