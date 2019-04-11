$(function(){
	layui.use("layer");
	updateDist();
	function updateDist(fn){
		var curCity=$("#city").val();
		$.ajax({
			url:url2,
			type:"post",
			dataType:"json",
			data:{city_id:curCity},
			success:function(res){
				var info=res.msg;
				var opt="";
				var curDist=$("#dist").attr("data-dist");
				for(var i=0,j=info.length;i<j;i++){
					if(curDist==info[i].area_name){
						opt+='<option value="'+info[i].area_id+'" selected>'+info[i].area_name+'</option>';
					}else{
						opt+='<option value="'+info[i].area_id+'">'+info[i].area_name+'</option>';
					}
				}
				$("#dist").html(opt);
				if(typeof fn=="function"){
					fn();
				}
			}
		});
	}
	$("#city").change(function(){
		updateDist();
	});
	updateDist2();
	function updateDist2(fn){
		var curCity=$("#fwdCity").val();
		$.ajax({
			url:url2,
			type:"post",
			dataType:"json",
			data:{city_id:curCity},
			success:function(res){
				var info=res.msg;
				var opt="";
				var curDist=$("#fwdDist").attr("data-dist");
				for(var i=0,j=info.length;i<j;i++){
					if(curDist==info[i].area_name){
						opt+='<option value="'+info[i].area_id+'" selected>'+info[i].area_name+'</option>';
					}else{
						opt+='<option value="'+info[i].area_id+'">'+info[i].area_name+'</option>';
					}
				}
				$("#fwdDist").html(opt);
				if(typeof fn=="function"){
					fn();
				}
			}
		});
	}
	$("#fwdCity").change(function(){
		updateDist2();
	});
	
	$(".addLogistics").click(function(){
		var dataId=$(this).attr("data-id");
		$.ajax({
			url:url3,
			type:"post",
			dataType:"json",
			data:{dataId:dataId},
			success:function(res){
				if(res.code="10161"){
					var info=res.msg;
					$("#logCompanyName").val(info.logistics_company_name);
					$("#logReceiver").val(info.logistics_receiver);
					$("#logReceiverPhone").val(info.logistics_receiver_phone);
					$("#logReceiverEmail").val(info.logistics_receiver_email);
					var opts=$("#city").find("option");
					for(var i=0,j=opts.size();i<j;i++){
						if((opts.eq(i).html()).replace(/^\s+||\s+$/,'')==info.logistics_city.replace(/^\s+||\s+$/,'')){
							opts.eq(i).attr("selected",true);
						}
					}
					updateDist(function(){
						var opts2=$("#dist").find("option");
						for(var i=0,j=opts2.size();i<j;i++){
							if((opts2.eq(i).html()).replace(/^\s+||\s+$/,'')==info.logistics_dist.replace(/^\s+||\s+$/,'')){
								opts2.eq(i).attr("selected",true);
							}
						}
					});
					$("#address").val(info.logistics_address);
				}
				$(".modal_01").removeClass('in_01');
				$(".modal_01").css('z-index','-1');
				$(".modal-backup_01").css('display','none');
			}
		});
	});
	
	$(".addForworder").click(function(){
		var dataId=$(this).attr("data-id");
		$.ajax({
			url:url4,
			type:"post",
			dataType:"json",
			data:{dataId:dataId},
			success:function(res){
				if(res.code="10161"){
					var info=res.msg;
					$("#fwdCompanyName").val(info.for_company_name);
					$("#fwdReceiver").val(info.for_receiver);
					$("#fwdReceiverPhone").val(info.for_receiver_phone);
					$("#fwdReceiverEmail").val(info.for_receiver_email);
					var opts=$("#fwdCity").find("option");
					for(var i=0,j=opts.size();i<j;i++){
						if((opts.eq(i).html()).replace(/^\s+||\s+$/,'')==info.for_city.replace(/^\s+||\s+$/,'')){
							opts.eq(i).attr("selected",true);
						}
					}
					updateDist(function(){
						var opts2=$("#fwdDist").find("option");
						for(var i=0,j=opts2.size();i<j;i++){
							if((opts2.eq(i).html()).replace(/^\s+||\s+$/,'')==info.for_dist.replace(/^\s+||\s+$/,'')){
								opts2.eq(i).attr("selected",true);
							}
						}
					});
					$("#fwdAddress").val(info.for_address);
				}
				$(".modal_01").removeClass('in_01');
				$(".modal_01").css('z-index','-1');
				$(".modal-backup_01").css('display','none');
			}
		});
	});
	
	$(".addInvoice").click(function(){
		var dataId=$(this).attr("data-id");
		$.ajax({
			url:url5,
			type:"post",
			dataType:"json",
			data:{dataId:dataId},
			success:function(res){
				if(res.code="10161"){
					var info=res.msg;
					var invoiceType=info.invoice_type_id;
					if(invoiceType==1){
						$("#invoiceType").find("option[data-id=1]").attr("selected",true);
						$("#invoiceName").val(info.invoice_name);
						$("#invoiceContent").find("option[data-id="+info.invoice_content_id+"]").attr("selected",true);
						$(".invoiceCredit").hide();
						$(".invoiceAddon").hide();
						$(".invoiceName").show();
					}
					if(invoiceType==2){
						$("#invoiceType").find("option[data-id=2]").attr("selected",true);
						$("#invoiceCredit").val(info.invoice_credit);
						var addon=info.invoice_addon;
						$(".invoiceAddon").find("a[data-category=taxes]").eq(0).attr("href",publicPath+addon.taxes.path).attr("data-path",addon.taxes.path);
						$(".invoiceAddon").find("a[data-category=license]").eq(0).attr("href",publicPath+addon.license.path).attr("data-path",addon.license.path);
						$(".invoiceAddon").find("a[data-category=invoice]").eq(0).attr("href",publicPath+addon.invoice.path).attr("data-path",addon.invoice.path);
						$(".invoiceCredit").show();
						$(".invoiceAddon").show();
						$(".invoiceName").hide();
					}
				}
				$(".modal_01").removeClass('in_01');
				$(".modal_01").css('z-index','-1');
				$(".modal-backup_01").css('display','none');
			}
		});
	});
	
	$(".productList").on("input propertychange",".orderTotalNumber",function(){
		var dataId=$(this).attr("data-id");
		var number=$(this).val();
		if(number<=0){
			number=0;
			$(this).val(number);
		}
		var that=$(this);
		$.ajax({
			url:url6,
			type:"post",
			dataType:"json",
			data:{dataId:dataId,number:number},
			success:function(res){
				if(res.code="10161"){
					var msg=res.msg;
					that.val(msg.number);
					that.parents(".productsItem").find(".amount").html(msg.amount);
					$("#orderTotalPrice").html(msg.total_price);
				}
			}
		});
	});
	
	$(".productList").on("input propertychange",".orderSinglePrice",function(){
		var dataId=$(this).attr("data-id");
		var price=$(this).val();
		if(price<=0){
			price=0;
			$(this).val(price);
		}
		var that=$(this);
		$.ajax({
			url:url7,
			type:"post",
			dataType:"json",
			data:{dataId:dataId,price:price},
			success:function(res){
				if(res.code="10161"){
					var msg=res.msg;
					that.val(msg.price);
					that.parents(".productsItem").find(".amount").html(msg.amount);
					$("#orderTotalPrice").html(msg.total_price);
				}
			}
		});
	});
	
	$(".productList").on("input propertychange","#serviceFee",function(){
		var dataId=$(this).attr("data-id");
		var url=$(this).attr("data-url");
		var price=$(this).val();
		if(price<=0){
			price=0;
			$(this).val(price);
		}
		var that=$(this);
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{dataId:dataId,price:price},
			success:function(res){
				if(res.code="10161"){
					var msg=res.msg;
					that.val(msg.price);
					$("#orderTotalPrice").html(msg.total_price);
				}
			}
		});
	});
	
	$(".specList").on("click",".specAdd",function(){
		var dataId=$(this).attr("data-id");
		var userId=$("#orderUser").attr("data-id");
		var oId=$("#orderUser").attr("data-oid");
		var that=$(this);
		$.ajax({
			url:url8,
			type:"post",
			dataType:"json",
			data:{dataId:dataId,userId:userId,oId:oId},
			success:function(res){
				if(res.code="10161"){
					var msg=res.msg;
					var ul='<ul class="productsItem">';
					ul+='<li><a class="delete" data-id="'+msg.order_detail_id+'">-</a><img src="'+publicPath+msg.products_img+'"></li>';
					ul+='<li>'+msg.model_name+'</li>';
					ul+='<li>'+msg.products_chinese_name+'</li>';
					ul+='<li>'+msg.length+'m</li>';
					ul+='<li>'+msg.color_name+'</li>';
					ul+='<li>'+msg.loading+'</li>';
					ul+='<li><input class="orderTotalNumber" autocomplete="off" data-id="'+msg.order_detail_id+'" value="'+msg.total_number+'" type="text"></li>';
					ul+='<li><input class="orderSinglePrice" autocomplete="off" data-id="'+msg.order_detail_id+'" value="'+msg.rmb+'" type="text"></li>';
					ul+='<li class="amount">'+msg.amount+'</li>';
					ul+="</ul>";
					$("#orderTotalPrice").html(msg.total_price);
					$("#serviceFeeDL").before(ul);
					that.removeClass("specAdd").css({"color":"#ccc","cursor":"default"}).html("已添加");
					
					$(".modal_02").removeClass('in_02');
					$(".modal_02").css('z-index','-1');
					$(".modal-backup_02").css('display','none');
				}
			}
		});
	});
	
	$("#saveBtn").click(function(){
		var url=$(this).attr("data-url");
		var logistics={};
		logistics.logCompanyName=$("#logCompanyName").val();
		logistics.logReceiver=$("#logReceiver").val();
		logistics.logReceiverPhone=$("#logReceiverPhone").val();
		logistics.logReceiverEmail=$("#logReceiverEmail").val();
		logistics.logCountry=$("#logCountry").val();
		logistics.city=$("#city").val();
		logistics.dist=$("#dist").val();
		logistics.address=$("#address").val();
		logistics.id=$("#logisticsBox").attr("data-id");
		
		var forworder={};
		forworder.fwdCompanyName=$("#fwdCompanyName").val();
		forworder.fwdReceiver=$("#fwdReceiver").val();
		forworder.fwdReceiverPhone=$("#fwdReceiverPhone").val();
		forworder.fwdReceiverEmail=$("#fwdReceiverEmail").val();
		forworder.fwdCompanyArea=$("#fwdCompanyArea").val();
		forworder.fwdCity=$("#fwdCity").val();
		forworder.fwdDist=$("#fwdDist").val();
		forworder.fwdAddress=$("#fwdAddress").val();
		forworder.id=$("#forworderBox").attr("data-id");
		
		var invoice={};
		invoice.invoiceType=$("#invoiceType").val();
		if($("#invoiceType").val()=="个人"){
			invoice.invoiceName=$("#invoiceName").val();
		}else{
			invoice.invoiceCredit=$("#invoiceCredit").val();
			invoice.addon={
				"taxes":$("#invoiceAddon").find("a[data-category=taxes]").eq(0).attr("data-path"),
				"license":$("#invoiceAddon").find("a[data-category=license]").eq(0).attr("data-path"),
				"invoice":$("#invoiceAddon").find("a[data-category=invoice]").eq(0).attr("data-path")
			};
		}
		invoice.invoiceContent=$("#invoiceContent").val();
		invoice.id=$("#invoiceBox").attr("data-id");
		
		var order={};
		order.operator=$("#operator").val();
		order.id=$("#orderUser").attr('data-oid');
		
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{logistics:logistics,forworder:forworder,invoice:invoice,order:order},
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
	
	$("#searchModelName,#keyword").bind("input propertychange",function(){
		where=wrapCondition();
		dataDeal(location.href,where);
	});
	$("#serial,#joint").change(function(){
		where=wrapCondition();
		dataDeal(location.href,where);
	});
	$("#page").on("click","a.next,a.prev,a.num",function(e){
		e.preventDefault();
		var curPageLink=$(this).attr("href");
		$("#page").attr("data-cur-page",curPageLink);
		where=wrapCondition();
		dataDeal(curPageLink,where);
	});
	function dataDeal(url,where){
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{where:where},
			success:function(res){
				if(res.code=="10161"){
					var info=res.msg;
					var specInfo=info.spec_info;
					var specIds=info.specIds;
					var uls='';
					for(var i=0,j=specInfo.length;i<j;i++){
						var temp=specInfo[i];
						var flag=false;
						var ul='<ul class="specItem">';
						ul+='<li>'+temp.model_name+'</li>';
						ul+='<li>'+temp.length+'m</li>';
						ul+='<li>'+temp.inventory+'</li>';
						for(var p=0,q=specIds.length;p<q;p++){
							if(specIds[p]==temp.specification_id){
								flag=true;
								break;
							}
						}
						if(flag){
							ul+='<li><a href="javascript:;" style="color:#ccc;cursor:default;">已添加</a></li>';
						}else{
							ul+='<li><a class="specAdd" href="javascript:;" data-id="'+temp.specification_id+'">添加</a></li>';
						}
						ul+='</ul><div class="hr"></div>';
						uls+=ul;
					}
					$(".specList").find(".specItem,.hr").remove();
					$("#page").before(uls);
					$("#page").html(info.page);
				}	
			}
		});
	}
	function wrapCondition(){
		var where={};
		var searchModelName=$("#searchModelName").val();
		var keyword=$("#keyword").val();
		var serial=$("#serial").val();
		var joint=$("#joint").val();
		where.searchModel=searchModelName;
		where.searchKeyWord=keyword;
		where.searchCategory=serial;
		where.searchJoint=joint;
		return where;
	}
});