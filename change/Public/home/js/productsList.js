 $(function(){
	layui.use("layer");
	$(document).on("click",".RDX",function(){
		var productsItem=$(this).parents(".productsItem");
		flushData(productsItem);
	});
	$("#setDisplayNumber").click(function(){
		$("#show_amount").show();
	});
	$("#cancelDisplay").click(function(){
		$("#show_amount").hide();
	});
	$("#descNumber").click(function(){
		var number=$("#realNumber").html();
		if(number-0<=0){
			return;
		}else{
			$("#realNumber").html(number-10);
		}
	});
	$("#confirmDisplay").click(function(){
		var number=$("#realNumber").html();
		var url=$(this).attr("data-url");
		var keyword=$("#import").val();
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{number:number,keyword:keyword},
			success:function(res){
				if(res.code=="10111"){
					location.reload();
				}
			}
		});
	});
	$("#ascNumber").click(function(){
		var number=$("#realNumber").html();
		$("#realNumber").html(number-0+10);
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
					productsItem.find(".specImg").eq(0).attr("src",info.specImg);
					productsItem.find(".model").eq(0).html(info.model);
					productsItem.find(".paramOther").eq(0).html(info.paramOther);
					productsItem.find(".singlePrice").eq(0).html(info.rmb);
					productsItem.find(".productsInventory").eq(0).html(info.inventory);
				}else{
					productsItem.find(".specImg").eq(0).attr("src",info.specImg);
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
		var that=$(this);
		$.ajax({
			url:url2,
			type:"post",
			dataType:"json",
			data:{specId:specId},
			success:function(res){
				if(res.code==10112){
					$(".cartNum").html(res.info);
					that.css({"background-color":"#e7e7e7"}).html(inCart);
				}
			}
		});
	});
        
        
        
        	$(".productsItem").on("click",".addCollo",function(){
		var specId=$(this).attr("data-spec-id");
		var that=$(this);
		$.ajax({
			url:url4,
			type:"post",
			dataType:"json",
			data:{specId:specId},
			success:function(res){
				if(res.code==10112){
					$(".shoucang").html(res.info);
					that.css({"background-color":"#e7e7e7"}).html();
				}
			}
		});
	});
        
        
        
        
        
        
        
//    $(".productsItem").on("click",".addCollo",function(){
//        var specId=$(this).attr("data-spec-id");
//        var that=$(this);
//        $.ajax({
//            url:url2,
//            type:"post",
//            dataType:"json",
//            data:{specId:specId},
//            success:function(res){
//            }
//        });
//	});
	/****************顶部搜索************/

	/*****
	****	1.  加载小接头
	***/
	$("#serial").change(function(){
		loadAllSmallJoint()
	});

	function loadAllSmallJoint(fn,joint){
		var sid=$("#serial").val();
		var url=$("#serial").attr("data-url");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{sid:sid},
			success:function(res){
				$("#joint").html(res.msg);
				if(typeof fn=="function"){
					fn(joint);
				}
			}
		});
	}
	autoLoad();
	function autoLoad(){
		var cur=$("#serial").val();
		if(cur!=0){
			var joint=$("#serial").attr("data-joint");
			loadAllSmallJoint(function(joint){
				$("#joint").val(joint);
			},joint);
		}
	}

	/*****
	****	2.  加载小接头
	***/
	$("#searchBtn").click(function(){
		var serial=$("#serial").val();
		var joint=$("#joint").val();
		var keywords=$("#keywords").val();
		$.ajax({
			url:url3,
			type:"post",
			dataType:"json",
			data:{serial:serial,joint:joint,keywords:keywords},
			success:function(res){
				location.href=res.msg;
			}
		});
	});
 });
