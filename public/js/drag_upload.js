var container=document.getElementById('drag_upload');

var Dragfiles=(function (){
    var instance;
    return function(){
        if(!instance){
            instance = new FormData();
        }
        return instance;
    }
}());

//为Dragfiles添加一个清空所有文件的方法
FormData.prototype.deleteAll=function () {
    var _this=this;
    this.forEach(function(value,key){
        _this.delete(key);
    })
}
/*拖拽的目标对象------ document 监听drop 并防止浏览器打开客户端的图片*/
document.ondragover = function (e) {
    e.preventDefault();  //只有在ondragover中阻止默认行为才能触发 ondrop 而不是 ondragleave
};
document.ondrop = function (e) {
    e.preventDefault();  //阻止 document.ondrop的默认行为  *** 在新窗口中打开拖进的图片
};
/*拖拽的源对象----- 客户端的一张图片 */
/*拖拽目标对象-----div#container  若图片释放在此元素上方，则需要在其中显示*/

container.ondragover = function (e) {
    e.preventDefault();
};

function addUploadFile(file) {
    var newForm=Dragfiles(); //获取单例
    newForm.append("file[]",file);
    ajaxUpload()
}

function callUploads(){
    document.getElementById("laraword_upload_files").click();
}