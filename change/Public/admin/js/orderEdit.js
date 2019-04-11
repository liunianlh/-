$(function(){
	layui.use("layer");
	$("#orderCurrency").change(function(){
		var v=$(this).val();
		if(v=="RMB"){
			location.href=edit;
		}else if(v=="USD"){
			location.href=editc;
		}
	});
	
	$("#cancelBtn").click(function(){
		var url=$(this).attr("data-url");
		var id=$("#orderUser").attr('data-oid');
		
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{id:id},
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
					$("#address").val(info.logistics_address);
					
					var countryId=info.logistics_country_id;
					$("#logCountry").val(countryId);
					getPC(function(){
						if(countryId==1){
							var reg=/^(\s+)|(\s+)$/;
							var opts=$("#prov").find("option");
							for(var i=0,j=opts.size();i<j;i++){
								if((opts.eq(i).html()).replace(reg,'')==info.logistics_province.replace(reg,'')){
									opts.eq(i).attr("selected",true);
									break;
								}
							}
							
							getC(function(info){
								var opts2=$("#city").find("option");
								for(var i=0,j=opts2.size();i<j;i++){
									if((opts2.eq(i).html()).replace(reg,'')==info.logistics_city.replace(reg,'')){
										opts2.eq(i).attr("selected",true);
										break;
									}
								}
							},info);
						}else{
							$("#prov").val(info.logistics_province);
							$("#city").val(info.logistics_city);
						}
					});
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
					$("#fwdAddress").val(info.for_address);
					
					var countryId=info.for_country_id;
					$("#fwdCountry").val(countryId);
					getPC2(function(){
						if(countryId==1){
							var reg=/^(\s+)|(\s+)$/;
							var opts=$("#fwdProv").find("option");
							for(var i=0,j=opts.size();i<j;i++){
								if((opts.eq(i).html()).replace(reg,'')==info.for_province.replace(reg,'')){
									opts.eq(i).attr("selected",true);
									break;
								}
							}
							getC2(function(info){
								var opts2=$("#fwdCity").find("option");
								for(var i=0,j=opts2.size();i<j;i++){
									if((opts2.eq(i).html()).replace(reg,'')==info.for_city.replace(reg,'')){
										opts2.eq(i).attr("selected",true);
										break;
									}
								}
							},info);
						}else{
							$("#fwdProv").val(info.for_province);
							$("#fwdCity").val(info.for_city);
						}
					});
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
						$(".invoiceAddon").find("a[data-category=taxes]").eq(0).attr("href",publicPath+addon.taxes.path).attr("data-path",addon.taxes.path).attr("data-name",addon.taxes.name).html(addon.taxes.name);
						$(".invoiceAddon").find("a[data-category=license]").eq(0).attr("href",publicPath+addon.license.path).attr("data-path",addon.license.path).attr("data-name",addon.license.name).html(addon.license.name);
						$(".invoiceAddon").find("a[data-category=invoice]").eq(0).attr("href",publicPath+addon.invoice.path).attr("data-path",addon.invoice.path).attr("data-name",addon.invoice.name).html(addon.invoice.name);
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
		var userId=$("#orderUser").attr("data-id");
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
			data:{dataId:dataId,number:number,userId:userId},
			success:function(res){
				if(res.code="10161"){
					var msg=res.msg;
					//that.val(msg.number);
					that.parents(".productsItem").find(".amount").html(msg.amount);
					$("#orderTotalPrice").html(msg.total_price);
				}
			}
		});
	});
	
	$(".productList").on("input propertychange",".orderSinglePrice",function(){
		var dataId=$(this).attr("data-id");
		var userId=$("#orderUser").attr("data-id");
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
			data:{dataId:dataId,price:price,userId:userId},
			success:function(res){
				if(res.code="10161"){
					var msg=res.msg;
					//that.val(msg.price);
					that.parents(".productsItem").find(".amount").html(msg.amount);
					$("#orderTotalPrice").html(msg.total_price);
				}
			}
		});
	});
	
	$(".productList").on("input propertychange","#serviceFee",function(){
		var userId=$("#orderUser").attr("data-id");
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
			data:{price:price,userId:userId},
			success:function(res){
				if(res.code="10161"){
					var msg=res.msg;
					//that.val(msg.price);
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
					ul+='<li><a class="delete" data-id="'+msg.specification_id+'">-</a><img src="'+publicPath+msg.products_img+'"></li>';
					ul+='<li>'+msg.model_name+'</li>';
					ul+='<li>'+msg.products_chinese_name+'</li>';
					ul+='<li>'+msg.length+'m</li>';
					ul+='<li>'+msg.color_name+'</li>';
					ul+='<li>'+msg.loading+'</li>';
					ul+='<li><input class="orderTotalNumber" autocomplete="off" data-id="'+msg.specification_id+'" value="'+msg.total_number+'" type="text"></li>';
					ul+='<li><input class="orderSinglePrice" autocomplete="off" data-id="'+msg.specification_id+'" value="'+msg.rmb+'" type="text"></li>';
					ul+='<li class="amount">'+msg.amount+'</li>';
					ul+="</ul>";
					$("#orderTotalPrice").html(msg.total_price);
					$("#serviceFeeDL").before(ul);
					that.removeClass("specAdd").css({"color":"#ccc","cursor":"default"}).html("已添加");
					
				}
			}
		});
	});
	
	$(".productList").on("click",".delete",function(){
		var dataId=$(this).attr("data-id");
		var userId=$("#orderUser").attr("data-id");
		$.ajax({
			url:url2,
			type:"post",
			dataType:"json",
			data:{dataId:dataId,userId:userId},
			success:function(res){
				writeHtml(res);
			}
		});
	});
	
	function writeHtml(res){
		if(res.code="10161"){
			var msg=res.msg;
			var index=0;
			
			var titleUl='<ul>';
			titleUl+='<li>产品图片</li>';
			titleUl+='<li>型号</li>';
			titleUl+='<li>规格</li>';
			titleUl+='<li>长度</li>';
			titleUl+='<li>颜色</li>';
			titleUl+='<li>整箱数量</li>';
			titleUl+='<li>订单总数量</li>';
			titleUl+='<li>产品单价</li>';
			titleUl+='<li>小计</li>';
			titleUl+='</ul>';
			
			var secondUl='';
			var pList=msg.detail;
			for(var pitem in pList){
				index++;
				var temp=pList[pitem];
				secondUl+='<ul class="productsItem">';
				secondUl+='<li><a class="delete" data-id="'+temp.specification_id+'">-</a><img src="'+publicPath+temp.products_img+'"></li>';
				secondUl+='<li>'+temp.model_name+'</li>';
				secondUl+='<li>'+temp.products_chinese_name+'</li>';
				secondUl+='<li>'+temp.length+'m</li>';
				secondUl+='<li>'+temp.color_name+'</li>';
				secondUl+='<li>'+temp.loading+'</li>';
				secondUl+='<li><input class="orderTotalNumber" autocomplete="off" data-id="'+temp.specification_id+'" value="'+temp.total_number+'" type="text"></li>';
				secondUl+='<li><input class="orderSinglePrice" autocomplete="off" data-id="'+temp.specification_id+'" value="'+temp.price+'" type="text"></li>';
				secondUl+='<li class="amount">'+temp.amount+'</li>';
				secondUl+="</ul>";
			}
			
			var threeUl='<dl id="serviceFeeDL"><div>';
			threeUl+='<dt>物流费用(RMB)</dt>';
			threeUl+='<dd><input autocomplete="off" id="serviceFee" value="'+msg.order.service_fee+'" type="text"></dd>';
			threeUl+='</div></dl>';
			
			var fourthUl='<dl id="orderTotalPriceDL"><div>';
			fourthUl+='<dt>RMB总计</dt>';
			fourthUl+='<dd id="orderTotalPrice">'+msg.order.order_total_price2+'</dd>';
			fourthUl+='</div></dl>';
			
			if(index>0){
				$(".productList").html(titleUl+secondUl+threeUl+fourthUl);
				$(".orderConfirm").show();
			}else{
				$(".productList").html('');
				$(".orderConfirm").hide();
			}
		}
	}
	
	$("#saveBtn").click(function(){
		var url=$(this).attr("data-url");
		var logistics={};
		logistics.logCompanyName=$("#logCompanyName").val();
		logistics.logReceiver=$("#logReceiver").val();
		logistics.logReceiverPhone=$("#logReceiverPhone").val();
		logistics.logReceiverEmail=$("#logReceiverEmail").val();
		logistics.logCountry=$("#logCountry").val();
		logistics.city=$("#city").val();
		logistics.prov=$("#prov").val();
		logistics.address=$("#address").val();
		logistics.id=$("#logisticsBox").attr("data-id");
		
		var forworder={};
		forworder.fwdCompanyName=$("#fwdCompanyName").val();
		forworder.fwdReceiver=$("#fwdReceiver").val();
		forworder.fwdReceiverPhone=$("#fwdReceiverPhone").val();
		forworder.fwdReceiverEmail=$("#fwdReceiverEmail").val();
		forworder.fwdCountry=$("#fwdCountry").val();
		forworder.fwdCity=$("#fwdCity").val();
		forworder.fwdProv=$("#fwdProv").val();
		forworder.fwdAddress=$("#fwdAddress").val();
		forworder.id=$("#forworderBox").attr("data-id");
		
		var invoice={};
		invoice.invoiceType=$("#invoiceType").val();
		if($("#invoiceType").val()=="个人"){
			invoice.invoiceName=$("#invoiceName").val();
		}else{
			invoice.invoiceCredit=$("#invoiceCredit").val();
			var invoiceAddon=$(".invoiceAddon");
			invoice.addon={
				"taxes":invoiceAddon.find("a[data-category=taxes]").eq(0).attr("data-path"),
				"taxesN":invoiceAddon.find("a[data-category=taxes]").eq(0).attr("data-name"),
				"license":invoiceAddon.find("a[data-category=license]").eq(0).attr("data-path"),
				"licenseN":invoiceAddon.find("a[data-category=license]").eq(0).attr("data-name"),
				"invoice":invoiceAddon.find("a[data-category=invoice]").eq(0).attr("data-path"),
				"invoiceN":invoiceAddon.find("a[data-category=invoice]").eq(0).attr("data-name")
			};
		}
		invoice.invoiceContent=$("#invoiceContent").val();
		invoice.id=$("#invoiceBox").attr("data-id");
		
		var order={};
		order.operator=$("#operator").val();
		order.id=$("#orderUser").attr('data-oid');
		order.currency=$("#orderCurrency").val();
		
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
	
	where=wrapCondition();
	dataDeal(url9,where);
	$("#searchModelName,#keyword").bind("input propertychange",function(){
		where=wrapCondition();
		dataDeal(url9,where);
	});
	$("#joint").change(function(){
		where=wrapCondition();
		dataDeal(url9,where);
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
				where=wrapCondition();
				dataDeal(url9,where);
			}
		});
	});
	$("#page").on("click","a.next,a.prev,a.num",function(e){
		e.preventDefault();
		var curPageLink=$(this).attr("href");
		$("#page").attr("data-cur-page",curPageLink);
		where=wrapCondition();
		dataDeal(curPageLink,where);
	});
	function dataDeal(url,where){
		var userId=$("#orderUser").attr("data-id");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{where:where,userId:userId},
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