$(function(){
	layui.use("layer");
	autoCheck();
	$("#company,#receiver,#phone,#email,#city,#prov,#address").bind('input propertychange',function(e){
		autoCheck();
	});
	function autoCheck(){
		var reg=/^(\s+)|(\s+)$/;
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
		var country=$("#country").val();
		var city=$("#city").val();
		var prov=$("#prov").val();
		var address=$("#address").val();
		var url=$(this).attr("data-url");
		var data_logistics=$(this).attr("data-logistics");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{
				data_logistics:data_logistics,
				company:company,
				receiver:receiver,
				phone:phone,
				email:email,
				country:country,
				city:city,
				prov:prov,
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
				$("#info_nav").html(editLogistics);
				$("#company").val(info.logistics_company_name);
				$("#receiver").val(info.logistics_receiver);
				$("#phone").val(info.logistics_receiver_phone);
				$("#email").val(info.logistics_receiver_email);
				$("#country").val(info.logistics_country_id);
				if(info.logistics_country_id==1){
					loadProv(url,function(prov){
						var reg=/^(\s+)|(\s+)$/;
						var opts=$("#prov").find("option");
						for(var i=0,j=opts.size();i<j;i++){
							if((opts.eq(i).html()).replace(reg,'')==prov.replace(reg,'')){
								opts.eq(i).attr("selected",true);
								break;
							}
						}
						loadCity(url2,function(city){
							var opts2=$("#city").find("option");
							for(var i=0,j=opts2.size();i<j;i++){
								if((opts2.eq(i).html()).replace(reg,'')==city.replace(reg,'')){
									opts2.eq(i).attr("selected",true);
									break;
								}
							}
						},info.logistics_city);
					},info.logistics_province);
				}else{
					switchAddr(url3,function(){
						$("#prov").val(info.logistics_province);
						$("#city").val(info.logistics_city);
					},'');
				}
				$("#address").val(info.logistics_address);
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
		$("#prov").val('');
		$("#city").val('');
		$("#address").val('');
	}
});

