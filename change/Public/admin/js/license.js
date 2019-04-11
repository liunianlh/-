$(function(){
	/*登陆界面图片（判断屏幕高度）*/
	var whole=$(document).height();
	$(".left").css("height",whole+"px");
	console.log("whole"+whole);
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
			before: function(obj){
				this.url=url
				var cat=this.item.attr("data-category");
				this.url=this.url.replace("_xxxxx_",cat);
			},
			done: function(res){
				if(res.code >0){
					if(res.code==2){
						var html="";
						if(this.item.attr("data-category")=="taxes"){
							html='<dl><dd>'+
								'一般纳税人证明（带公章）</dd><dd>不明格式</dd>'+
							'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
						}
						if(this.item.attr("data-category")=="license"){
							html='<dl><dd>'+
								'营业执照（带公章）</dd><dd>不明格式</dd>'+
							'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
						}
						if(this.item.attr("data-category")=="invoice"){
							html='<dl><dd>'+
								'开票资料(带公章)<br/>（开票名称/银行信息）</dd><dd>不明格式</dd>'+
							'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
						}
						this.item.removeClass("upload_04").addClass("upload_03").html(html);
						return layer.msg(res.msg);
					}
					if(res.code==3){
						var html="";
						if(this.item.attr("data-category")=="taxes"){
							html='<dl><dd>'+
								'一般纳税人证明（带公章）</dd><dd>档案太大</dd>'+
							'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
						}
						if(this.item.attr("data-category")=="license"){
							html='<dl><dd>'+
								'营业执照（带公章）</dd><dd>档案太大</dd>'+
							'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
						}
						if(this.item.attr("data-category")=="invoice"){
							html='<dl><dd>'+
								'开票资料(带公章)<br/>（开票名称/银行信息）</dd><dd>档案太大</dd>'+
							'<dd><a class="retry" href="javascript:;">点击重新上传</a></dd></dl>';
						}
						this.item.removeClass("upload_04").addClass("upload_02").html(html);
						return layer.msg(res.msg);
					}
					return layer.msg('上传失败');
				}
				var html="";
				if(this.item.attr("data-category")=="taxes"){
					html='<span>一般纳税人证明（带公章）<br/>已上传</span>';
				}
				if(this.item.attr("data-category")=="license"){
					html='<span>营业执照（带公章）<br/>已上传</span>';
				}
				if(this.item.attr("data-category")=="invoice"){
					html='<span>开票资料(带公章)<br/>（开票名称/银行信息）<br/>已上传</span>';
				}
				this.item.removeClass("upload_04").addClass("upload_01").html(html);
				return layer.msg('上传成功');
			},
			error: function(){
				
			}
	});
});

