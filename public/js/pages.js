$(document).scroll(float)
$(window).resize(float)
$(document).ready(float)
function float() {
    if($(window).width()>=751){
        $("#float .category, .Filelist").css('width',$("#float").width());
        if($(this).scrollTop()>=145){
            $("#larawordFileList").css('max-height',$(window).height()-220);
            $("#float .Filelist").css('position','fixed');
            $("#float .Filelist").css('top','80px');
        }else{
            $("#larawordFileList").css('max-height',$(window).height()-300);
            $("#float .Filelist").css('position','');
            $("#float .Filelist").css('top','');
        }
    }else{
        $("#category").css('max-height','');
        $("#float .Filelist").css('width','');
        $("#float .Filelist").css('position','');
        $("#float .Filelist").css('top','');
    }
}