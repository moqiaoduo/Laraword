@extends('admin.layout')

@section('title',__('admin.edit_post'))

@section('head')
    @include($head)
    <style>
        .url-slug{margin-top:-0.5em;color:#AAA;font-size:.92857em;word-break:break-word;}
        #slug{padding:2px;border:none;background:#FFFBCC;color:#666;box-sizing: border-box;line-height: normal;font-size: 100%;}
        .mono{font-family:Menlo,Monaco,Consolas,"Courier New",monospace;}
        .sr-only{border:0;height:1px;margin:-1px;overflow:hidden;padding:0;position:absolute;width:1px;}
        #category{overflow-y: auto;}
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
        var category=[];
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
        $.getJSON("{{url('/api/category')}}",{selected:
            @php
                print_r(json_encode($data['category']))
            @endphp
        },function (data) {
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
            $('#category').treeview('getSelected').forEach(function (value, index, arr) {
                category.push(value.id)
            })
            $("#categories").val(JSON.stringify(category))
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
                $("#float .card").css('width',$("#float").width());
                if($(this).scrollTop()>=145){
                    $("#category").css('max-height',$(window).height()/2-100);
                    $("#float .card").css('position','fixed');
                    $("#float .card").css('top','60px');
                }else{
                    $("#category").css('max-height',$(window).height()/2-160);
                    $("#float .card").css('position','');
                    $("#float .card").css('top','');
                }
            }else{
                $("#category").css('max-height','');
                $("#float .card").css('width','');
                $("#float .card").css('position','');
                $("#float .card").css('top','');
            }
        }
    </script>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('admin::post.index')}}">@lang('admin.posts')</a></li>
    <li class="breadcrumb-item active">@lang('admin.edit_post')</li>
@endsection

@section('content')
    @if($info!='' && $alert!='')
        @include('admin.alert',['type'=>$alert,'info'=>$info])
    @endif
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('admin.edit_post')
                <span style="font-size: 15px">
                    @if(count($draft)>0)
                        您正在编辑的是该文章的草稿
                    @endif
                </span>
            </h1>
            <hr>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <form class="form-horizontal" role="form" method="post" action="{{route('admin::post.update',$data['id'])}}">
        <div class="row">
            {{ method_field('PUT') }}
            @csrf
            <input type="hidden" name="category" id="categories">
            <div class="col-md-8 col-xl-9">
                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="title" name="title"
                               placeholder="请输入标题" value="@if(count($draft)>0){{$draft[0]['title']}}@else{{$data['title']}}@endif">
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
                    <div class="col-sm-12">
                        <!-- 加载编辑器的容器 -->
                        @component($editor_container)
                            @if(count($draft)>0)
                                {!! $draft[0]['content'] !!}
                            @else
                                {!! $data['content'] !!}
                            @endif
                        @endcomponent
                    </div>
                </div>
                <div class="form-group">
                    <div style="text-align: right" class="col-sm-12">
                        <button type="submit" class="btn btn-default" name="submit" value="save">
                            @if($data['status']==0)
                                保存草稿
                            @else
                            保存但不发布
                            @endif
                        </button>
                        <button type="submit" class="btn btn-primary" name="submit" value="publish">发布文章</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-3" id="float">
                <div class="card">
                    <div class="card-header">@lang('admin.category')</div>
                    <div class="card-body">
                        <div id="category"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection