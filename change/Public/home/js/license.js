$(function(){
	/*登陆界面图片（判断屏幕高度）*/
	var whole=$(document).height();
//	$(".left").css("height",whole+"px");
//	console.log("whole"+whole);
	$("#uploadBtns").click(function(){
		$("#AR_upload").show();
	});
	$("#cancle").click(function(){
		$("#AR_upload").hide();
	});
	$("#complete").click(function(){
		$("#AR_upload").hide();
		location.href=loginUrl;
	});
});
layui.use('upload', function(){
	var $ = layui.jquery,upload = layui.upload;
	var uploadInst = upload.render({
			elem: '.uploadBtn',
			url: url,
			accept:"file",
			exts:'pdf',
			size:5*1024,
			before: function(obj){
				flag=false;
				percent=Math.floor(15+Math.random()*57);
				msgTip(percent);
				this.url=url;
				var cat=this.item.attr("data-category");
				this.url=this.url.replace("_xxxxx_",cat);
			},
			done: function(res){
				clearTimeout(t);
				msgTip(100,function(res,that){
					if(res.code >0){
						if(res.code==2){
							var html="";
							if(that.item.attr("data-category")=="taxes"){
								html='<dl><dd>'+
									'一般纳税人证明（带公章）</dd><dd>不明格式</dd>'+
								'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
							}
							if(that.item.attr("data-category")=="license"){
								html='<dl><dd>'+
									'营业执照（带公章）</dd><dd>不明格式</dd>'+
								'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
							}
							if(that.item.attr("data-category")=="invoice"){
								html='<dl><dd>'+
									'开票资料(带公章)<br/>（开票名称/银行信息）</dd><dd>不明格式</dd>'+
								'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
							}
							that.item.removeClass("upload_04").addClass("upload_03").html(html);
							return layer.msg(res.msg);
						}
						if(res.code==3){
							var html="";
							if(that.item.attr("data-category")=="taxes"){
								html='<dl><dd>'+
									'一般纳税人证明（带公章）</dd><dd>档案太大</dd>'+
								'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
							}
							if(that.item.attr("data-category")=="license"){
								html='<dl><dd>'+
									'营业执照（带公章）</dd><dd>档案太大</dd>'+
								'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
							}
							if(that.item.attr("data-category")=="invoice"){
								html='<dl><dd>'+
									'开票资料(带公章)<br/>（开票名称/银行信息）</dd><dd>档案太大</dd>'+
								'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
							}
							that.item.removeClass("upload_04").addClass("upload_02").html(html);
							return layer.msg(res.msg);
						}
						//return layer.msg('上传失败');
					}
					var html="";
					if(that.item.attr("data-category")=="taxes"){
						html='<span>一般纳税人证明（带公章）<br/>已上传</span>';
					}
					if(that.item.attr("data-category")=="license"){
						html='<span>营业执照（带公章）<br/>已上传</span>';
					}
					if(that.item.attr("data-category")=="invoice"){
						html='<span>开票资料(带公章)<br/>（开票名称/银行信息）<br/>已上传</span>';
					}
					that.item.removeClass("upload_04").addClass("upload_01").html(html);
					//return layer.msg('上传成功');
				},res,this);
				
			},
			error: function(){
				
			}
	});
});
var t,flag=false;
function msgTip(percent,fn,arg1,arg2){
	$("#msgTip2").show();
	$("#screen").show();
	var h=$(window).height();
	var w=$(window).width();
	var selfW=$("#msgTip2").width();
	$("#msgTip2").css({"top":(h/2-25)+"px","left":(w-selfW)/2+"px"});
	animate(percent,fn,arg1,arg2);
}
function animate(percent,fn,arg1,arg2){
	percents=$("#progressTip").html()-0;
	if(percents<percent){
		percents++;
		if(percents>=100){
			percents=100;
			flag=true
		}
		$("#progressTip").html(percents);
		$("#progressNum").css("width",(percents*2)+5+"px");
		
		t=setTimeout(function(){
			animate(percent,fn,arg1,arg2);
		},30);
	}else{
		clearTimeout(t);
		if(flag){
			$("#msgTip2").fadeOut(2000,"swing",function(){
				$("#screen").hide();
				$("#progressTip").html(0);
				$("#progressNum").css({"width":5+"px"});
				if(typeof fn==="function"){
					fn(arg1,arg2);
				}
			});
		}
	}
}

