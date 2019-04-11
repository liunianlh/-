$(function(){
	 // $(".left_bot .nav").click(function(){  
      // var iclass = $(this).attr("class");  
      // if (iclass=="current"){  
           // $(this).next(".subnav").hide(); 
      // }else{  
          // $(this).addClass("current").next(".subnav").toggle();
           // $(this).siblings().next(".subnav").removeClass("current").hide();
          // return false;  
      // }  
    // })
	
	//全局订单搜索
	$("#globalOrderBtn").click(function(){
		$("#globalOrderForm").submit();
	});
	
	
	$(".left_bot .nav").click(function(){ 
		var subNav=$(this).next(".subnav");
		var isHide=subNav.hasClass("tohide");
		if(isHide==true){
			$(this).addClass("cursel");
			subNav.removeClass("tohide");
			$(this).parent().siblings("div").find(".subnav").addClass("tohide");
			$(this).parent().siblings("div").find(".nav").removeClass("cursel");
			$(this).parent().siblings("div").find(".subnav").find("a").removeClass("onli");
		}else{
			$(this).removeClass("cursel");
			subNav.addClass("tohide");
			subNav.find("a").removeClass("onli");
		}
    })

	 $(".wrap .right .right_bot .customer_box .bot .table .square:not(:first)").click(function(event){
	    $(this).toggleClass("bg_blue");
	 })
	 $(".wrap .right .right_bot .customer_box .bot .table .square:first").click(function(event){
	    $(".wrap .right .right_bot .customer_box .bot .table li .square:not(:first)").each(function(){
	    	if($(this).hasClass("bg_blue")){
	    		$(this).removeClass("");
	    	}else{
    	  	 	$(this).toggleClass("bg_blue");
    	  	}
//				    	$(this).toggleClass("bg_blue");
	    })
	})
		 var whole=$(document).height();
		var h1=$(".wrap .left .left_top").height();
		var h2=$(".wrap .left .left_bot").height();
		// $(".wrap .left .left_bot").css("margin-bottom",whole-h1-h2+"px");
		// console.log("whole"+whole);
		var h3=$(".wrap .right .right_top").height();
		var h4=$(".customer_box .Customer_top").height();
		$(".customer_box .Customer_bot").css("height",(whole-h3-h4)/1.3+"px");
		var h5=$(".wrap .left").height();
		$(".wrap .right").css("height",h5+"px");
		
	 $(window).resize(function(){
		var whole=$(document).height();
		var h1=$(".wrap .left .left_top").height();
		var h2=$(".wrap .left .left_bot").height();
		$(".wrap .left .left_bot").css("margin-bottom",whole-h1-h2+"px");
		console.log("whole"+whole);
		var h3=$(".wrap .right .right_top").height();
		var h4=$(".customer_box .Customer_top").height();
		$(".customer_box .Customer_bot").css("height",(whole-h3-h4)/1.3+"px");
	});
});