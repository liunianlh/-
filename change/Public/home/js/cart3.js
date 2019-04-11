$(function(){
	layui.use("layer");
	loadProv2(url12,loadCity2,url11);
	
	$("#l_rr2").on("change","select#fwdProv",function(){
		loadCity2(url11);
	});
	$("#fwdCountry").change(function(){
		switchAddr2(url10);
	});
});
function loadProv2(url,fn,addon){
	$.ajax({
		url:url,
		type:"post",
		dataType:"json",
		data:{},
		success:function(res){
			if(res.code=="10090"){
				$("#l_rr2").find("#fwdProv").remove();
				$("#l_rr2").find("#fwdCity").remove();
				$("#l_rr2").append(res.msg);
				if(typeof fn=="function"){
					fn(addon);
				}
			}
		}
	});
}
function loadCity2(url,fn,addon){
	var areaId=$("#l_rr2").find("#fwdProv").val();
	$.ajax({
		url:url,
		type:"post",
		dataType:"json",
		data:{areaId:areaId},
		success:function(res){
			if(res.code=="10090"){
				$("#l_rr2").find("#fwdCity").remove();
				$("#l_rr2").append(res.msg);
				if(typeof fn=="function"){
					fn(addon);
				}
			}
		}
	});
}
function switchAddr2(url,fn,addon){
	var countryId=$("#fwdCountry").val();
	$.ajax({
		url:url,
		type:"post",
		dataType:"json",
		data:{countryId:countryId},
		success:function(res){
			if(res.code=="10090"){
				$("#l_rr2").find("#fwdProv").remove();
				$("#l_rr2").find("#fwdCity").remove();
				$("#l_rr2").append(res.msg);
				if(typeof fn=="function"){
					fn(addon);
				}
			}
		}
	});
}

