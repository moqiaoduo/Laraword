@extends('admin.layout')

@section('title',__('admin.edit_page'))

@section('head')
    @include($head)
    <style>
        .url-slug{margin-top:-0.5em;color:#AAA;font-size:.92857em;word-break:break-word;}
        #slug{padding:2px;border:none;background:#FFFBCC;color:#666;box-sizing: border-box;line-height: normal;font-size: 100%;}
        .mono{font-family:Menlo,Monaco,Consolas,"Courier New",monospace;}
        .sr-only{border:0;height:1px;margin:-1px;overflow:hidden;padding:0;position:absolute;width:1px;}
        #category{overflow-y: auto;}
    </style>
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
    <script>
        $(document).scroll(float)
        $(window).resize(float)
        $(document).ready(float)
        function float() {
            if($(window).width()>=751){
                $("#float .card").css('width',$("#float").width());
                if($(this).scrollTop()>=145){
                    $("#float .card").css('position','fixed');
                    $("#float .card").css('top','60px');
                }else{
                    $("#float .card").css('position','');
                    $("#float .card").css('top','');
                }
            }else{
                $("#float .card").css('width','');
                $("#float .card").css('position','');
                $("#float .card").css('top','');
            }
        }
    </script>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('admin::page.index')}}">@lang('admin.page')</a></li>
    <li class="breadcrumb-item active">@lang('admin.edit_page')</li>
@endsection

@section('content')
    @if($info!='' && $alert!='')
        @include('admin.alert',['type'=>$alert,'info'=>$info])
    @endif
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('admin.edit_page')
                <span style="font-size: 15px">
                    @if(count($draft)>0)
                        您正在编辑的是该页面的草稿
                    @endif
                </span>
            </h1>
            <hr>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <form class="form-horizontal" role="form" method="post" action="{{route('admin::page.update',$data['id'])}}">
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
                        <button type="submit" class="btn btn-primary" name="submit" value="publish">发布页面</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-3" id="float">
            </div>
        </div>
    </form>
@endsection