$(function(){
    layui.use("layer");
    $("#saveEditBtn").click(function(){
        var url=$(this).attr("data-url");
        var id=$(this).attr("data-id");
        var name=$("#name").val();
        var fob=$("#fob").val();


        var reg=/^\s+|\s+$/;
        name=name.replace(reg,"");
        fob=fob.replace(reg,"");


        if(!name){
            return layer.msg("系统设置不能为空");
        }
        if(!fob){
            return layer.msg("FOB不能为空");
        }
        $.ajax({
            url:url,
            type:"post",
            dataType:"json",
            data:{id:id,name:name,fob:fob},
            success:function(res){
                if(res.code==10125){
                    layer.msg(res.msg);
                    location.href=res.url;
                }else{
                    layer.msg(res.msg);
                }
            }
        });
    });

});
