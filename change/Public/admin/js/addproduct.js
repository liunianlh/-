$(function(){
	$("#addModel").click(function(){
		var ul=$(this).parent();
			ul.before("<ul class='Model-list'>"+ul.prev().html()+"</ul>");
		var lastM=$("#Model").find(".Model-list").last();
		lastM.find("input[name=model]").eq(0).val('');
		lastM.find("input[name=length]").eq(0).val('');
		lastM.find("input[name=mao_weight]").eq(0).val('');
		lastM.find("input[name=jing_weight]").eq(0).val('');
		lastM.find("input[name=cai_volume]").eq(0).val('');
		lastM.find("input[name=zx_number]").eq(0).val('');
		lastM.find("input[name=kc_number]").eq(0).val('');
		lastM.find("input[name=rmb1]").eq(0).val('');
		lastM.find("input[name=usd1]").eq(0).val('');
		lastM.find("input[name=rmb2]").eq(0).val('');
		lastM.find("input[name=usd2]").eq(0).val('');
		lastM.find(".removeModel").eq(0).attr("data-id",0);
	});
	$(document).on("click",".removeModel",function(){
		var dataId=$(this).attr("data-id");
		var that=$(this);
		if((dataId-0)){
			$.ajax({
				url:url3,
				type:"post",
				dataType:"json",
				data:{specId:dataId},
				success:function(res){
					var code=res.code;
					switch(code){
						case 10102:
							layer.msg(res.msg);
							that.parents("ul").remove();
							break;
						case 10103:
							layer.msg(res.msg);
							that.parents("ul").remove();
							location.reload();
							break;
						default:
							location.href=res.url;
					}
				}
			});
		}else{
			if($("#Model").find("ul").size()==3){
				alert("必须保留一行数据！");
				return;
			}
			$(this).parents("ul").remove();
		}
	});
	
	$("#saveBtn,#saveBtn2").click(function(){
		var url=$(this).attr("data-url");
		var identity=$(this).attr("data-identity");
		var dataId=$(this).attr("data-id");
		var proId=$("#upic").attr("pic-src");
		var chinese=$("#chinese").val();
		var english=$("#english").val();
		var mlist=$("#Model .Model-list");
		var grade=$("#grade").val();
		var user=$("#user").val();
		if(!proId){
			return layer.msg("没有上传图片");
		}
		if(!chinese){
			return layer.msg("缺少中文描述");
		}
		if(!english){
			return layer.msg("缺少英文描述");
		}
		if(mlist.size()<=0){
			return layer.msg("没有添加型号");
		}
		var modellist=[];
		for(var i=0,j=mlist.size();i<j;i++){
			var temp={};
			var curObj=mlist.eq(i);
			temp.protype=curObj.find("select[name=protype]").eq(0).val();
			temp.jietou=curObj.find("select[name=jietou]").eq(0).val();
			temp.model=curObj.find("input[name=model]").eq(0).val();
			temp.color=curObj.find("select[name=color]").eq(0).val();
			temp.length=curObj.find("input[name=length]").eq(0).val();
			temp.mao_weight=curObj.find("input[name=mao_weight]").eq(0).val();
			temp.jing_weight=curObj.find("input[name=jing_weight]").eq(0).val();
			temp.cai_volume=curObj.find("input[name=cai_volume]").eq(0).val();
			temp.zx_number=curObj.find("input[name=zx_number]").eq(0).val();
			temp.kc_number=curObj.find("input[name=kc_number]").eq(0).val();
			temp.rmb1=curObj.find("input[name=rmb1]").eq(0).val();
			temp.usd1=curObj.find("input[name=usd1]").eq(0).val();
			temp.rmb2=curObj.find("input[name=rmb2]").eq(0).val();
			temp.usd2=curObj.find("input[name=usd2]").eq(0).val();
			temp.status1=curObj.find("select[name=status1]").eq(0).val();
			temp.status2=curObj.find("select[name=status2]").eq(0).val();
			temp.specId=curObj.find("a[data-id]").eq(0).attr("data-id");
			if(!temp.model)return layer.msg("第"+(i+1)+"行型号这一列没有填写值");
			if(!temp.length)return layer.msg("第"+(i+1)+"行长度这一列没有填写值");
			if(!temp.mao_weight)return layer.msg("第"+(i+1)+"行毛重这一列没有填写值");
			if(!temp.jing_weight)return layer.msg("第"+(i+1)+"行净重这一列没有填写值");
			if(!temp.cai_volume)return layer.msg("第"+(i+1)+"行材积这一列没有填写值");
			if(!temp.zx_number)return layer.msg("第"+(i+1)+"行装箱量这一列没有填写值");
			if(!temp.kc_number)return layer.msg("第"+(i+1)+"行库存数这一列没有填写值");
			modellist.push(temp);
		}
		var data={};
		data.proId=proId;
		data.chinese=chinese;
		data.english=english;
		data.modellist=modellist;
		data.dataId=dataId;
		data.grade=grade;
		data.user=user;
		data.productsLink=$("#webLink").val();
		data.productseLink=$("#english_link").val();
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:data,
			success:function(res){
				var code=res.code;
				switch(code){
					case 10090:
					case 10092:
						layer.msg(res.msg);
						if(identity==1){
							//location.href=res.url;
							history.go(-1);
						}else{
							location.href=addproduct;
						}
						break;
					default:
						if(identity==1){
							//location.href=res.url;
							history.go(-1);
						}else{
							location.href=addproduct;
						}
				}
			}
		});
	});
    $(".product_bot").on("input propertychange",".orderSinglePrice",function(){

    });
	// $(document).on("change","input[name=rmb1]"
	function changes(obj){
        var money=obj.val();
        layer.msg(money);
        obj.val(999);
	}
    $('.rmb1s').bind("oninput",function () {
		var money=$(this).val();
		var that=$(this);
		layer.msg(money);
		$(this).val(999);
        // $.ajax({
        //     url:url,
        //     type:"post",
        //     dataType:"json",
        //     data:{sid:serialId},
        //     success:function(res){
        //         if(res.status==1){
        //             var info=res.info;
        //             var opt="";
        //             for(var i=0,j=info.length;i<j;i++){
        //                 var tem=info[i];
        //                 opt+="<option value='"+tem.category_id+"'>"+tem.cat_chinese_name+"</option>";
        //             }
        //             that.parents('ul').find("select[name=jietou]").html(opt);
        //         }
        //     }
        // });
    });
    function RMB2USD(rmb){
        if(((rateRMB-0)==0)||((rmb-0)==0)){
            return 0.00;
        }
        usd=rmb/1.17/rateRMB;
        return usd.toFixed(2);
    }
	$(document).on("change","select[name=protype]",function(){
		var serialId=$(this).val();
		var that=$(this);
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{sid:serialId},
			success:function(res){
				if(res.status==1){
					var info=res.info;
					var opt="";
					for(var i=0,j=info.length;i<j;i++){
						var tem=info[i];
						opt+="<option value='"+tem.category_id+"'>"+tem.cat_chinese_name+"</option>";
					}
					that.parents('ul').find("select[name=jietou]").html(opt);
				}
			}
		});
	});
	
	$(document).on("click",".ischeck",function(){
		var name=$(this).attr("data-class");
		var checked=$(this).attr("checked");
		$(".Model-list").find("input[name="+name+"]").attr("checked",checked?true:false);
	});
	
	$("#grade,#user").change(function(){
		var dataFlag=$(this).attr("data-flag");
		var proId=$("#saveBtn").attr("data-id");
		if(!proId){
			return;
		}
		if(dataFlag=="grade"){
			gradeId=$(this).val();
			userId='';
		}else{
			gradeId=$("#grade").val();
			userId=$(this).val();
		}
		$.ajax({
			url:url4,
			type:"post",
			dataType:"json",
			data:{gradeId:gradeId,userId:userId,productId:proId},
			success:function(res){
				if(res.code==10102){
					var info=res.msg;
					var specInfo=info.spec||[];
					var category=info.category;
					var color=info.color;
					var product_status=info.product_status;
					var uls='';
					for(var i=0,j=specInfo.length;i<j;i++){
						var ul="<ul class='Model-list'>";
						var tem=specInfo[i];
						var opt1='';
						for(var m=0,n=category.length;m<n;m++){
							var temp1=category[m];
							if(temp1.category_id==tem.serial_id){
								opt1+="<option value='"+temp1.category_id+"' selected>"+temp1.cat_chinese_name+"</option>";
							}else{
								opt1+="<option value='"+temp1.category_id+"'>"+temp1.cat_chinese_name+"</option>";
							}
						}
						ul+="<li><select name='protype'>"+opt1+"</select></li>";
						var joint=tem.joint;
						var opt2='';
						for(var k=0,l=joint.length;k<l;k++){
							var temp2=joint[k];
							if(temp2.category_id==tem.joint_id){
								opt2+="<option value='"+temp2.category_id+"' selected>"+temp2.cat_chinese_name+"</option>";
							}else{
								opt2+="<option value='"+temp2.category_id+"'>"+temp2.cat_chinese_name+"</option>";
							}
						}
						ul+="<li><select name='jietou'>"+opt2+"</select></li>";
						ul+="<li><input name='model' value='"+tem.model_name+"' type='text'/></li>";
						var opt3='';
						for(var q=0,r=color.length;q<r;q++){
							var temp3=color[q];
							if(temp3.color_id==tem.color_id){
								opt3+="<option value='"+temp3.color_id+"' selected>"+temp3.color_chinese_name+"</option>";
							}else{
								opt3+="<option value='"+temp3.color_id+"'>"+temp3.color_chinese_name+"</option>";
							}
						}
						ul+="<li><select name='color'>"+opt3+"</select></li>";
						ul+="<li><input name='length' value='"+tem.length+"' type='text'/></li>";
						ul+="<li><input name='mao_weight' value='"+tem.rough_weight+"' type='text'/></li>";
						ul+="<li><input name='jing_weight' value='"+tem.net_weight+"' type='text'/></li>";
						ul+="<li><input name='cai_volume' value='"+tem.volume+"' type='text'/></li>";
						ul+="<li><input name='zx_number' value='"+tem.loading+"' type='text'/></li>";
						ul+="<li><input name='kc_number' value='"+tem.inventory+"' type='text'/></li>";
						ul+="<li><input name='ischeck1' type='checkbox'></li>";
						ul+="<li><input name='rmb1' class='rmb1s'  oninput='changes(this)' value='"+tem.rmb1+"' type='text'/><input name='usd1' value='"+RMB2USD(tem.rmb1)+"' oninput='change_start(this)' type='text'/></li>";
						var opt4='';
						for(var s=0,t=product_status.length;s<t;s++){
							var temp4=product_status[s];
							if(temp4.products_status_id==tem.status1){
								opt4+="<option value='"+temp4.products_status_id+"' selected>"+temp4.ps_chinese_name+"</option>";
							}else{
								opt4+="<option value='"+temp4.products_status_id+"'>"+temp4.ps_chinese_name+"</option>";
							}
						}
						ul+="<li><select name='status1'>"+opt4+"</select></li>";
						ul+="<li><input name='ischeck2' type='checkbox'></li>";
						ul+="<li><input name='rmb2'  oninput='changes(this)' value='"+tem.rmb2+"' type='text'/><input name='usd2'  oninput='change_start(this)' value='"+RMB2USD(tem.rmb2)+"' type='text'/></li>";
						var opt5='';
						for(var s=0,t=product_status.length;s<t;s++){
							var temp5=product_status[s];
							if(temp5.products_status_id==tem.status2){
								opt5+="<option value='"+temp5.products_status_id+"' selected>"+temp5.ps_chinese_name+"</option>";
							}else{
								opt5+="<option value='"+temp5.products_status_id+"'>"+temp5.ps_chinese_name+"</option>";
							}
						}
						ul+="<li><select name='status2'>"+opt5+"</select></li>";
						ul+="<li><a class='removeModel' href='javascript:;' data-id="+tem.specification_id+">删除</a></li>";
						ul+="</ul>";
						uls+=ul;
					}
					if(specInfo.length>0){
						$("#Model").find(".Model-list").remove();
						$("#Model .addModel").before(uls);
					}
					var user=info.user;
					var userId=info.userId;
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
				}
			}
		});
	});
	
	$(".batch-In-Out").click(function(){
		var status=$(this).attr("data-status");
		var modelList=$("#Model").find(".Model-list");
		
		for(var i=0,j=modelList.size();i<j;i++){
			var temp=modelList.eq(i);
			if(status==1){
				temp.find("input[name=ischeck1]:checked").parent().siblings("li").find("select[name=status1]").val(1);
				temp.find("input[name=ischeck2]:checked").parent().siblings("li").find("select[name=status2]").val(1);
			}
			if(status==2){
				temp.find("input[name=ischeck1]:checked").parent().siblings("li").find("select[name=status1]").val(2);
				temp.find("input[name=ischeck2]:checked").parent().siblings("li").find("select[name=status2]").val(2);
			}
		}
	});
	
	function RMB2USD(rmb){
		usd=rmb/1.17/rateRMB;
		return usd.toFixed(2);
	}
});
