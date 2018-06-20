var category=[];
$(document).scroll(float)
$(window).resize(float)
$(document).ready(float)
function float() {
    if($(window).width()>=751){
        $("#float .category, .Filelist").css('width',$(".float").width());
        if($(this).scrollTop()>=145){
            $("#category, #larawordFileList").css('max-height',$(window).height()/2-180);
            $("#float").css('position','fixed');
            $("#float").css('top','80px');
        }else{
            $("#category, #larawordFileList").css('max-height',$(window).height()/2-200);
            $("#float").css('position','');
            $("#float").css('top','');
        }
    }else{
        $("#category").css('max-height','');
        $("#float .category, .Filelist").css('width','');
        $("#float").css('position','');
        $("#float").css('top','');
    }
}
function getChildNodeIdArr(node) {
    var ts = [];
    if (node.nodes) {
        for (x in node.nodes) {
            ts.push(node.nodes[x].nodeId);
            if (node.nodes[x].nodes) {
                var getNodeDieDai = getChildNodeIdArr(node.nodes[x]);
                for (j in getNodeDieDai) {
                    ts.push(getNodeDieDai[j]);
                }
            }
        }
    } else {
        ts.push(node.nodeId);
    }
    return ts;
}
function setParentNodeCheck(node) {
    var parentNode = $("#category").treeview("getNode", node.parentId);
    if (parentNode.nodes) {
        var checkedCount = 0;
        for (x in parentNode.nodes) {
            if (parentNode.nodes[x].state.checked) {
                checkedCount ++;
            } else {
                break;
            }
        }
        if (checkedCount === parentNode.nodes.length) {
            $("#category").treeview("checkNode", parentNode.nodeId);
            setParentNodeCheck(parentNode);
        }
    }
}