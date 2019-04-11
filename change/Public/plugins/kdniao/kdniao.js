var kdniao={
	init:function(){
		/*加载样式表*/
		var url='/tonetron/Public/plugins/kdniao/skin/kdniao.css?'+new Date().getTime();
		var link = document.createElement("link");
		link.rel = "stylesheet";
		link.type = "text/css";
		link.href = url;
		document.getElementsByTagName("head")[0].appendChild(link);
		var csstype="flo";
		/*
		fix 固定  flo 浮动
		默认浮动,不需要在页面添加容器
		使用固定的方式在页面容器中指定class="fix"
		*/
		var cont=$("#queryContext");
		if(cont.length<1)
		{
			$(document.body).append('<div id="queryContext" class="'+csstype+'"></div>'); 
			cont=$("#queryContext");
		}
		if($("#queryContextbg").length<1)
		{
			$(document.body).append('<div id="queryContextbg"></div>'); 
		}
		cont.hide();
		$("#queryContextbg").hide();
	},
	query:function(num,wltype){
		$.ajax({
			url:urlKD, 
			type:"post",
			dataType:"json",
			data:{nu:num,com:wltype}, 
			success:function(result){
				var result=result.msg.Traces;
				var uls='';
				for(var i=0,j=result.length;i<j;i++){
					var temp=result[i];
					uls+='<ul class="queryItem"><li>'+temp.AcceptTime+'</li><li>'+temp.AcceptStation+'</li></ul>'
				}
				$("#queryContext").find(".queryItem").remove();
				$("#sanjiao").before(uls);
			}
		})
		
	},
	close:function(){
		$("#queryContext").hide();
		$("#queryContextbg").hide();
	}
};
kdniao.init();


//---------------------------------------------------  
// 日期格式化  
// 格式 YYYY/yyyy/YY/yy 表示年份  
// MM/M 月份  
// W/w 星期  
// dd/DD/d/D 日期  
// hh/HH/h/H 时间  
// mm/m 分钟  
// ss/SS/s/S 秒  
//---------------------------------------------------  
Date.prototype.Format = function(formatStr)   
{   
    var str = formatStr;   
    var Week = ['日','一','二','三','四','五','六'];  
  
    str=str.replace(/yyyy|YYYY/,this.getFullYear());   
    str=str.replace(/yy|YY/,(this.getYear() % 100)>9?(this.getYear() % 100).toString():'0' + (this.getYear() % 100));   
  
    str=str.replace(/MM/,(this.getMonth()+1)>9?this.getMonth().toString():'0' + (this.getMonth()+1));   
    str=str.replace(/M/g,(this.getMonth()+1));   
  
    str=str.replace(/w|W/g,Week[this.getDay()]);   
  
    str=str.replace(/dd|DD/,this.getDate()>9?this.getDate().toString():'0' + this.getDate());   
    str=str.replace(/d|D/g,this.getDate());   
  
    str=str.replace(/hh|HH/,this.getHours()>9?this.getHours().toString():'0' + this.getHours());   
    str=str.replace(/h|H/g,this.getHours());   
    str=str.replace(/mm/,this.getMinutes()>9?this.getMinutes().toString():'0' + this.getMinutes());   
    str=str.replace(/m/g,this.getMinutes());   
  
    str=str.replace(/ss|SS/,this.getSeconds()>9?this.getSeconds().toString():'0' + this.getSeconds());   
    str=str.replace(/s|S/g,this.getSeconds());   
  
    return str;   
}   