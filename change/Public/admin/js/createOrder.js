$(function(){
	layui.use("layer");
	
	$("#salesItem").change(function(){
		if($(".productList").find(".productsItem").size()>0){
			var salesItem=$("#salesItem").val();
			if(salesItem==1){
				$("#serviceFee").val(250.00);
				serviceFee();
			}
			if(salesItem==2){
				$("#serviceFee").val(450.00);
				serviceFee();
			}
		}
	});
	
	$("#orderCurrency").change(function(){
		switchSalesTerms();
		
		var url=$(this).attr("data-url");
		var currency=$("#orderCurrency").val();
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{currency:currency},
			success:function(res){
				writeHtml(res);
				$("#salesItem").change();
			}
		});
	});
	switchSalesTerms();
	function switchSalesTerms(){
		var currency=$("#orderCurrency").val();
		if(currency=="RMB"){
			$("#sales-terms-box").hide();
		}else{
			$("#sales-terms-box").show();
		}
	}
	
	function writeHtml(res){
		if(res.code="10161"){
			var msg=res.msg;
			var index=0;
			var orderCurrency=$("#orderCurrency").val();
			
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
			var pList=msg.order_detail;
			
			for(var pitem in pList){
				index++;
				var temp=pList[pitem];
				secondUl+='<ul class="productsItem">';
				if(temp.spec_img){
					secondUl+='<li><a class="delete" data-id="'+temp.specification_id+'">-</a><img src="'+publicPath+temp.spec_img+'"></li>';
				}else{
					secondUl+='<li><a class="delete" data-id="'+temp.specification_id+'">-</a><img src="'+publicPath+temp.products_img+'"></li>';
				}
				secondUl+='<li>'+temp.model_name+'</li>';
				secondUl+='<li>'+temp.products_chinese_name+'</li>';
				secondUl+='<li>'+temp.length+'m</li>';
				secondUl+='<li>'+temp.color_name+'</li>';
				secondUl+='<li>'+temp.loading+'</li>';
				secondUl+='<li><input class="orderTotalNumber" autocomplete="off" data-id="'+temp.specification_id+'" value="'+temp.total_number+'" type="text"></li>';
				secondUl+='<li><input class="orderSinglePrice" autocomplete="off" data-id="'+temp.specification_id+'" value="'+temp.rmb+'" type="text" ></li>';
				secondUl+='<li class="amount">'+temp.amount+'</li>';
				secondUl+="</ul>";
			}
			
			var threeUl='<dl id="serviceFeeDL"><div>';
			if(orderCurrency=="RMB"){
				threeUl+='<dt>物流费用(RMB)</dt>';
			}
			if(orderCurrency=="USD"){
				threeUl+='<dt>报关手续费(USD)</dt>';
			}
			threeUl+='<dd><input autocomplete="off" id="serviceFee" value="'+msg.service_fee+'" type="text"></dd>';
			threeUl+='</div></dl>';
			
			var fourthUl='<dl id="orderTotalPriceDL"><div>';
			if(orderCurrency=="RMB"){
				fourthUl+='<dt>RMB总计</dt>';
			}
			if(orderCurrency=="USD"){
				fourthUl+='<dt>USD总计</dt>';
			}
			fourthUl+='<dd id="orderTotalPrice">'+msg.total_price2+'</dd>';
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
	
	$("#userUID").change(function(){
		var userId=$(this).val();
		loadRequiredInfo(userId);
	});
	loadRequiredInfo($("#userUID").val());
	
	$(".logisticsListBox").on("click",".addLogistics",function(){
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
	
	$(".forworderListBox").on("click",".addForworder",function(){
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
	
	$(".invoiceListBox").on("click",".addInvoice",function(){
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
						$("#invoiceContent").find("option[data-id="+info.invoice_content_id+"]").prop("selected",true);
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
		var userId=$("#userUID").val();
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
					that.val(msg.total_number);
					that.parents(".productsItem").find(".amount").html(msg.amount);
					$("#orderTotalPrice").html(msg.total_price);
				}
			}
		});
	});
	
	$(".productList").on("input propertychange",".orderSinglePrice",function(){
		var dataId=$(this).attr("data-id");
		var userId=$("#userUID").val();
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
		serviceFee();
	});
	
	function serviceFee(){
		var that=$("#serviceFee");
		var dataId=that.attr("data-id");
		var userId=$("#userUID").val();
		var price=that.val();
		if(price<=0){
			price=0;
			that.val(price);
		}
		
		$.ajax({
			url:url11,
			type:"post",
			dataType:"json",
			data:{dataId:dataId,price:price,userId:userId},
			success:function(res){
				if(res.code="10161"){
					var msg=res.msg;
					//that.val(msg.price);
					$("#orderTotalPrice").html(msg.total_price);
				}
			}
		});
	}
	
	$(".productList").on("click",".delete",function(){
		var dataId=$(this).attr("data-id");
		$.ajax({
			url:url2,
			type:"post",
			dataType:"json",
			data:{dataId:dataId},
			success:function(res){
				writeHtml(res);
			}
		});
	});
	
	$(".specList").on("click",".specAdd",function(){
		var dataId=$(this).attr("data-id");
		var userId=$("#userUID").val();
		var currency=$("#orderCurrency").val();
		var that=$(this);
		$.ajax({
			url:url8,
			type:"post",
			dataType:"json",
			data:{dataId:dataId,userId:userId,currency:currency},
			success:function(res){
				if(res.code="10161"){
					var msg=res.msg;
					var index=0;
					var orderCurrency=$("#orderCurrency").val();
					
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
					var pList=msg.order_detail;
					for(var pitem in pList){
						index++;
						var temp=pList[pitem];
						secondUl+='<ul class="productsItem">';
						if(temp.spec_img){
							secondUl+='<li><a class="delete" data-id="'+temp.specification_id+'">-</a><img src="'+publicPath+temp.spec_img+'"></li>';
						}else{
							secondUl+='<li><a class="delete" data-id="'+temp.specification_id+'">-</a><img src="'+publicPath+temp.products_img+'"></li>';
						}
						
						
						secondUl+='<li>'+temp.model_name+'</li>';
						secondUl+='<li>'+temp.products_chinese_name+'</li>';
						secondUl+='<li>'+temp.length+'m</li>';
						secondUl+='<li>'+temp.color_name+'</li>';
						secondUl+='<li>'+temp.loading+'</li>';
						secondUl+='<li><input class="orderTotalNumber" autocomplete="off" data-id="'+temp.specification_id+'" value="'+temp.total_number+'" type="text"></li>';
						secondUl+='<li><input class="orderSinglePrice" autocomplete="off" data-id="'+temp.specification_id+'" value="'+temp.rmb+'" type="text"></li>';
						secondUl+='<li class="amount">'+temp.amount+'</li>';
						secondUl+="</ul>";
					}
					
					var threeUl='<dl id="serviceFeeDL"><div>';
					if(orderCurrency=="RMB"){
						threeUl+='<dt>物流费用(RMB)</dt>';
					}
					if(orderCurrency=="USD"){
						threeUl+='<dt>报关手续费(USD)</dt>';
					}
					threeUl+='<dd><input autocomplete="off" id="serviceFee" value="'+msg.service_fee+'" type="text"></dd>';
					threeUl+='</div></dl>';
					
					var fourthUl='<dl id="orderTotalPriceDL"><div>';
					if(orderCurrency=="RMB"){
						fourthUl+='<dt>RMB总计</dt>';
					}
					if(orderCurrency=="USD"){
						fourthUl+='<dt>USD总计</dt>';
					}
					fourthUl+='<dd id="orderTotalPrice">'+msg.total_price2+'</dd>';
					fourthUl+='</div></dl>';
					
					if(index>0){
						$(".productList").html(titleUl+secondUl+threeUl+fourthUl);
						$(".orderConfirm").show();
						that.removeClass("specAdd").css({"color":"#ccc","cursor":"default"}).html("已添加");
					}else{
						$(".orderConfirm").hide();
					}
				}
			}
		});
	});
	
	$("#saveBtn").click(function(){
		var url=$(this).attr("data-url");
		var userId=$("#userUID").val();
		var logistics={};
		logistics.logCompanyName=$("#logCompanyName").val();
		logistics.logReceiver=$("#logReceiver").val();
		logistics.logReceiverPhone=$("#logReceiverPhone").val();
		logistics.logReceiverEmail=$("#logReceiverEmail").val();
		logistics.logCountry=$("#logCountry").val();
		logistics.city=$("#city").val();
		logistics.prov=$("#prov").val();
		logistics.address=$("#address").val();
		
		var forworder={};
		forworder.fwdCompanyName=$("#fwdCompanyName").val();
		forworder.fwdReceiver=$("#fwdReceiver").val();
		forworder.fwdReceiverPhone=$("#fwdReceiverPhone").val();
		forworder.fwdReceiverEmail=$("#fwdReceiverEmail").val();
		forworder.fwdCountry=$("#fwdCountry").val();
		forworder.fwdCity=$("#fwdCity").val();
		forworder.fwdProv=$("#fwdProv").val();
		forworder.fwdAddress=$("#fwdAddress").val();
		
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
		
		var order={};
		order.orderPONumber=$("#orderPONumber").val();
		order.orderRemark=$("#orderRemark").val();
		order.orderCurrency=$("#orderCurrency").val();
		order.orderSales=$("#orderSales").val();
		
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{logistics:logistics,forworder:forworder,invoice:invoice,order:order,userId:userId},
			success:function(res){
				if(res.code="10161"){
					layer.msg(res.msg);
					//location.reload();
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	
	$("#searchModelName,#keyword").bind("input propertychange",function(){
		where=wrapCondition();
		dataDeal(url10,where);
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
				dataDeal(url10,where);
			}
		});
	});
	$("#joint").change(function(){
		where=wrapCondition();
		dataDeal(url10,where);
	});
	
	$("#page").on("click","a.next,a.prev,a.num",function(e){
		e.preventDefault();
		var curPageLink=$(this).attr("href");
		$("#page").attr("data-cur-page",curPageLink);
		where=wrapCondition();
		dataDeal(curPageLink,where);
	});
	dataDeal(url10,wrapCondition());
	function dataDeal(url,where){
		var userId=$("#userUID").val();
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
	function loadRequiredInfo(userId){
		$.ajax({
			url:url9,
			type:"post",
			dataType:"json",
			data:{userId:userId},
			success:function(res){
				if(res.code=="10161"){
					var logistics=res.msg.logistics;
					var forworder=res.msg.forworder;
					var invoice=res.msg.invoice;
					
					var luls='';
					var fuls='';
					var iuls='';
					
					for(var i=0,j=logistics.length;i<j;i++){
						var temp=logistics[i];
						var ul="<ul class='logisticsItemBox'>";
						ul+='<li>'+temp.logistics_company_name+'</li>';
						ul+='<li>'+temp.logistics_receiver+'</li>';
						ul+='<li>'+temp.logistics_receiver_phone+'</li>';
						ul+='<li>'+temp.logistics_country+temp.logistics_province+temp.logistics_city+temp.logistics_dist+temp.logistics_address+'</li>';
						ul+='<li><a class="addLogistics" href="javascript:;" data-id="'+temp.logistics_id+'">添加</a></li>';
						ul+='</ul><div class="hr_01"></div>';
						luls+=ul;
					}
					
					for(var p=0,q=forworder.length;p<q;p++){
						var temp2=forworder[p];
						var ul="<ul class='forworderItemBox'>";
						ul+='<li>'+temp2.for_company_name+'</li>';
						ul+='<li>'+temp2.for_receiver+'</li>';
						ul+='<li>'+temp2.for_receiver_phone+'</li>';
						ul+='<li>'+temp2.for_country+temp2.for_province+temp2.for_city+temp2.for_dist+temp2.for_address+'</li>';
						ul+='<li><a class="addForworder" href="javascript:;" data-id="'+temp2.forworder_id+'">添加</a></li>';
						ul+='</ul><div class="hr_01"></div>';
						fuls+=ul;
					}
					
					for(var s=0,t=invoice.length;s<t;s++){
						var temp=invoice[s];
						var ul="<ul class='invoiceItemBox'>";
						if(temp.invoice_type_id==1){
							ul+='<li>个人</li>';
						}else{
							ul+='<li>单位专用发票</li>';
						}
						ul+='<li>'+filterStr(temp.invoice_name)+'</li>';
						ul+='<li>'+filterStr(temp.invoice_credit)+'</li>';
						if(temp.invoice_content_id==1){
							ul+='<li>大类</li>';
						}else{
							ul+='<li>明细</li>';
						}
						ul+='<li><a class="addInvoice" href="javascript:;" data-id="'+temp.invoice_id+'">添加</a></li>';
						ul+='</ul><div class="hr_01"></div>';
						iuls+=ul;
					}
					
					$(".logisticsListBox").find(".logisticsItemBox").remove();
					$(".forworderListBox").find(".forworderItemBox").remove();
					$(".invoiceListBox").find(".invoiceItemBox").remove();
					
					$(".logisticsListBox").append(luls);
					$(".forworderListBox").append(fuls);
					$(".invoiceListBox").append(iuls);
				}	
			}
		});
	}
	
	function filterStr(str){
		if(!(str.replace(/^\s+|\s+$/,'')))str="--";
		return str;
	}
	function subS(s, n){
		return s.replace(/([^x00-xff])/g, "$1a").slice(0, n).replace(/([^x00-xff])a/g, "$1");
	}
});