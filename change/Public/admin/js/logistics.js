$(function(){
	layui.use("layer");
	updateDist();
	function updateDist(fn){
		var curCity=$("#city").val();
		$.ajax({
			url:url3,
			type:"post",
			dataType:"json",
			data:{city_id:curCity},
			success:function(res){
				var info=res.msg;
				var opt="";
				for(var i=0,j=info.length;i<j;i++){
					opt+='<option value="'+info[i].area_id+'">'+info[i].area_name+'</option>';
				}
				$("#dist").html(opt);
				if(typeof fn=="function"){
					fn();
				}
			}
		});
	}
	$("#city").change(function(){
		updateDist();
	});
	autoCheck();
	$("#company,#receiver,#phone,#email,#city,#dist,#address").bind('input propertychange',function(e){
		autoCheck();
	});
	function autoCheck(){
		var reg=/\s+/;
		var company=$("#company").val();
		company=company.replace(reg,'');
		$("#company").val(company);
		var receiver=$("#receiver").val();
		receiver=receiver.replace(reg,'');
		$("#receiver").val(receiver);
		var phone=$("#phone").val();
		phone=phone.replace(reg,'');
		$("#phone").val(phone);
		var email=$("#email").val();
		email=email.replace(reg,'');
		$("#email").val(email);
		var address=$("#address").val();
		address=address.replace(reg,'');
		$("#address").val(address);
		if(company&&receiver&&phone&&email&&address){
			$("#saveBtn").removeClass("disabled").attr("data-qualified","yes");
		}else{
			$("#saveBtn").addClass("disabled").attr("data-qualified","no");
		}
	}
	$("#saveBtn").click(function(){
		var isQualified=$(this).attr("data-qualified");
		if(isQualified=="no")return;
		var company=$("#company").val();
		var receiver=$("#receiver").val();
		var phone=$("#phone").val();
		var email=$("#email").val();
		var city=$("#city").val();
		var dist=$("#dist").val();
		var address=$("#address").val();
		var url=$(this).attr("data-url");
		var data_forworder=$(this).attr("data-forworder");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{
				data_forworder:data_forworder,
				company:company,
				receiver:receiver,
				phone:phone,
				email:email,
				city:city,
				dist:dist,
				address:address
			},
			success:function(res){
				var code=res.code;
				if(code==10077||code==10080){
					layer.msg(res.msg);
					clearInput();
					location.reload();
				}else{
					layer.msg(res.msg);
				}
			}
		});
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
				company=$("#company").val(info.logistics_company_name);
				receiver=$("#receiver").val(info.logistics_receiver);
				phone=$("#phone").val(info.logistics_receiver_phone);
				email=$("#email").val(info.logistics_receiver_email);
				var opts=$("#city").find("option");
				for(var i=0,j=opts.size();i<j;i++){
					if((opts.eq(i).html()).replace(/^\s+||\s+$/,'')==info.logistics_city.replace(/^\s+||\s+$/,'')){
						opts.eq(i).attr("selected",true);
					}
				}
				updateDist(function(){
					var opts2=$("#dist").find("option");
					for(var i=0,j=opts2.size();i<j;i++){
						if((opts2.eq(i).html()).replace(/^\s+||\s+$/,'')==info.logistics_dist.replace(/^\s+||\s+$/,'')){
							opts2.eq(i).attr("selected",true);
						}
					}
				});
				address=$("#address").val(info.logistics_address);
				$("#saveBtn").attr("data-logistics",info.logistics_id);
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
				if(code==10083){
					layer.msg(res.msg);
					location.reload();
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	function clearInput(){
		$("#company").val('');
		$("#receiver").val('');
		$("#phone").val('');
		$("#email").val('');
		$("#address").val();
		city=$("#city").find("option").eq(0).attr("selected",true);
	}
});

