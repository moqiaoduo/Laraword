@extends('admin.layout')

@section('title',__('admin.new_post'))

@section('head')
    @include($head)
    <style>
        .url-slug{margin-top:-0.5em;color:#AAA;font-size:.92857em;word-break:break-word;}
        #slug{padding:2px;border:none;background:#FFFBCC;color:#666;box-sizing: border-box;line-height: normal;font-size: 100%;}
        .mono{font-family:Menlo,Monaco,Consolas,"Courier New",monospace;}
        .sr-only{border:0;height:1px;margin:-1px;overflow:hidden;padding:0;position:absolute;width:1px;}
        #category,#larawordFileList{overflow-y: auto;}
        .list-group-item .options{float: right}
    </style>
    <link rel="stylesheet" href="{{vendor('bootstrap/css/glyphicon.css')}}">
    <link rel="stylesheet" href="{{asset('css/bootstrap-treeview.min.css')}}">
@endsection

@section('js')
    @include($js)
    <script type="text/javascript">
        $(function(){
            $('#slug').bind('input propertychange',function(){
                $("#preview").html($(this).val())
            });
        })
    </script>
    <script type="text/javascript" src="{{asset('js/bootstrap-treeview.min.js')}}"></script>
    <script type="text/javascript">
        var category=[];var uploadFiles=[];
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
        $.getJSON("{{url('/api/category')}}",function (data) {
            $('#category').treeview({
                data: data,
                showCheckbox: true,
                multiSelect: true,
                onNodeChecked: function(event, node) { //选中节点
                    $('#category').treeview('selectNode',[node.nodeId])
                },
                onNodeUnchecked: function(event, node) { //取消选中节点
                    $('#category').treeview('unselectNode',[node.nodeId])
                },
                onNodeSelected: function(event, node) { //选中节点
                    category.push(node.id)
                    $('#category').treeview('checkNode',[node.nodeId])
                },
                onNodeUnselected: function(event, node) { //选中节点
                    category.remove(node.id)
                    $('#category').treeview('uncheckNode',[node.nodeId])
                },
            });
        })
        Array.prototype.indexOf = function(val) {
            for (var i = 0; i < this.length; i++) {
                if (this[i] == val) return i;
            }
            return -1;
        };
        Array.prototype.remove = function(val) {
            var index = this.indexOf(val);
            if (index > -1) {
                this.splice(index, 1);
            }
        };
    </script>
    <script>
        $(document).scroll(float)
        $(window).resize(float)
        $(document).ready(float)
        function float() {
            if($(window).width()>=751){
                $("#float .category, .Filelist").css('width',$("#float").width());
                if($(this).scrollTop()>=145){
                    $("#category, #larawordFileList").css('max-height',$(window).height()/2-180);
                    $("#float .category, .Filelist").css('position','fixed');
                    $("#float .category").css('top','80px');
                    $("#float .Filelist").css('top',($(window).height()/2)+'px');
                }else{
                    $("#category, #larawordFileList").css('max-height',$(window).height()/2-200);
                    $("#float .category, .Filelist").css('position','');
                    $("#float .category, .Filelist").css('top','');
                }
            }else{
                $("#category").css('max-height','');
                $("#float .category, .Filelist").css('width','');
                $("#float .category, .Filelist").css('position','');
                $("#float .category, .Filelist").css('top','');
            }
        }
        function addFiles(file) {
            $.post("{{route('basename')}}",{url:file.url},function (data) {
                uploadFiles.push(data)
                $("#laraword_file").val(JSON.stringify(uploadFiles))
                $("#larawordFileList").append('<li file="'+data+'" class="list-group-item">'+file.title+'<div class="options"><a href="javascript:delFile(\''+data+'\')"><i class="fa fa-trash-o" aria-hidden="true"></i></a></div></li>')
            })
        }
        function delFile(fileName) {
            $.post("{{route('admin::delFile')}}",{"filename":fileName,"_token":"{{csrf_token()}}"},function(data) {
                $("li[file='"+fileName+"']").remove()
                uploadFiles.remove(fileName)
                $("#laraword_file").val(JSON.stringify(uploadFiles))
            })
        }
        function callUploads(){
            document.getElementById("laraword_upload_files").click();
        }
        $("#laraword_upload_files").change(function () {
            ajaxUpload()
        })
        function ajaxUpload() {
            var files = document.getElementById("laraword_upload_files").files; // js 获取文件对象
            if (typeof (files[0]) == "undefined" || files[0].size <= 0) {
                return;
            }
            var formFile = new FormData();
            //console.log(files)
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
                    })
                },
            })
        }
    </script>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('admin::post.index')}}">@lang('admin.posts')</a></li>
    <li class="breadcrumb-item active">@lang('admin.new_post')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('admin.new_post')</h1>
            <hr>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <form role="form" method="post" action="{{route('admin::post.store')}}">
        <div class="row">
            @csrf
            <div class="col-md-8 col-xl-9">
                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="title" name="title"
                               placeholder="请输入标题">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <p class="mono url-slug">
                            <label for="slug" class="sr-only">网址缩略名</label>
                            <div class="mono url-slug">{!! $url !!}</div>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12" id="editor">
                        <!-- 加载编辑器的容器 -->
                        @component($editor_container)

                        @endcomponent
                    </div>
                </div>
                <div class="form-group">
                    <div style="text-align: right" class="col-sm-12">
                        <button type="submit" class="btn btn-default" name="submit" value="save">保存但不发布</button>
                        <button type="submit" class="btn btn-primary" name="submit" value="publish">发布文章</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-3" id="float">
                <div class="card category">
                    <div class="card-header">@lang('admin.category')</div>
                    <div class="card-body">
                        <div id="category"></div>
                    </div>
                </div>
                <div class="card Filelist">
                    <div class="card-header">@lang('admin.media') <div style="float: right"><a href="javascript:callUploads();"><span class="badge badge-success">添加附件</span></a></div></div>
                    <div class="card-body">
                        <ul class="list-group">
                            <div id="larawordFileList"></div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="laraword_file" name="files">
    </form>
    <input type="file" id="laraword_upload_files" style="display:none;" multiple/>
@endsection