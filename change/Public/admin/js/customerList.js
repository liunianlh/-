layui.use("layer");
$(function(){
	$("#UID,#customerName,#tianxin").bind("input propertychange",function(){
		query(url);
	});
	$("#adminId,#gradeId").change(function(){
		query(url);
	});
	$("#CBX").click(function(){
		var checkedStatus=$(this).attr("checked");
		isChecked=false;
		if(checkedStatus=="checked"){
			isChecked=true;
		}
		$(".userList").find("input.CBXItem").attr("checked",isChecked);
	});
	$(".userStatus").click(function(){
		var dataValue=$(this).attr("data-value");
		var userList=$(".userList").find("input.CBXItem:checked");
		var userData=[];
		for(var i=0,j=userList.size();i<j;i++){
			userData.push(userList.eq(i).attr('data-key'));
		}
		$.ajax({
			url:url2,
			type:"post",
			dataType:"json",
			data:{userData:userData,dataValue:dataValue},
			success:function(res){
				if(res.code==10123){
					layer.msg(res.msg,{shade:0.3,time:3000,shadeClose:true,end:function(){location.reload();}});
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	$("#page").on("click","a.next,a.prev,a.num",function(e){
		e.preventDefault();
		var curPageLink=$(this).attr("href");
		$("#page").attr("data-cur-page",curPageLink);
		query(curPageLink);
	});
	function query(url){
		var condition={};
		var UID=$("#UID").val();
		var customerName=$("#customerName").val();
		var reg=/\s+/;
		UID=UID.replace(reg,'');
		customerName=customerName.replace(reg,'');
		$("#UID").val(UID);
		$("#customerName").val(customerName);
		var adminId=$("#adminId").val();
		var gradeId=$("#gradeId").val();
		var tianxin=$("#tianxin").val();
		condition.UID=UID;
		condition.customerName=customerName;
		condition.adminId=adminId;
		condition.gradeId=gradeId;
		condition.tianxin=tianxin;
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{condition:condition},
			success:function(res){
				if(res.code==10123){
					var userInfo=res.msg.user_info;
					var uls='';
					for(var i=0,j=userInfo.length;i<j;i++){
						var temp=userInfo[i];
						ul='<ul class="userItem">';
						ul+='<li><input class="CBXItem" data-key="'+temp.hash+'" type="checkbox" /></li>';
						switch(temp.account_status_id){
							case "1":
								ul+='<li><font color="green">正常</font></li>';
								break;
							case "2":
								ul+='<li><font color="gray">待审核</font></li>';
								break;
							case "3":
								ul+='<li><font color="red">失效</font></li>';
								break;
							case "4":
								ul+='<li><font color="red">注册失败</font></li>';
								break;
						}
						ul+='<li>'+temp.user_uid+'</li>';
						// if(temp.company_contacts){
						// 	ul+='<li>'+temp.company_name+'</li>';
						// }else{
						// 	ul+='<li>-</li>';
						// }
						ul+='<li>'+temp.company_name+'</li>';
						if(temp.grade){
							ul+='<li>'+temp.grade+'</li>';
						}else{
							ul+='<li>-</li>';
						}
						ul+='<li style="width:8%;">'+temp.order_time+'</li>';
						ul+='<li style="width:8%;">'+temp.last_time+'</li>';
						ul+='<li style="width:8%;">'+temp.contract_time+'</li>';
						ul+='<li style="width:8%;">'+temp.tianxin_code+'</li>';
						if(temp.admin_name){
							ul+='<li style="width:8%;">'+temp.admin_name+'</li>';
						}else{
							ul+='<li>-</li>';
						}
						ul+='<li><a href="'+temp.url+'">查看</a>&nbsp;|&nbsp;<a class="del" data-id="'+temp.user_id+'" href="javascript:;">删除</a></li>';
						ul+="</ul>";
						uls+=ul;
					}
					$(".userList").find(".userItem").remove().end().append(uls);
					$("#page").html(res.msg.page);
				}
			}
		});
	}
	$(document).on("click",".del",function(){
		if(confirm("确定要删除吗？")==false)return;
		var id=$(this).attr("data-id");
		var _this = $(this)
		$.ajax({
			url:url3,
			type:"post",
			dataType:"json",
			data:{id:id},
			success:function(res){
				layer.msg(res.msg);
				_this.parent().parent().remove()
				if(res.code==10029){
					$("#country").change();
				}
			}
		});
	});
});
