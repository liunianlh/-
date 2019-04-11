$(function(){
	layui.use("layer");
	autoCheck();
	$("#society").bind('input propertychange',function(e){
		autoCheck();
	});
	function autoCheck(){
		var invoice_type=$("input.invoice_type:checked").val();
		if(invoice_type==1){
			$("#name").text(invoiceContent);
			$(".uploadContent").hide();
		}
		if(invoice_type==2){
			$("#name").text(creditCode);
			$(".uploadContent").show();
		}
		var reg=/^(\s+)|(\s+)/;
		var society=$("#society").val();
		society=society.replace(reg,'');
		$("#society").val(society);
		if(society){
			$("#saveBtn").removeClass("disabled").attr("data-qualified","yes");
		}else{
			$("#saveBtn").addClass("disabled").attr("data-qualified","no");
		}
	}
	$(".invoice_type").click(function(){
		$("#society").val('');
		autoCheck();
	});
	$("#saveBtn").click(function(){
		var isQualified=$(this).attr("data-qualified");
		if(isQualified=="no")return;
		var dataInvoice=$(this).attr("data-invoice");
		var society=$("#society").val();
		var invoice_type=$("input.invoice_type:checked").val();
		var invoice_content=$("input.invoice_content:checked").val();
		var picData='';
		if(invoice_type==2){
			var taxes=$(".addBtn[data-type=taxes]").attr("pic-data");
			var dataAddon={};
			if(!taxes){
				return layer.msg("请上传一般纳税人证明");
			}else{
				var objTaxes=JSON.parse(taxes);
				dataAddon.taxes=objTaxes;
			}
			var license=$(".addBtn[data-type=license]").attr("pic-data");
			if(!license){
				return layer.msg("请上传营业执照");
			}else{
				var objLicense=JSON.parse(license);
				dataAddon.license=objLicense;
			}
			var invoice=$(".addBtn[data-type=invoice]").attr("pic-data");
			if(!invoice){
				return layer.msg("请上传开票资料");
			}else{
				var objInvoice=JSON.parse(invoice);
				dataAddon.invoice=objInvoice;
			}
			picData=JSON.stringify(dataAddon);
			picData=picData||'';
		}
		var url=$(this).attr("data-url");
		$.ajax({
			url:url3,
			type:"post",
			dataType:"json",
			data:{data_invoice:dataInvoice,society:society,invoice_type:invoice_type,invoice_content:invoice_content,pic_data:picData},
			success:function(res){
				var code=res.code;
				if(code==4){
					layer.msg(res.msg);
					$("#society").val('');
					location.reload();
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	$(document).on("click",".remBtn",function(){
		// var index=$(this).index(".remBtn");
		// var picData=$("#addBtn").attr("pic-data");
		// var picPath=picData&&JSON.parse(picData);
		// if(!picPath)return;
		// var picPaths=picPath.picPath||false;
		// if(!picPaths)return;
		// var currentPath=picPaths[index];
		// if(!currentPath)return;
		// var that=$(this);
		// $.ajax({
			// url:url2,
			// type:"post",
			// dataType:"json",
			// data:{data:currentPath.path},
			// success:function(res){
				// if(res.code==0){
					// picPaths.splice(index,1);
					// var temp={"picPath":picPaths};
					// $("#addBtn").attr("pic-data",JSON.stringify(temp));
					// that.prev("a").remove();
					// that.prev("span").remove();
					// that.next("br").remove();
					// that.remove();
				// }
				// layer.msg(res.msg);
			// }
		// });
		$(this).siblings("a.uploadItem").eq(0).remove();
		$(this).siblings("div.addBtn").eq(0).attr('pic-data','');
		$(this).remove();
	});
	$(document).on("click",".editA",function(){
		var url4=$(this).attr("data-url");
		var key=$(this).attr("check-key");
		$.ajax({
			url:url4,
			type:"post",
			dataType:"json",
			data:{key:key},
			success:function(res){
				var info=res.msg;
				var invoice_type=info.invoice_type_id;
				var society='';
				$("#info_nav").html(editInvoice);
				if(invoice_type==2){
					$(".uploadContent").find(".uploadItem").remove();
					$("#company").click();
					society=info.invoice_credit;
					var arr=info.addon;
					if(arr){
						var remHtml='<div class="remBtn uploadItem">-</div>';
					
						var taxes='<a class="uploadItem" href="'+publicPath+arr.taxes.path+'">'+arr.taxes.name+'</a>';
						$(".uploadContent").find(".addBtn[data-type=taxes]").before(taxes);
						$(".uploadContent").find(".addBtn[data-type=taxes]").after(remHtml);
						$(".uploadContent").find(".addBtn[data-type=taxes]").attr("pic-data",JSON.stringify(arr.taxes));
						
						var license='<a class="uploadItem" href="'+publicPath+arr.license.path+'">'+arr.license.name+'</a>';
						$(".uploadContent").find(".addBtn[data-type=license]").before(license);
						$(".uploadContent").find(".addBtn[data-type=license]").after(remHtml);
						$(".uploadContent").find(".addBtn[data-type=license]").attr("pic-data",JSON.stringify(arr.license));
						
						var invoice='<a class="uploadItem" href="'+publicPath+arr.invoice.path+'">'+arr.invoice.name+'</a>';
						$(".uploadContent").find(".addBtn[data-type=invoice]").before(invoice);
						$(".uploadContent").find(".addBtn[data-type=invoice]").after(remHtml);
						$(".uploadContent").find(".addBtn[data-type=invoice]").attr("pic-data",JSON.stringify(arr.invoice));
					}
					
				}else{
					$("#personal").click();
					$("#addon").html("");
					society=info.invoice_name;
				}
				$("#society").val(society);
				var invoice_content=info.invoice_content_id;
				if(invoice_content==2){
					$("#detail").click();
				}else{
					$("#big").click();
				}
				$("#saveBtn").attr("data-invoice",info.invoice_id);
				autoCheck();
			}
		});
	});
	$(document).on("click",".delA",function(){
		var url4=$(this).attr("data-url");
		var key=$(this).attr("check-key");
		$.ajax({
			url:url4,
			type:"post",
			dataType:"json",
			data:{key:key},
			success:function(res){
				var code=res.code;
				if(code==4){
					layer.msg(res.msg);
					location.reload();
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
});
layui.use('upload', function(){
  var $ = layui.jquery,upload = layui.upload;
  var url=$('.addBtn').eq(0).attr("data-url");
  var uploadInst = upload.render({
	elem: '.addBtn',
	url: url,
	accept:"file",
	exts:"pdf",
	size:5*1024,
	before: function(obj){
		flag=false;
		percent=Math.floor(15+Math.random()*57);
		msgTip(percent);
	},
	done: function(res){
		if(res.code > 0){
			return layer.msg(res.msg);
		}
		this.item.parent().find(".uploadItem").remove();
		this.item.before('<a class="uploadItem" href="'+publicPath+res.picPath+'">'+res.fileName+'</a>');
		this.item.after('<div class="remBtn uploadItem">-</div>');
		var dataType=this.item.attr("data-type");
		var durObj={"path":res.picPath,"name":res.fileName};
		this.item.attr("pic-data",JSON.stringify(durObj));
		clearTimeout(t);
		msgTip(100);
		//return layer.msg(res.msg);
	},
	error: function(){
		this.msg("请上传pdf文件");
	}
  });
});
var t,flag=false;
function msgTip(percent){
	$("#msgTip2").show();
	$("#screen").show();
	var h=$(window).height();
	var w=$(window).width();
	var selfW=$("#msgTip2").width();
	$("#msgTip2").css({"top":(h/2-25)+"px","left":(w-selfW)/2+"px"});
	animate(percent);
}
function animate(percent){
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
			animate(percent);
		},30);
	}else{
		clearTimeout(t);
		if(flag){
			$("#msgTip2").fadeOut(2000,"swing",function(){
				$("#screen").hide();
				$("#progressTip").html(0);
				$("#progressNum").css({"width":5+"px"});
			});
		}
	}
}

