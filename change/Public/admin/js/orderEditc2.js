$(function(){
	$("#logCountry").change(function(){
		getPC();
	});
	
	$("#PCA").on("change","select#prov",function(){
		getC();
	});
	getPC(function(){
		var countryId=$("#logCountry").val();
		var originalP=$("#logCountry").attr("data-ori-prov");
		var originalC=$("#logCountry").attr("data-ori-city");
		
		var reg=/^(\s+)|(\s+)$/;
		if(countryId==1){
			var opts=$("#prov").find("option");
			for(var i=0,j=opts.size();i<j;i++){
				var curO=opts.eq(i);
				var curP=curO.html();
				if(curP.replace(reg,'')==originalP.replace(reg,'')){
					curO.attr("selected",true);
				}
			}
			
			getC(function(originalC){
				var opts2=$("#city").find("option");
				for(var m=0,n=opts2.size();m<n;m++){
					var curO=opts2.eq(m);
					var curP=curO.html();
					if(curP.replace(reg,'')==originalC.replace(reg,'')){
						curO.attr("selected",true);
					}
				}
			},originalC);
		}else{
			$("#prov").val(originalP);
			$("#city").val(originalC);
		}
	});
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