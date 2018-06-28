@extends('admin.layout')

@section('title',__('Write Post'))

@section('head')
    @include($head)
    <link rel="stylesheet" href="{{asset('css/post.css')}}">
    <link rel="stylesheet" href="{{vendor('bootstrap/css/glyphicon.css')}}">
    <link rel="stylesheet" href="{{asset('css/bootstrap-treeview.min.css')}}">
@endsection

@section('js')
    @include($js)
    @include('admin.post')
    <script type="text/javascript" src="{{asset('js/bootstrap-treeview.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/post.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/posts.js')}}"></script>
    <script type="text/javascript">
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
                    $("#categories").val(JSON.stringify(category))
                    $('#category').treeview('checkNode',[node.nodeId])
                },
                onNodeUnselected: function(event, node) { //选中节点
                    category.remove(node.id)
                    $("#categories").val(JSON.stringify(category))
                    $('#category').treeview('uncheckNode',[node.nodeId])
                },
            });
        })
    </script>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('admin::post.index')}}">@lang('Posts')</a></li>
    <li class="breadcrumb-item active">@lang('Write Post')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('Write Post')</h1>
            <hr>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <form role="form" method="post" action="{{route('admin::post.store')}}">
        <div class="row">
            @csrf
            <input type="hidden" name="category" id="categories">
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
            <div class="col-md-4 col-xl-3 float">
                <div id="float">
                    <div class="card category">
                        <div class="card-header">@lang('Category')</div>
                        <div class="card-body">
                            <div id="category"></div>
                        </div>
                    </div>
                    <div class="card Filelist" id="drag_upload">
                        <div class="card-header">@lang('Media') <div style="float: right"><a href="javascript:callUploads();"><span class="badge badge-success">添加附件</span></a></div></div>
                        <div class="card-body">
                            <ul class="list-group">
                                <div class="progress" id="progress_bar">
                                    <div id="progress" class="progress-bar"></div>
                                </div>
                                <div id="larawordFileList"></div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="laraword_file" name="files">
    </form>
    <input type="file" id="laraword_upload_files" style="display:none;" multiple/>
@endsection