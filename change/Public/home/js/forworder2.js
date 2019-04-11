$(function(){
	loadProv(url,loadCity,url2);
	$("#l_rr1").on("change","select#prov",function(){
		loadCity(url2);
	});
	$("#country").change(function(){
		switchAddr(url3);
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
