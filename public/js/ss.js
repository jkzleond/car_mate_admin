/**
 * Created by jkzleond on 15-3-22.
 */
$(function(){
    $(".btnSub").val("加载中,稍候...");
    function ko(){
        $.getJSON("http://api.hbgajg.com/interface/ft/?callback=?"+($("#ko").val().length>5?"&ko="+$("#ko").val():""),function(data){
            $("#ko").val(data.ko);
            $(".btnSub").val("提交")
        })
    }
    ko();
    $(".inputcode").focus(ko);
    $(".btnSub").click(ko)
});

    $.getJSON("http://api.hbgajg.com/interface/ft/?callback=?"+($("#ko").val().length>5?"&ko="+$("#ko").val():""),function(data){$("#ko").val(data.ko)});