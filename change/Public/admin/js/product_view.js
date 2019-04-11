$(function(){
	layui.use("layer");
	
	$("#searchCategory").click(function(){
		var sid=$(this).val();
		var url=$(this).attr("data-url");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{sid:sid},
			success:function(res){
				$("#searchJoint").html(res.msg);
			}
		});
	});
	
	$("#grade,#user").change(function(){
		var dataFlag=$(this).attr("data-flag");
		if(dataFlag=="grade"){
			gradeId=$(this).val();
			userId='';
		}else{
			gradeId=$("#grade").val();
			userId=$(this).val();
		}
		var curPageLink=$("#page").attr("data-cur-page");
		where=wrapCondition();
		dataDeal(gradeId,userId,curPageLink?curPageLink:url,'',where);
	});
	$("#page").on("click","a.next,a.prev,a.num",function(e){
		e.preventDefault();
		gradeId=$("#grade").val();
		userId=$("#user").val();
		var curPageLink=$(this).attr("href");
		$("#page").attr("data-cur-page",curPageLink);
		where=wrapCondition();
		dataDeal(gradeId,userId,curPageLink,'',where);
	});
	$(".pagesize").click(function(){
		gradeId=$("#grade").val();
		userId=$("#user").val();
		where=wrapCondition();
		var pageSize=$(this).attr("data-page-size");
		dataDeal(gradeId,userId,url,pageSize,where);
	});
	$("#searchCategory,#searchJoint").change(function(){
		gradeId=$("#grade").val();
		userId=$("#user").val();
		where=wrapCondition();
		dataDeal(gradeId,userId,url,'',where);
	});
	$("#searchModel,#searchKeyWord").bind("input propertychange",function(){
		gradeId=$("#grade").val();
		userId=$("#user").val();
		where=wrapCondition();
		dataDeal(gradeId,userId,url,'',where);
	});
	$("#flushInventory").click(function(){
		flushInventory();
	});
	$(document).on("click",".del",function(){
		if(confirm("确定要删除吗？")==false)return;
		var dataId=$(this).attr("data-id");
		var that=$(this);
		$.ajax({
			url:delUrl,
			type:"post",
			dataType:"json",
			data:{specId:dataId},
			success:function(res){
				var code=res.code;
				layer.msg(res.msg);
				switch(code){
					case 10102:
					case 10103:
						$("#searchCategory").change();
						break;
					default:
						
				}
			}
		});
	});
	function dataDeal(gradeId,userId,url,pageSize,where){
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{gradeId:gradeId,userId:userId,pageSize:pageSize,where:where},
			success:function(res){
				if(res.code==10102){
					var info=res.msg;
					var specInfo=info.spec;
					var uls='';
					for(var i=0,j=specInfo.length;i<j;i++){
						var ul="<ul class='Mlist'>";
						var tem=specInfo[i];
//						if(tem.spec_img){
//							ul+="<li class='uploadBtn' data-pic='"+tem.spec_img+"'><img src='"+publicPath+tem.spec_img+"' width='26' height='26'/></li>";
//						}else{
//							ul+="<li class='uploadBtn' data-pic='"+tem.products_img+"'><img src='"+publicPath+tem.products_img+"' width='26' height='26'/></li>";
//						}
                                                ul+="<li class='uploadBtn' data-pic='"+tem.products_img+"'><img src='"+publicPath+tem.products_img+"' width='26' height='26'/></li>";
						ul+="<li>"+tem.serial_name+"</li>";
						ul+="<li>"+tem.joint_name+"</li>";
						ul+="<li>"+tem.model_name+"</li>";
						ul+="<li>"+tem.products_chinese_name+"</li>";
						ul+="<li>"+tem.length+"m</li>";
						ul+="<li>"+tem.color_name+"</li>";
						ul+="<li><input class='inventory' value="+tem.inventory+" placeholder='' type='text'/></li>";
						ul+="<li><p><span>"+tem.rmb1+"</span><span>"+RMB2USD(tem.rmb1)+"</span></p></li>";
						if(tem.status1==1){
							ul+="<li><font color='green'>上架</font></li>";
						}else{
							ul+="<li><font color='red'>下架</font></li>";
						}
						ul+="<li><p><span>"+tem.rmb2+"</span><span>"+RMB2USD(tem.rmb2)+"</span></p></li>";
						if(tem.status2==1){
							ul+="<li><font color='green'>上架</font></li>";
						}else{
							ul+="<li><font color='red'>下架</font></li>";
						}
						ul+="<li><a class='view blue' data-spec-id='"+tem.specification_id+"' href='"+tem.url+"'>查看</a>&nbsp;|&nbsp;<a class='del blue' data-id='"+tem.specification_id+"' href='javascript:;'>删除</a></li>";
						ul+="</ul>";
						uls+=ul;
					}
					$("#Model").find(".Mlist").remove();
					$("#Model").append(uls);
					var user=info.user;
					var userId=info.user_id;
					var opt6='';
					for(var u=0,v=user.length;u<v;u++){
						var temp6=user[u];
						if(temp6.user_id==userId){
							opt6+="<option value='"+temp6.user_id+"' selected>"+temp6.company_name+"</option>";
						}else{
							opt6+="<option value='"+temp6.user_id+"'>"+temp6.company_name+"</option>";
						}
					}
					$("#user").html(opt6);
					$("#page").html(info.page);
					
					var category=info.category;
					var joint=info.joint;
					var categoryId=info.categoryId;
					var jointId=info.jointId;
					var opt5='<option value="0">全部</option>';
					for(var s=0,t=category.length;s<t;s++){
						var temp5=category[s];
						if(temp5.category_id==categoryId){
							opt5+="<option value='"+temp5.category_id+"' selected>"+temp5.cat_chinese_name+"</option>";
						}else{
							opt5+="<option value='"+temp5.category_id+"'>"+temp5.cat_chinese_name+"</option>";
						}
					}
					$("#searchCategory").html(opt5);
					var opt4='<option value="0">全部</option>';
					for(var q=0,r=joint.length;q<r;q++){
						var temp4=joint[q];
						if(temp4.category_id==jointId){
							opt4+="<option value='"+temp4.category_id+"' selected>"+temp4.cat_chinese_name+"</option>";
						}else{
							opt4+="<option value='"+temp4.category_id+"'>"+temp4.cat_chinese_name+"</option>";
						}
					}
					$("#searchJoint").html(opt4);
				}
			}
		});
	}
	$("#saveInventory").click(function(){
		var saveUrl=$(this).attr("data-url");
		var Mlist=$("#Model").find(".Mlist");
		if(Mlist.size()<=0)return;
		var mlist=[];
		for(var i=0,j=Mlist.size();i<j;i++){
			var temp=Mlist.eq(i);
			var inventory=temp.find(".inventory").eq(0).val();
			var specImg=temp.find(".uploadBtn").eq(0).attr("data-pic");
			var specId=temp.find(".view").eq(0).attr("data-spec-id");
			var tem={};
			tem.inventory=inventory;
			tem.specId=specId;
			tem.specImg=specImg;
			mlist.push(tem);
		}
		$.ajax({
			url:saveUrl,
			type:"post",
			dataType:"json",
			data:{data:mlist},
			success:function(res){
				if(res.code==10103){
					layer.msg(res.msg);
					flushInventory();
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
	function flushInventory(){
		gradeId=$("#grade").val();
		userId=$("#user").val();
		var curPageLink=$("#page").attr("data-cur-page");
		where=wrapCondition();
		dataDeal(gradeId,userId,curPageLink?curPageLink:url,'',where);
	}
	function wrapCondition(){
		var searchModel=$("#searchModel").val();
		var searchKeyWord=$("#searchKeyWord").val();
		var searchCategory=$("#searchCategory").val();
		var searchJoint=$("#searchJoint").val();
		var where={searchModel:searchModel,searchKeyWord:searchKeyWord,searchCategory:searchCategory,searchJoint:searchJoint};
		return where;
	}
	function RMB2USD(rmb){
		usd=rmb/1.17/rateRMB;
		return usd.toFixed(2);
	}
});
