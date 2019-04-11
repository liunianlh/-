$(function(){
	layui.use("layer");
	loadProv(url3,loadCity,url8);
	switchDisplay();
	
	$("#l_rr1").on("change","select#prov",function(){
		loadCity(url8);
	});
	$("#country").change(function(){
		switchAddr(url9);
		switchDisplay();
		flushCart();
	});
});
function loadProv(url,fn,addon){
	$.ajax({
		url:url,
		type:"post",
		dataType:"json",
		data:{},
		success:function(res){
			if(res.code=="10090"){
				$("#l_rr1").find("#prov").remove();
				$("#l_rr1").find("#city").remove();
				$("#l_rr1").append(res.msg);
				if(typeof fn=="function"){
					fn(addon);
				}
			}
		}
	});
}
function loadCity(url,fn,addon){
	var areaId=$("#l_rr1").find("#prov").val();
	$.ajax({
		url:url,
		type:"post",
		dataType:"json",
		data:{areaId:areaId},
		success:function(res){
			if(res.code=="10090"){
				$("#l_rr1").find("#city").remove();
				$("#l_rr1").append(res.msg);
				if(typeof fn=="function"){
					fn(addon);
				}
			}
		}
	});
}
function switchAddr(url,fn,addon){
	var countryId=$("#country").val();
	$.ajax({
		url:url,
		type:"post",
		dataType:"json",
		data:{countryId:countryId},
		success:function(res){
			if(res.code=="10090"){
				$("#l_rr1").find("#prov").remove();
				$("#l_rr1").find("#city").remove();
				$("#l_rr1").append(res.msg);
				if(typeof fn=="function"){
					fn(addon);
				}
			}
		}
	});
}
function switchDisplay(){
	var country=$("#country").val();
	var invoiceControl=$("#invoiceControl").attr("data-control");
	if((country==1)){
		if(invoiceControl==1){
			$(".invioceBoxModule").show();
			$("#invoiceControl").show();
			$("#switchInvoiceContent").show();
			$(".forworderBoxModule").hide();
		}else{
			$(".invioceBoxModule").hide();
			$("#invoiceControl").show();
			$("#switchInvoiceContent").hide();
			$(".forworderBoxModule").hide();
		}
	}else{
		$(".invioceBoxModule").hide();
		$(".forworderBoxModule").show();
	}
}
function flushCart(){
	var countryId=$("#country").val();
	var fob_city=$("#fob_city").val();
		
	$.ajax({
		url:url13,
		type:"post",
		dataType:"json",
		data:{countryId:countryId,fob_city:fob_city},
		success:function(res){
			if(res.code=="10090"){
				$("#cartList").html(res.msg);
			}
		}
	});
}

