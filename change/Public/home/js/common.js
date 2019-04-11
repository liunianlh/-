$(document).ready(function(){
	//弹出框JS代码
	$("#triangle-down").click(function(e){
		$(".ID_tip").toggle();
		e.stopPropagation();
	});
	$(".account_D").click(function(e){
		$(".Aurhorized_tip").toggle();
		e.stopPropagation();
	});

	$(document).click(function(){
		$(".ID_tip").hide();
		$(".Aurhorized_tip").hide();
		$(".Shopping_tip").hide();
	});

	$("#userCart").click(function(e){
		$(".Shopping_tip").toggle();
		e.stopPropagation();
		var url=$(this).attr("data-url");
		var urlPublic=$(this).attr("data-public");
		$.ajax({
			url:url,
			type:"post",
			dataType:"json",
			data:{},
			success:function(res){
				if(res.code=="10113"){
					var info=res.info;
					var lis='';
					for(var i=0,j=info.length;i<j;i++){
						var temp=info[i];
						// lis+='<li><div><img width="50" height="50" src="'+urlPublic+'/'+temp.product.products_img+'"/></div><div><span>'+temp.model_name+'</span><span>'+formatStr(temp.product.products_name)+'</span></div></li>';
                        lis+='<li><div style="cursor:pointer;display: inline-block;width: 16px;height: 16px;line-height: 13px;border-radius: 50%;background: red;text-align: center;color: white;font-size: 28px;position: absolute;left: 10px;" data-spec-id="'+temp.specification_id+'" class="del">-</div><div><img width="50" height="50" src="'+urlPublic+'/'+temp.product.products_img+'"/></div><div><span>'+temp.model_name+'</span></div></li>';
					}
					$(".Shopping_tip>ul").html(lis);
				}
			}
		});

	});
    //修改锚点
    $(".Shopping_tip").on("click",".del",function(){
		var specId=$(this).attr("data-spec-id");
		var that=$(this);
		$.ajax({
			url:'http://order.tonetron.com/Cart/delSpecFromCart.html',
			type:"post",
			dataType:"json",
			data:{specId:specId},
			success:function(res){
                console.log(res);
                window.location.reload();
				if(res.code==10115){
					flushCart();
					checkInput();
				}
			}
		});
	});
	  // toblock('有效期至');
        // toblock("年");
        // toblock("月");
        // toblock("日");
	  // function toblock(content){
        // var bodyHtml = $(".Aurhorized_tip li:nth-of-type(2)").html();
        // var x = bodyHtml.replace(new RegExp(content,"gm"),"<font color='black' >"+content+"</font>")
      	 // $(".Aurhorized_tip li:nth-of-type(2)").html(x);
   	 // }

	function formatStr(str){
		if(str.length>9){
			str=str.substring(0,9);
			str+="...";
		}
		return str;
	}

 });
