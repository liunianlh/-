$(function(){
	layui.use("layer");
	$(".invoiceList .invoiceItem").click(function(){
		var dataId=$(this).attr("data-id");
		$.ajax({
			url:url5,
			type:"post",
			dataType:"json",
			data:{dataId:dataId},
			success:function(res){
				if(res.code==6){
					var msg=res.msg;
					var invoice_type_id=msg.invoice_type_id;
					var invoice_content_id=msg.invoice_content_id;
					if(invoice_type_id==1){//个人
						$(".invoiceType[data-type=1]").click();
						$("#invoiceName").val(msg.invoice_name);
						$(".invoiceContent2[data-type="+invoice_content_id+"]").click();
					}else{
						$(".invoiceType[data-type=2]").click();
						$("#invoiceCredit").val(msg.invoice_credit);
						$(".invoiceContent3[data-type="+invoice_content_id+"]").click();
						$(".fileList").find("a[data-category=taxes]").eq(0).remove();
						$(".fileList").find("a[data-category=license]").eq(0).remove();
						$(".fileList").find("a[data-category=invoice]").eq(0).remove();
						$(".fileList").append("<a target='_blank' data-category='taxes' data-pic='"+msg.addon.taxes.path+"' href='"+publicPath+msg.addon.taxes.path+"'>"+msg.addon.taxes.name+"</a>");
						$(".fileList").append("<a target='_blank' data-category='license' data-pic='"+msg.addon.license.path+"' href='"+publicPath+msg.addon.license.path+"'>"+msg.addon.license.name+"</a>");
						$(".fileList").append("<a target='_blank' data-category='invoice' data-pic='"+msg.addon.invoice.path+"' href='"+publicPath+msg.addon.invoice.path+"'>"+msg.addon.invoice.name+"</a>");
					}
				}
			}
		});
	});
	$(".forworderList .forworderItem").click(function(){
		var dataId=$(this).attr("data-id");
		$.ajax({
			url:url6,
			type:"post",
			dataType:"json",
			data:{dataId:dataId},
			success:function(res){
				if(res.code==6){
					var msg=res.msg;
					$("#fwdCompanyName").val(msg.for_company_name);
					$("#fwdReceiver").val(msg.for_receiver);
					$("#fwdReceiverPhone").val(msg.for_receiver_phone);
					$("#fwdReceiverEmail").val(msg.for_receiver_email);
					$("#fwdAddress").val(msg.for_address);
					$("#fwdShipment").val(msg.shipment_id);
					$("#fwdCountry").val(msg.for_country_id);
					if(msg.for_country_id==1){
						loadProv2(url12,function(prov){
							var reg=/^(\s+)|(\s+)$/;
							var opts=$("#fwdProv").find("option");
							for(var i=0,j=opts.size();i<j;i++){
								if((opts.eq(i).html()).replace(reg,'')==prov.replace(reg,'')){
									opts.eq(i).attr("selected",true);
									break;
								}
							}
							loadCity2(url11,function(city){
								var opts2=$("#fwdCity").find("option");
								for(var i=0,j=opts2.size();i<j;i++){
									if((opts2.eq(i).html()).replace(reg,'')==city.replace(reg,'')){
										opts2.eq(i).attr("selected",true);
										break;
									}
								}
							},msg.for_city);
						},msg.for_province);
					}else{
						switchAddr2(url10,function(){
							$("#fwdProv").val(msg.for_province);
							$("#fwdCity").val(msg.for_city);
						},'');
					}
				}
			}
		});
	});
	$(".logisticsList .logisticsItem").click(function(){
		var dataId=$(this).attr("data-id");
		$.ajax({
			url:url7,
			type:"post",
			dataType:"json",
			data:{dataId:dataId},
			success:function(res){
				if(res.code==6){
					var msg=res.msg;
					$("#companyName").val(msg.logistics_company_name);
					$("#receiver").val(msg.logistics_receiver);
					$("#receiverPhone").val(msg.logistics_receiver_phone);
					$("#receiverEmail").val(msg.logistics_receiver_email);
					$("#address").val(msg.logistics_address);
					$("#country").val(msg.logistics_country_id);
					if(msg.logistics_country_id==1){
						loadProv(url3,function(prov){
							var reg=/^(\s+)|(\s+)$/;
							var opts=$("#prov").find("option");
							for(var i=0,j=opts.size();i<j;i++){
								if((opts.eq(i).html()).replace(reg,'')==prov.replace(reg,'')){
									opts.eq(i).attr("selected",true);
									break;
								}
							}
							loadCity(url8,function(city){
								var opts2=$("#city").find("option");
								for(var i=0,j=opts2.size();i<j;i++){
									if((opts2.eq(i).html()).replace(reg,'')==city.replace(reg,'')){
										opts2.eq(i).attr("selected",true);
										break;
									}
								}
								flushCart();
							},msg.logistics_city);
						},msg.logistics_province);
					}else{
						switchAddr(url9,function(){
							$("#prov").val(msg.logistics_province);
							$("#city").val(msg.logistics_city);
							flushCart();
						},'');
					}
					switchDisplay();
				}
			}
		});
	});
	$(".cartList").on("click",".up,.down",function(){
		var countryId=$("#country").val();
		var fob_city=$("#fob_city").val();
		if(fob_city == 'shenzhen'){
			var sum_fob = 250.00;
		}else{
			var sum_fob = 450.00;
		}
		var dataDesc=$(this).attr("data-desc");
		var buyNum=$(this).siblings(".buyNum").eq(0).html();
		var specId=$(this).parent().attr("data-spec-id");
		var that=$(this);
		if(dataDesc=="up"){
			buyNum++;
			var errors=$(this).parents("ul").find(".shopping_tip").find(".error").size();
			// if(errors>0)return;
		}else{
			buyNum--;
		}
		if(buyNum<0)return;
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{specId:specId,buyNum:buyNum,countryId:countryId},
			success:function(res){
				if(res.code==10114){
					var info=res.info;
					that.siblings(".buyNum").eq(0).html(buyNum);
					var inventory=info.inventory;
					var orderTotalNum=info.orderTotalNum;
					var smallPrice=info.smallPrice;
					that.parents("ul").find(".orderBuyNum").html(orderTotalNum);
					that.parents("ul").find(".smallPrice").html(smallPrice);
					var country=$("#country").val();
					var fobmoney=$("#fobmoney").val();
					if(res.totalPrice>fobmoney){
						if(country==1){
							$(".extraLogisticsFee").hide();
							$("#totalPrice").siblings("dt").eq(0).html(rmbTotal);
						}else{
							$(".extraLogisticsFee").hide();
							$("#totalPrice").siblings("dt").eq(0).html(usdTotal);
						}
						$("#totalPrice").html(toThousands(res.totalPrice));
					}else{
						if(country==1){
							$(".extraLogisticsFee").show();
							$("#totalPrice").siblings("dt").eq(0).html(rmbTaxTotal);
							$("#totalPrice").html(toThousands(res.totalPrice));
						}else{
							$(".extraLogisticsFee").show();
							$("#totalPrice").siblings("dt").eq(0).html(usdTaxTotal);
							$("#totalPrice").html(toThousands((res.totalPrice-0+sum_fob).toFixed(2)));
						}
					}
					var spt=that.parents("ul").find(".shopping_tip");
					if(orderTotalNum>inventory){
						var html='<span class="error" style="color:red">*'+excessInventory+'</span>';
						spt.find(".error").remove().end().append(html);
					}else{
						spt.find(".error").remove();
					}
				}
			}
		});
	});
	$(".shopping_B").on("change","#fob_city",function(){
		var fob_city=$("#fob_city").val();
		var totalPrice=parseInt($("#totalPrice").text().replace(/,/g,''));
		var fob_num=parseInt($('#fob_num').text())
		if(fob_num&&fob_num == 250){
			if(fob_city == 'shenzhen'){
			}else{
				$('#fob_num').text(450)
				$("#totalPrice").text(toThousands((totalPrice+200.00).toFixed(2)));
			}
		}else if(fob_num&&fob_num == 450){
			if(fob_city == 'shenzhen'){
				$('#fob_num').text(250)
				$("#totalPrice").text(toThousands((totalPrice-200.00).toFixed(2)));
			}
		}
	});
	$(".cartList").on("click",".del",function(){
		var specId=$(this).attr("data-spec-id");
		var that=$(this);
		$.ajax({
			url:url2,
			type:"post",
			dataType:"json",
			data:{specId:specId},
			success:function(res){
				if(res.code==10115){
					flushCart();
					checkInput();
				}
			}
		});
	});
	$(".invoiceType").click(function(){
		var invoiceType=$(this).attr("data-type");
		$(this).attr("checked",true);
		$(".invoiceContent ol[data-type="+invoiceType+"]").show().siblings("ol").hide();
	});
	$(".uploadBoxOpenBtn").click(function(){
		$(".uploadFileBox").show();
	});
	$(".cancelBtn,.completeBtn").click(function(){
		$(".uploadFileBox").hide();
	});
	var idArray1=[
	"#PONumber","#remark","#companyName","#receiver","#receiverPhone","#receiverEmail",
	"#address","#invoiceName","#invoiceCredit","#fwdCompanyName","#fwdReceiver",
	"#fwdReceiverPhone","#fwdReceiverEmail","#fwdAddress"
	];
	$(idArray1.join(",")).bind("input propertychange",function(){
		checkInput();
	});
	var idArray2=[
	"#country","#fwdCountry","#fwdShipment"
	];
	$(idArray2.join(",")).change(function(){
		checkInput();
	});
	$(".invoiceType,.invoiceContent3,.invoiceContent2,#cbx").click(function(){
		checkInput();
	});
	checkInput();
	function toThousands(num) {
		var result = [], counter = 0;
		num = (num || 0).toString().split('');
		for (var i = num.length - 1; i >= 0; i--) {
			counter++;
			result.unshift(num[i]);
			if (!(counter % 3) && i != 0) {
				if(num[i]!="."){
					result.unshift(',');
				}
			}
		}
		return result.join('');
	}
	$(".saveBtn").click(function(){
		var isQualified=$(this).attr("data-qualified");
		if(isQualified=="no")return;
		var url=$(this).attr("data-url");
		productsData=checkInput();
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{productsData:productsData},
			success:function(res){
				var code=res.code;
				switch(code){
					case 10029:
						layer.msg(res.info);
						location.href=res.url;
						break;
					default:
						layer.msg(res.info);
				}
			}
		});
	});
});
function checkInput(){
	var flag=true;
	var reg=/\s+/;
	var reg2=/^(\s+)|(\s+)$/;
	var productsData={};

	var PONumber=$("#PONumber").val();
	var remark=$("#remark").val();
	var companyName=$("#companyName").val();
	var receiver=$("#receiver").val();
	var receiverPhone=$("#receiverPhone").val();
	var receiverEmail=$("#receiverEmail").val();
	var address=$("#address").val();
	var country=$("#country").val();

	PONumber=PONumber.replace(reg,'');
	remark=remark.replace(reg2,'');
	companyName=companyName.replace(reg2,'');
	receiver=receiver.replace(reg2,'');
	receiverPhone=receiverPhone.replace(reg,'');
	receiverEmail=receiverEmail.replace(reg,'');
	address=address.replace(reg2,'');

	$("#PONumber").val(PONumber);
	$("#remark").val(remark);
	$("#companyName").val(companyName);
	$("#receiver").val(receiver);
	$("#receiverPhone").val(receiverPhone);
	$("#receiverEmail").val(receiverEmail);
	$("#address").val(address);

	if(!PONumber){
		flag=false;
	}else{
		productsData.orderInfo={PONumber:PONumber,remark:remark};//订单信息
	}
	if(!companyName||!receiver||!receiverPhone||!address){
		flag=false;
	}else{
		productsData.logisticsInfo={//物流信息
			companyName:companyName,
			receiver:receiver,
			receiverPhone:receiverPhone,
			receiverEmail:receiverEmail,
			country:country,
			address:address
		};
		productsData.logisticsInfo.city=$("#city").val();
		productsData.logisticsInfo.prov=$("#prov").val();
	}


	if((country==1)){//检查开票信息
		var invoiceControl=$("#invoiceControl").attr("data-control");
		if(invoiceControl==1){
			var invoiceType=$(".invoiceType:checked").attr("data-type");
			if(invoiceType==1){
				var invoiceName=$("#invoiceName").val();
				var invoiceContent2=$(".invoiceContent2:checked").attr("data-type");
				invoiceName=invoiceName.replace(reg,'');
				$("#invoiceName").val(invoiceName);

				if(!invoiceName){
					flag=false;
				}else{
					productsData.invoiceInfo={//开票信息
						invoiceType:invoiceType,
						invoiceContent:invoiceContent2,
						invoiceName:invoiceName,
						invoiceCredit:'',
						invoiceAddon:''
					};
				}
			}else{
				var invoiceCredit=$("#invoiceCredit").val();
				var invoiceContent3=$(".invoiceContent3:checked").attr("data-type");
				invoiceCredit=invoiceCredit.replace(reg,'');
				$("#invoiceCredit").val(invoiceCredit);

				var size=$(".fileList").find("a").size();
				if(size<3){
					flag=false;
				}
				if(!invoiceCredit){
					flag=false;
				}else{
					productsData.invoiceInfo={//开票信息
						invoiceType:invoiceType,
						invoiceContent:invoiceContent3,
						invoiceCredit:invoiceCredit,
						invoiceAddon:{//附件
							taxes:$(".fileList").find("a[data-category=taxes]").eq(0).attr("data-pic"),//一般纳税人证明
							taxesN:$(".fileList").find("a[data-category=taxes]").eq(0).attr("data-name"),//一般纳税人证明
							license:$(".fileList").find("a[data-category=license]").eq(0).attr("data-pic"),//营业执照
							licenseN:$(".fileList").find("a[data-category=license]").eq(0).attr("data-name"),//营业执照
							invoice:$(".fileList").find("a[data-category=invoice]").eq(0).attr("data-pic"),//开票资料
							invoiceN:$(".fileList").find("a[data-category=invoice]").eq(0).attr("data-name")//开票资料
						},
						invoiceName:''
					};
				}
			}
		}

	}else{//检查货代信息
		var fwdCompanyName=$("#fwdCompanyName").val();
		var fwdReceiver=$("#fwdReceiver").val();
		var fwdReceiverPhone=$("#fwdReceiverPhone").val();
		var fwdReceiverEmail=$("#fwdReceiverEmail").val();
		var fwdAddress=$("#fwdAddress").val();

		fwdCompanyName=fwdCompanyName.replace(reg,'');
		fwdReceiver=fwdReceiver.replace(reg,'');
		fwdReceiverPhone=fwdReceiverPhone.replace(reg,'');
		fwdReceiverEmail=fwdReceiverEmail.replace(reg,'');
		fwdAddress=fwdAddress.replace(reg,'');

		$("#fwdCompanyName").val(fwdCompanyName);
		$("#fwdReceiver").val(fwdReceiver);
		$("#fwdReceiverPhone").val(fwdReceiverPhone);
		$("#fwdReceiverEmail").val(fwdReceiverEmail);
		$("#fwdAddress").val(fwdAddress);

		if(!fwdCompanyName||!fwdReceiver||!fwdReceiverPhone||!fwdAddress){
			flag=false;
		}else{
			productsData.forworderInfo={//货代信息
				fwdCompanyName:fwdCompanyName,
				fwdReceiver:fwdReceiver,
				fwdReceiverPhone:fwdReceiverPhone,
				fwdReceiverEmail:fwdReceiverEmail,
				fwdAddress:fwdAddress,
				fwdCountry:$("#fwdCountry").val(),
				fwdShipment:$("#fwdShipment").val()
			};
			productsData.forworderInfo.fwdCity=$("#fwdCity").val();
			productsData.forworderInfo.fwdProv=$("#fwdProv").val();
		}
	}

	var cartSize=$("#cartList").find(".cartPItem").size();
	if(cartSize<=0){
		flag=false;
	}

	if(flag===true){
		if($("#cbx").attr("checked")=="checked"){
			$(".saveBtn").removeClass("invalid").attr("data-qualified","yes");
		}else{
			$(".saveBtn").addClass("invalid").attr("data-qualified","no");
		}
		return productsData;
	}else{
		$(".saveBtn").addClass("invalid").attr("data-qualified","no");
	}
}
layui.use('upload', function(){
	var $ = layui.jquery,upload = layui.upload;
	var uploadInst = upload.render({
			elem: '.uploadBtn',
			url: url4,
			accept:"file",
			exts:'pdf',
			size:5*1024,
			before: function(obj){
				flag=false;
				percent=Math.floor(15+Math.random()*57);
				msgTip(percent);
				this.url=url4
				var cat=this.item.attr("data-category");
				this.url=this.url.replace("_xxxxx_",cat);
			},
			done: function(res){

				//return layer.msg('上传成功');
				clearTimeout(t);
				msgTip(100,function(res,that){
					if(res.code >0){
						if(res.code==2){
							var html="";
							if(that.item.attr("data-category")=="taxes"){
								html='<dl><dd>'+
									'一般纳税人证明（带公章）</dd><dd>不明格式</dd>'+
								'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
							}
							if(that.item.attr("data-category")=="license"){
								html='<dl><dd>'+
									'营业执照（带公章）</dd><dd>不明格式</dd>'+
								'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
							}
							if(that.item.attr("data-category")=="invoice"){
								html='<dl><dd>'+
									'开票资料(带公章)<br/>（开票名称/银行信息）</dd><dd>不明格式</dd>'+
								'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
							}
							that.item.removeClass("upload_04").addClass("upload_03").html(html);
							return layer.msg(res.msg);
						}
						if(res.code==3){
							var html="";
							if(that.item.attr("data-category")=="taxes"){
								html='<dl><dd>'+
									'一般纳税人证明（带公章）</dd><dd>档案太大</dd>'+
								'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
							}
							if(that.item.attr("data-category")=="license"){
								html='<dl><dd>'+
									'营业执照（带公章）</dd><dd>档案太大</dd>'+
								'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
							}
							if(that.item.attr("data-category")=="invoice"){
								html='<dl><dd>'+
									'开票资料(带公章)<br/>（开票名称/银行信息）</dd><dd>档案太大</dd>'+
								'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
							}
							that.item.removeClass("upload_04").addClass("upload_02").html(html);
							return layer.msg(res.msg);
						}
						//return layer.msg('上传失败');
					}
					var html="";
					var picPath=res.picPath;
					var name=res.fileName;
					if(that.item.attr("data-category")=="taxes"){
						html='<span class="uploadSuccess" data-category="taxes"  data-pic="'+picPath+'">一般纳税人证明（带公章）<br/>已上传</span>';
						$(".fileList").find("a[data-category=taxes]").eq(0).remove();
						$(".fileList").append("<a target='_blank' data-category='taxes' data-pic='"+picPath+"' data-name='"+name+"' href='"+publicPath+picPath+"'>"+name+"</a>");
					}
					if(that.item.attr("data-category")=="license"){
						html='<span class="uploadSuccess" data-category="license" data-pic="'+picPath+'">营业执照（带公章）<br/>已上传</span>';
						$(".fileList").find("a[data-category=license]").eq(0).remove();
						$(".fileList").append("<a target='_blank' data-category='license' data-pic='"+picPath+"' data-name='"+name+"' href='"+publicPath+picPath+"'>"+name+"</a>");
					}
					if(that.item.attr("data-category")=="invoice"){
						html='<span class="uploadSuccess" data-category="invoice" data-pic="'+picPath+'">开票资料(带公章)<br/>（开票名称/银行信息）<br/>已上传</span>';
						$(".fileList").find("a[data-category=invoice]").eq(0).remove();
						$(".fileList").append("<a target='_blank' data-category='invoice' data-pic='"+picPath+"' data-name='"+name+"' href='"+publicPath+picPath+"'>"+name+"</a>");
					}
					that.item.removeClass("upload_04").addClass("upload_01").html(html);
					checkInput();
				},res,this);
			},
			error: function(){

			}
	});
});
var t,flag=false;
function msgTip(percent,fn,arg1,arg2){
	$("#msgTip2").show();
	$("#screen").show();
	var h=$(window).height();
	var w=$(window).width();
	var selfW=$("#msgTip2").width();
	$("#msgTip2").css({"top":(h/2-25)+"px","left":(w-selfW)/2+"px"});
	animate(percent,fn,arg1,arg2);
}
function animate(percent,fn,arg1,arg2){
	percents=$("#progressTip").html()-0;
	if(percents<percent){
		percents++;
		if(percents>=100){
			percents=100;
			flag=true
		}
		$("#progressTip").html(percents);
		$("#progressNum").css("width",(percents*2)+5+"px");

		t=setTimeout(function(){
			animate(percent,fn,arg1,arg2);
		},30);
	}else{
		clearTimeout(t);
		if(flag){
			$("#msgTip2").fadeOut(2000,"swing",function(){
				$("#screen").hide();
				$("#progressTip").html(0);
				$("#progressNum").css({"width":5+"px"});
				if(typeof fn==="function"){
					fn(arg1,arg2);
				}
			});
		}
	}
}
