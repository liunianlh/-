$(function(){
	$("#fwdCountry").change(function(){
		getPC2();
	});
	
	$("#fwdPCA").on("change","select#fwdProv",function(){
		getC2();
	});
	getPC2(function(){
		var countryId=$("#fwdCountry").val();
		var originalP=$("#fwdCountry").attr("data-ori-prov");
		var originalC=$("#fwdCountry").attr("data-ori-city");
		
		var reg=/^(\s+)|(\s+)$/;
		if(countryId==1){
			var opts=$("#fwdProv").find("option");
			for(var i=0,j=opts.size();i<j;i++){
				var curO=opts.eq(i);
				var curP=curO.html();
				if(curP.replace(reg,'')==originalP.replace(reg,'')){
					curO.attr("selected",true);
				}
			}
			
			getC2(function(originalC){
				var opts2=$("#fwdCity").find("option");
				for(var m=0,n=opts2.size();m<n;m++){
					var curO=opts2.eq(m);
					var curP=curO.html();
					if(curP.replace(reg,'')==originalC.replace(reg,'')){
						curO.attr("selected",true);
					}
				}
			},originalC);
		}else{
			$("#fwdProv").val(originalP);
			$("#fwdCity").val(originalC);
		}
	});
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