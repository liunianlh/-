layui.use("layer");
var uploader = new plupload.Uploader({
	runtimes : 'html5,flash,silverlight,html4',
	browse_button : 'uploadBtn',
	container: document.getElementById('P_details_box'),
	url : uploadUrl,
	flash_swf_url : '../../../plugins/plupload/js/Moxie.swf',
	silverlight_xap_url : '../../../plugins/plupload/js/Moxie.xap',
	
	filters : {
		max_file_size : '5mb',
		mime_types: [
			{title : "Image files", extensions : "jpg,gif,png"}
		]
	},

	init: {
		FilesAdded: function(up, files) {
			plupload.each(files, function(file) {
				if(window.FileReader){
				  var reader = new FileReader();
					reader.readAsDataURL(file.getNative());
					reader.onload = function(){
						$("#upic").attr("src",this.result);
						uploader.start();
					}
				}
			});
			
		},
		UploadProgress: function(up, file) {
			$("#progressBar").css({"width":file.percent+"px"});
			$("#uploadProgress").html(file.percent+"%");
		},
		FileUploaded:function(up,file,resp){
			try{
				var obj=jQuery.parseJSON(resp.response);
				if(obj.code==0){
					$("#upic").attr("pic-src",obj.picPath);
					return layer.msg('上传成功');
				}else{
					return layer.msg('上传失败');
				}
			}catch(e){
				$("#progressBar").css({"width":0+"px"});
				$("#uploadProgress").html('');
				return layer.msg('上传失败');
			}
		},
		Error: function(up, err) {
			layer.msg(err.message);
		}
	}
});
uploader.init();