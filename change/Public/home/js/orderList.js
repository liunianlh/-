$(function(){
	layui.use("layer");
	$(".searchBtn").click(function(){
		var where=wrapCondition();
		dataDeal(url,where);
	});
	$(".dataValue").click(function(){
		if($(this).hasClass("active")){
			$(this).removeClass("active");
		}else{
			$(".dataValue").removeClass("active");
			$(this).addClass("active");
			var where=wrapCondition();
			dataDeal(url,where);
		}
	});
	$("#page").on("click","a.next,a.prev,a.num",function(e){
		e.preventDefault();
		var curPageLink=$(this).attr("href");
		$("#page").attr("data-cur-page",curPageLink);
		where=wrapCondition();
		dataDeal(curPageLink,where);
	});
});
function dataDeal(url,where){
	$.ajax({
		url:url,
		type:"post",
		dataType:"json",
		data:{where:where},
		success:function(res){
			if(res.code=="10102"){
				var orderInfo=res.msg;
				var page=res.page;
				var uls='';
				for(var i=0,j=orderInfo.length;i<j;i++){
					var temp=orderInfo[i];
					var ul="<ul class='orderItem'>";
					switch(temp.order_status){
						case "1":
							ul+='<li> '+waiting+' </li>';
							break;
						case "2":
							ul+='<li> '+confired+' </li>';
							break;
						case "3":
							ul+='<li> '+completed+' </li>';
							break;
						case "4":
							ul+='<li> '+canceled+' </li>';
							break;
						case "5":
							ul+='<li> '+transit+' </li>';
							break;
						case "6":
							ul+='<li> '+waitConfired+' </li>';
							break;
						default:
							ul+='<li> -- </li>';
							break;
					}
					ul+='<li> '+temp.order_ponumber+' </li>';
					ul+='<li style="width:18%;"> '+temp.order_serial_number+' </li>';
					ul+='<li> '+temp.order_time+' </li>';
					ul+='<li style="width:12%;"> '+toThousands(temp.order_total_price)+' </li>';
					ul+='<li style="width:12%;">'+temp.order_currency+'</li>';
					ul+='<li><a href="'+temp.url+'"><span>'+view+'</span></a> <a href="'+temp.url2+'"><span>'+download+'</span></a></li>';
					ul+="</ul>";
					uls+=ul;
				}
				$(".orderList").find(".orderItem").remove();
				$(".orderPage").before(uls);
				$("#page").html(page);
			}
		}
	});
}
function wrapCondition(){
	var start=$("#start").val();
	var end=$("#end").val();
	var orderStatus=$("#orderStatus").val();
	var orderCurrency=$("#orderCurrency").val();
	var PONumber=$("#PONumber").val();
	var dataValue=$(".order_table01 .active").eq(0).attr("data-value");
	var where={
		start:start,
		end:end,
		orderStatus:orderStatus,
		orderCurrency:orderCurrency,
		PONumber:PONumber,
		dataValue:dataValue
	};
	return where;
}
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