<style>
    /* 半透明的遮罩层 */
    .mask {
        position: absolute; top: 0px; filter: alpha(opacity=60); background-color: #777;
        z-index: 1002; left: 0px;
        opacity:0.5; -moz-opacity:0.5;
    }
</style>
<script>
    //兼容火狐、IE8
    //显示遮罩层
    function showMask(){
        $("#mask").css("height",$(document).height());
        $("#mask").css("width",$(document).width());
        $("#mask").show();
    }
    //隐藏遮罩层
    function hideMask(){

        $("#mask").hide();
    }
    function ajaxUpload() {
        showMask()
        var files = document.getElementById("laraword_upload_files").files; // js 获取文件对象
        if (typeof (files[0]) == "undefined" || files[0].size <= 0) {
            return;
        }
        var formFile = new FormData();

        formFile.append("action", "UploadVMKImagePath");
        for(i=0;i<files.length;i++){
            formFile.append("file[]", files[i]); //加入文件对象
        }
        formFile.append("_token","{{csrf_token()}}");

        var data = formFile;
        $.ajax({
            url: "{{route('admin::upload')}}",
            data: data,
            type: "Post",
            dataType: "json",
            cache: false,//上传文件无需缓存
            processData: false,//用于对data参数进行序列化处理 这里必须false
            contentType: false, //必须
            success: function (result) {
                result.forEach(function(val){
                    addFiles(val)
                    hideMask()
                })
            },
        })
    }
    function addFiles(file) {
        $.post("{{route('getAttachmentInfo')}}",{url:file.url},function (data) {
            uploadFiles.push(data.id)
            $("#laraword_file").val(JSON.stringify(uploadFiles))
            $("#larawordFileList").append('<li file="'+data.id+'" class="list-group-item"><a href="javascript:showFile(\''+file.title+'\',\''+data.filename+'\')">'+file.title+'</a><div class="options"><a href="javascript:delFile(\''+data.id+'\')"><i class="fa fa-trash-o" aria-hidden="true"></i></a></div></li>')
        },"json")
    }
    function delFile(id) {
        $.post("{{route('admin::delFile')}}",{"id":id,"_token":"{{csrf_token()}}"},function(data) {
            $("li[file='"+id+"']").remove()
            uploadFiles.remove(id)
            $("#laraword_file").val(JSON.stringify(uploadFiles))
        })
    }
    function showFile(title,filename) {
        $("#file_title").val(title)
        $.post("{{route('getAttachmentUrl')}}",{filename:filename},function(data){
            updateCode(title,data)
            $("#file_name").val(data)
        })
        $("#insertFile").modal('show')
    }
    $(document).ready(function () {
        @if(!empty($data))
        showMask()
        $.get("{{route('getPAttachment',$data['cid'])}}",function (data) {
            for(i=0;i<data.length;i++){
                addFiles(data[i])
            }
            hideMask()
        });
        @endif
    })
    function copyUrl(selector) {
        var Url=document.getElementById(selector);
        Url.select(); // 选择对象
        document.execCommand("Copy"); // 执行浏览器复制命令
        $("#insertFile").modal('hide')
        alert("复制成功！");
    }
    $(function(){
        //输入框的值改变时触发
        $("#file_title").on("input",function(e){
            //获取input输入的值
            updateCode(e.delegateTarget.value,$("#file_name").val())
        });
    });
    function insertFile() {
        ueditor.focus();
        var html_code=$("#file_insertHTML").html();
        ueditor.execCommand('inserthtml',HtmlUtil.htmlDecode(html_code));
        $("#insertFile").modal('hide')
    }
    function updateCode(title,url) {
        $("#file_insertHTML").html('<a href="'+url+'">['+title+']</a>')
        $("#file_insertMD").html('['+title+']('+url+')')
    }
</script>
<div id="mask" class="mask"></div>
<!-- 模态框 -->
<div class="modal fade" id="insertFile">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">

            <!-- 模态框头部 -->
            <div class="modal-header">
                <h4 class="modal-title">插入附件</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- 模态框主体 -->
            <div class="modal-body">
                <label>
                    标题
                    <input type="text" class="form-control" id="file_title">
                </label>
                <label>
                    插入HTML代码 <a href="javascript:copyUrl('file_insertHTML')">复制</a>
                    <textarea class="form-control" style="width: 100%" id="file_insertHTML" rows="8"></textarea>
                </label>
                <label>
                    插入Markdown代码 <a href="javascript:copyUrl('file_insertMD')">复制</a>
                    <textarea class="form-control" style="width: 100%" id="file_insertMD" rows="6"></textarea>
                </label>
                <p>若您使用UEditor外的编辑器，请手动复制html代码或markdown代码</p>
            </div>

            <input id="file_name" type="hidden">

            <!-- 模态框底部 -->
            <div class="modal-footer">
                @if(env('APP_EDITOR','none')=='ueditor')
                    <a class="btn btn-primary" href="javascript:insertFile();">插入</a>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
            </div>

        </div>
    </div>
</div>