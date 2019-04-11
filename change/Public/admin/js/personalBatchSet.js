$(function(){
	layui.use("layer");
	$("#saveBtn").click(function(){
		var rmbItem=$(".personalBatchSetList").find(".rmbItem");
		var pbL=[];
		for(var i=0,j=rmbItem.size();i<j;i++){
			var temp={};
			temp.hash=rmbItem.eq(i).attr("data-hash");
			temp.rmb=rmbItem.eq(i).find(".rmb").eq(0).val();
			temp.status=rmbItem.eq(i).find(".status").eq(0).val();
			pbL.push(temp);
		}
		var url=$(this).attr("data-url");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{gps:pbL},
			success:function(res){
				layer.msg(res.msg);
				var searchModel=$("#searchModel").val();
				if(searchModel.replace(/^(\s+)|(\s+)$/,'')!=''){
					$("#searchBtn").click();
				}else{
					location.reload();
				}
			}
		});
	});
	
	$("#searchBtn").click(function(){
		var url=location.href;//$(this).attr("data-url");
		var searchModel=$("#searchModel").val();
		searchModel=searchModel.replace(/^\s+$/,"");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{searchModel:searchModel},
			success:function(res){
				var info=res.msg.spec_info;
				var uls='';
				for(var sItem in info){
					var ul="<ul class='personalBatchSetItem'>";
					var dd1='';
					var dd2='';
					var dd3='';
					var dd4='';
					var tempArr=info[sItem];
					for(var i=0,j=tempArr.length;i<j;i++){
						var temp=tempArr[i];
						if(i==0){
							dd1+='<dd><input class="serialIdCBX" data-serial="serial" data-serial-id="serial'+temp.serial_id+'" type="checkbox"><text>'+temp.serial_name+'</text><div class="line"></div></dd>';
						}else{
							dd1+='<dd><text>&emsp;</text> <div class="line02"></div></dd>';
						}
						dd2+='<dd><input class="modelIdCBX" data-serial="serial" data-model="model" data-serial-id="serial'+temp.serial_id+'" data-model-id="model'+temp.m5_model+'" type="checkbox"><text>'+temp.model_name+'</text><div class="line"></div></dd>';
						dd3+='<dd class="rmbItem" data-hash="'+temp._hashKey+'"><input class="rusCBX" data-serial="serial" data-model="model" data-rmb="rmb" data-serial-id="serial'+temp.serial_id+'" data-model-id="model'+temp.m5_model+'" type="checkbox"> <span>' +
							'<input class="rmb" oninput="changes(this)"   value="'+toFixed(temp.rmb)+'" type="text"></span>' +
							' <span><input oninput="change_start(this)"  value="'+RMB2USD(temp.rmb)+'" type="text"></span>';
						if(temp.status==1){
							dd3+='<span><select class="status" autocomplete="off"><option value="1" selected>上架</option><option value="2">下架</option></select></span><div class="line"></div></dd>';
						}else{
							dd3+='<span><select class="status" autocomplete="off"><option value="1">上架</option><option value="2" selected>下架</option></select></span><div class="line"></div></dd>';
						}
						dd4+='<dd>&emsp;</dd>';
					}
					ul+='<li><dl>'+dd1+'</dl></li>';
					ul+='<li><dl>'+dd2+'</dl></li>';
					ul+='<li><dl>'+dd3+'</dl></li>';
					ul+='<li><dl>'+dd4+'</dl></li>';
					ul+='</ul>';
					uls+=ul;
				}
				$(".personalBatchSetList").find(".personalBatchSetItem").remove();
				$(".personalBatchSetList").append(uls);
			}
		});
	});
	$("#searchModel").bind("input propertychange",function(){
		var reg=/^(\s+)|(\s+)$/;
		$(this).val($(this).val().replace(reg,""));
	});
	$(".personalBatchSetList").on("click",".serialCBX",function(){
		var dataSerial=$(this).attr("data-serial");
		var CBXStatus=$(this).attr("checked");
		var isChecked=false;
		if(CBXStatus=="checked"){
			isChecked=true;
		}
		$(".personalBatchSetList input[data-serial="+dataSerial+"]").attr("checked",isChecked);
	});
	$(".personalBatchSetList").on("click",".modelCBX",function(){
		var dataModel=$(this).attr("data-model");
		var CBXStatus=$(this).attr("checked");
		var isChecked=false;
		if(CBXStatus=="checked"){
			isChecked=true;
		}
		$(".personalBatchSetList input[data-model="+dataModel+"]").attr("checked",isChecked);
	});
	$(".personalBatchSetList").on("click",".rmbCBX",function(){
		var dataRmb=$(this).attr("data-rmb");
		var CBXStatus=$(this).attr("checked");
		var isChecked=false;
		if(CBXStatus=="checked"){
			isChecked=true;
		}
		$(".personalBatchSetList input[data-rmb="+dataRmb+"]").attr("checked",isChecked);
	});
	$(".personalBatchSetList").on("click",".serialIdCBX",function(){
		var dataSerialId=$(this).attr("data-serial-id");
		var CBXStatus=$(this).attr("checked");
		var isChecked=false;
		if(CBXStatus=="checked"){
			isChecked=true;
		}
		$(".personalBatchSetList input[data-serial-id="+dataSerialId+"]").attr("checked",isChecked);
	});
	$(".personalBatchSetList").on("click",".modelIdCBX",function(){
		var dataModelId=$(this).attr("data-model-id");
		var CBXStatus=$(this).attr("checked");
		var isChecked=false;
		if(CBXStatus=="checked"){
			isChecked=true;
		}
		$(".personalBatchSetList input[data-model-id="+dataModelId+"]").attr("checked",isChecked);
	});
	$(".editStatus").click(function(){
		var selectValue=$(this).attr("data-status-value");
		var rusCBXStatus=$(".personalBatchSetList input.rusCBX:checked");
		for(var i=0,j=rusCBXStatus.size();i<j;i++){
			rusCBXStatus.eq(i).parents(".rmbItem").find("select.status").eq(0).val(selectValue);
		}
	});
	function RMB2USD(rmb){
		usd=rmb/1.17/rateRMB;
		return usd.toFixed(2);
	}
	function toFixed(rmb){
		rmb=rmb-0;
		return rmb.toFixed(2);
	}
});
