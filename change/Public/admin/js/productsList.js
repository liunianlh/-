 $(function(){
	layui.use("layer");
	$(document).on("click",".RDX",function(){
		var productsItem=$(this).parents(".productsItem");
		flushData(productsItem);
	});
	function flushData(productsItem){
		var productsId=productsItem.attr("data-id");
		var length=productsItem.find(".length").find(".RDX:checked").val();
		var color=productsItem.find(".color").find(".RDX:checked").val();
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{productsId:productsId,length:length,color:color},
			beforeSend:function(){
				$(".fakeloader").fakeLoader({
                    timeToHide:60000,
                    bgColor:"rgba(0,0,0,0.5)",
                    spinner:"spinner2"
                });
			},
			success:function(res){
				$(".fakeloader").css({"display":"none"});
				var info=res.info;
				if(res.code=="10111"){
					productsItem.find(".paramOther").eq(0).html(info.paramOther);
					productsItem.find(".singlePrice").eq(0).html(info.rmb);
					productsItem.find(".productsInventory").eq(0).html(info.inventory);
				}else{
					productsItem.find(".paramOther").eq(0).html(info);
					productsItem.find(".singlePrice").eq(0).html(info);
					productsItem.find(".productsInventory").eq(0).html(info);
				}
			}
		});
	}
	autoFlush();
	function autoFlush(){
		var productsItems=$(".productsItem");
		for(var i=0,j=productsItems.size();i<j;i++){
			var productsItem=productsItems.eq(i);
			flushData(productsItem);
		}
	}
	$(".productsItem").on("click",".addCart",function(){
		var specId=$(this).attr("data-spec-id");
		$.ajax({
			url:url2,
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
 });
