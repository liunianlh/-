 $(function(){
	$("#viewMode").change(function(){
		var value=$(this).val();
		if(value=="picture"){
			location.href=$(this).attr("data-url1");
		}else{
			location.href=$(this).attr("data-url2");
		}
	});
 });
