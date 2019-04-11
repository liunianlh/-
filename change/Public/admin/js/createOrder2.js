$(function(){
	$("#logCountry").change(function(){
		getPC();
	});
	
	$("#PCA").on("change","select#prov",function(){
		getC();
	});
	getPC();
});
function getPC(fn){
	var countryId=$("#logCountry").val();
	$.ajax({
		url:getPCUrl,
		type:"post",
		dataType:"json",
		data:{countryId:countryId},
		success:function(res){
			$("#PCA").html(res.msg);
			if(typeof fn=="function"){
				fn();
			}
		}
	});
}
function getC(fn,info){
	var areaId=$("#prov").val();
	$.ajax({
		url:getCUrl,
		type:"post",
		dataType:"json",
		data:{areaId:areaId},
		success:function(res){
			$("#city").remove();
			$("#PCA").append(res.msg);
			if(typeof fn=="function"){
				fn(info);
			}
		}
	});
}