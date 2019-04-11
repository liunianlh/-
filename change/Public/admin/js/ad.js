var upload;
var unqueArr=[];
$(function(){
	$(".addBtn").click(function(){
		var randStr=createUniqueId();
		var html='<li class="modelist" data-id="0"><ul><li><span>图片</span><input type="text" autocomplete="off" class="picText" placeholder="Order System" /></li><li><a href="javascript:;" class="uploadBtn" id="'+randStr+'"><div class="button">上传</div></a></li><li><span>链接</span><input autocomplete="off" class="linkText" type="text"/></li><li style="padding-top:20px;width:5%;"><a href="javascript:;"><div class="remove remBtn button">移除</div></a></li></ul></li>';
		$(this).parents(".mode").append(html);
		if(!upload){
			upload = layui.upload;
		}
		reloadUpload(upload,"#"+randStr);//重新载入
	});
	$(".mode").on("click",".remBtn",function(){
		var adId=$(this).parents(".modelist").attr("data-id");
		var that=$(this);
		if(adId!=0){
			$.ajax({
				url:url3,
				type:"post",
				dataType:"json",
				data:{adId:adId},
				success:function(res){
					if(res.code==6){
						layer.msg(res.msg);
						that.parents(".modelist").remove();
					}else{
						layer.msg(res.msg);
					}
				}
			});
		}else{
			$(this).parents(".modelist").remove();
		}
	});
	layui.use('upload', function(){
		var $ = layui.jquery;
		upload = layui.upload;
		var uploadBtns=$(".uploadBtn");
		for(var i=0,j=uploadBtns.size();i<j;i++){
			var randStr=createUniqueId();
			uploadBtns.eq(i).attr("id",randStr);
		}
		var uploadInst =reloadUpload(upload,unqueArr.join(","));
	});
	function reloadUpload(upload,idStr=''){
		var uploadInst=upload.render({
				elem: idStr,
				url: url,
				accept:"file",
				exts:'jpg|jpeg|png|gif',
				before: function(obj){
					var flag=this.item.parents("ul.mode").attr("data-flag");
					this.url=this.url.replace("_xxxxx_",flag);
				},
				done: function(res){
					this.item.parent().prev().find(".picText").eq(0).val(res.picPath);
					return layer.msg(res.msg);
				},
				error: function(){
					
				}
		});
		return uploadInst;
	}
	function createUniqueId(len=10){
		var randStr='';
		var strArr=["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"];
		var strArrLen=strArr.length;
		var flag=true;
		do{
			for(var i=0;i<len;i++){
				randStr+=strArr[parseInt(Math.random()*strArrLen)];
			}
			for(var m=0,n=unqueArr.length;m<n;m++){
				if(randStr==unqueArr[m]){
					flag=false;
					break;
				}
			}
			if(flag){
				flag=false;
			}else{
				flag=true;
			}
		}while(flag);
		unqueArr.push("#"+randStr);
		return randStr;
	}
	
	$("#saveBtn").click(function(){
		var modeList=[];
		var picModeList=$(".picMode .modelist");
		for(var i=0,j=picModeList.size();i<j;i++){
			var temp={};
			temp.type=1;
			temp.id=picModeList.eq(i).attr("data-id");
			temp.picPath=picModeList.eq(i).find(".picText").eq(0).val();
			temp.picLink=picModeList.eq(i).find(".linkText").eq(0).val();
			modeList.push(temp);
		}
		var speedModeList=$(".speedMode .modelist");
		for(var m=0,n=speedModeList.size();m<n;m++){
			var temp2={};
			temp2.type=2;
			temp2.id=speedModeList.eq(m).attr("data-id");
			temp2.picPath=speedModeList.eq(m).find(".picText").eq(0).val();
			temp2.picLink=speedModeList.eq(m).find(".linkText").eq(0).val();
			modeList.push(temp2);
		}
		$.ajax({
			url:url2,
			type:"post",
			dataType:"json",
			data:{modeList:modeList},
			success:function(res){
				if(res.code==6){
					layer.msg(res.msg);
					location.reload();
				}else{
					layer.msg(res.msg);
				}
			}
		});
	});
});
