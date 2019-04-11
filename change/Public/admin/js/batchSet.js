$(function(){
	layui.use("layer");
	$("#saveBtn").click(function(){
		var gpsList=$(".gpsList");
		var gps=[];
		for(var i=0,j=gpsList.size();i<j;i++){
			var gpsItem=gpsList.eq(i).find(".gpsItem");
			for(var m=0,n=gpsItem.size();m<n;m++){
				var temp={};
				temp.hash=gpsItem.eq(m).attr("data-hash");
				temp.rmb=gpsItem.eq(m).find(".rmb").eq(0).val();
				temp.status=gpsItem.eq(m).find(".status").eq(0).val();
				gps.push(temp);
			}
		}
		var url=$(this).attr("data-url");
		var totalCount=gps.length;//总长度
		var index=1;//  分段序列
		var total=Math.ceil(totalCount/300);//总序列
		gps_will_post=gps.slice((index-1)*300,index*300);
		
		postData(url,gps_will_post);//提交数据
		
		function postData(url,data){
			$.ajax({
				url:url,
				type:"post",
				dataType:"json",
				data:{gps:data},
				success:function(res){
					index++;
					if(index>total){
						layer.msg(res.msg);
						var searchModel=$("#searchModel").val();
						if(searchModel.replace(/^(\s+)|(\s+)$/,'')!=''){
							$("#searchBtn").click();
						}else{
							location.reload();
						}
					}else{
						
						gps_will_post=gps.slice((index-1)*300,index*300);
						postData(url,gps_will_post);//提交数据
						
					}
				}
			});
		}
		
	});
	
	$("#searchBtn").click(function(){
		var url=$(this).attr("data-url");
		var searchModel=$("#searchModel").val();
		searchModel=searchModel.replace(/^\s+$/,"");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{searchModel:searchModel},
			success:function(res){
				var info=res.msg;
				var spec_info=info.spec_info;
				var grade_info=info.grade_info;
				var html='';
				var oldName='';
				for(var i=0,j=spec_info.length;i<j;i++){
					var temp=spec_info[i];
					var newName=temp.serial_name;
					if(oldName==newName){
						html+='<li class="serialItem"><dl class="border_bot_none"><dt class="border_bot_none">&nbsp;</dt><dt><div><input class="modelCBX" type="checkbox" data-serial-id="'+temp.serial_id+'" data-model-id="model'+temp.specification_id+'" data-serial="serial" data-model="model" data-flag="model"/></div><span title="'+temp.model_name+'">'+temp.model_name+'</span></dt></dl></li>';
					}else{
						if(i==0){
							html+='<li class="serialItem"><dl class="border_bot_none"><dt class="border_bot_none"><div><input type="checkbox" class="serialCBX" data-serial-id="'+temp.serial_id+'" data-flag="serial" data-serial="serial"/></div>'+temp.serial_name+'</dt><dt><div><input class="modelCBX" type="checkbox" data-serial-id="'+temp.serial_id+'" data-model-id="model'+temp.specification_id+'" data-serial="serial" data-model="model" data-flag="model"/></div><span title="'+temp.model_name+'">'+temp.model_name+'</span></dt></dl></li>';
						}else{
							html+='<li class="serialItem"><dl class="border_bot_none"><dt class="border_bot_none" style="border-top: 1px solid rgb(223, 223, 223) !important;"><div><input type="checkbox" class="serialCBX" data-serial-id="'+temp.serial_id+'" data-flag="serial" data-serial="serial"/></div>'+temp.serial_name+'</dt><dt><div><input class="modelCBX" type="checkbox" data-serial-id="'+temp.serial_id+'" data-model-id="model'+temp.specification_id+'" data-serial="serial" data-model="model" data-flag="model"/></div><span title="'+temp.model_name+'">'+temp.model_name+'</span></dt></dl></li>';
						}
					}
					oldName=newName;
				}
				$(".serialList .serialItem").remove();
				$(".serialList>ul").append(html);
				var html2='';
				for(var gr in grade_info){
					var temp2=grade_info[gr];
					html2+='<div class="model_2 gpsList"><ul><li>'+temp2.name+'</li><li><div><input type="checkbox" class="gradeCBX" data-grade-id="'+temp2.gradeId+'"/></div><span>RMB</span><span>USD</span><span>状态</span></li>';
					var grv=temp2.value;
					var html3='';
					for(var m=0,n=grv.length;m<n;m++){
						var temp3=grv[m];
						html3+='<li class="gpsItem" data-hash="'+temp3._hashKey+'"><div><input type="checkbox" class="rusCBX" data-grade-id="'+temp3.grade_id+'" data-model="model" data-serial-id="'+temp3.serial_id+'" data-model-id="'+temp3.specification_id+'" data-serial="serial"/></div> <input type="text"  oninput="changes(this)"  class="rmb" value="'+toFixed(temp3.rmb)+'" placeholder=""/> <input class="usd" type="text"  oninput="change_start(this)" value="'+RMB2USD(temp3.rmb)+'" placeholder=""/>'+
								' <select autocomplete="off" class="status">';
						if(temp3.products_status_id==1){
							html3+='<option value="1" selected>上架</option><option value="2">下架</option>';
						}else{
							html3+='<option value="1">上架</option><option value="2" selected>下架</option>';
						}
						html3+='</select></li>';
					}
					html2+=html3+'</ul></div>';
				}
				$(".spec_info .gpsList").remove();
				$(".spec_info").append(html2);
			}
		});
	});
	$("#searchModel").bind("input propertychange",function(){
		var reg=/^(\s+)|(\s+)$/;
		$(this).val($(this).val().replace(reg,""));
	});
	$(".rmb,.usd").bind("input propertychange",function(){
		var reg=/\s+/;
		$(this).val($(this).val().replace(reg,""));
	});
	$(".spec_info").on("click",".serialCBX",function(){
		var dataSerialId=$(this).attr("data-serial-id");
		var CBXStatus=$(this).attr("checked");
		var isChecked=false;
		if(CBXStatus=="checked"){
			isChecked=true;
		}
		$(".spec_info input[data-serial-id="+dataSerialId+"]").attr("checked",isChecked);
	});
	$(".spec_info").on("click",".modelCBX",function(){
		var dataModelId=$(this).attr("data-model-id");
		var CBXStatus=$(this).attr("checked");
		var isChecked=false;
		if(CBXStatus=="checked"){
			isChecked=true;
		}
		$(".spec_info input[data-model-id="+dataModelId+"]").attr("checked",isChecked);
	});
	$(".spec_info").on("click",".serialAllCBX",function(){
		var dataSerial=$(this).attr("data-serial");
		var CBXStatus=$(this).attr("checked");
		var isChecked=false;
		if(CBXStatus=="checked"){
			isChecked=true;
		}
		$(".spec_info input[data-serial="+dataSerial+"]").attr("checked",isChecked);
	});
	$(".spec_info").on("click",".modelAllCBX",function(){
		var dataModel=$(this).attr("data-model");
		var CBXStatus=$(this).attr("checked");
		var isChecked=false;
		if(CBXStatus=="checked"){
			isChecked=true;
		}
		$(".spec_info input[data-model="+dataModel+"]").attr("checked",isChecked);
	});
	$(".spec_info").on("click",".gradeCBX",function(){
		var dataGradeId=$(this).attr("data-grade-id");
		var CBXStatus=$(this).attr("checked");
		var isChecked=false;
		if(CBXStatus=="checked"){
			isChecked=true;
		}
		$(".spec_info input[data-grade-id="+dataGradeId+"]").attr("checked",isChecked);
	});
	$(".editStatus").click(function(){
		var selectValue=$(this).attr("data-status-value");
		var rusCBXStatus=$(".spec_info input.rusCBX:checked");
		for(var i=0,j=rusCBXStatus.size();i<j;i++){
			rusCBXStatus.eq(i).parents(".gpsItem").find("select.status").eq(0).val(selectValue);
		}
	});
	function RMB2USD(rmb){
		if(((rateRMB-0)==0)||((rmb-0)==0)){
			return 0.00;
		}
		usd=rmb/1.17/rateRMB;
		return usd.toFixed(2);
	}
	function toFixed(rmb){
		rmb=rmb-0;
		return rmb.toFixed(2);
	}
});
