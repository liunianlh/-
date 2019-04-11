$(function(){
	$("#fwdCountry").change(function(){
		getPC2();
	});
	
	$("#fwdPCA").on("change","select#fwdProv",function(){
		getC2();
	});
	getPC2();
});
function getPC2(fn){
	var countryId=$("#fwdCountry").val();
	$.ajax({
		url:getPCUrl2,
		type:"post",
		dataType:"json",
		data:{countryId:countryId},
		success:function(res){
			$("#fwdPCA").html(res.msg);
			if(typeof fn=="function"){
				fn();
			}
		}
	});
}
function getC2(fn,info){
	var areaId=$("#fwdProv").val();
	$.ajax({
		url:getCUrl2,
		type:"post",
		dataType:"json",
		data:{areaId:areaId},
		success:function(res){
			$("#fwdCity").remove();
			$("#fwdPCA").append(res.msg);
			if(typeof fn=="function"){
				fn(info);
			}
		}
	});
}