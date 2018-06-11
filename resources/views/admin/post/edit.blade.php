@extends('admin.layout')

@section('title',__('admin.edit_post'))

@section('head')
    @include($head)
    <style>
        .url-slug{margin-top:-0.5em;color:#AAA;font-size:.92857em;word-break:break-word;}
        #slug{padding:2px;border:none;background:#FFFBCC;color:#666;box-sizing: border-box;line-height: normal;font-size: 100%;}
        .mono{font-family:Menlo,Monaco,Consolas,"Courier New",monospace;}
        .sr-only{border:0;height:1px;margin:-1px;overflow:hidden;padding:0;position:absolute;width:1px;}
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
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">@lang('admin.edit_post')
                <span style="font-size: 15px">
                    @if(count($draft)>0)
                        您正在编辑的是该文章的草稿
                    @endif
                </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">
        <form class="form-horizontal" role="form" method="post" action="{{route('admin::post.update',$data['id'])}}">
            {{ method_field('PUT') }}
            @csrf
            <div class="col-sm-8">
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
            <div class="col-sm-4">

            </div>
        </form>
    </div>
@endsection