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
            <h1 class="page-header">@lang('admin.edit_post')</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">
        <form class="form-horizontal" role="form" method="post">
            <div class="col-sm-8">
                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="title"
                               placeholder="请输入标题" value="{{$data['title']}}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        @if($have_slug)
                            <p class="mono url-slug">
                                <label for="slug" class="sr-only">网址缩略名</label>
                                <div class="mono url-slug">{!! $url !!}</div>
                            </p>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <!-- 加载编辑器的容器 -->
                        @component($editor_container)
                            {{$data['content']}}
                        @endcomponent
                    </div>
                </div>
                <div class="form-group">
                    <div style="text-align: right" class="col-sm-12">
                        <button type="submit" class="btn btn-default" name="submit" value="save">保存草稿</button>
                        <button type="submit" class="btn btn-primary" name="submit" value="publish">发布文章</button>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">

            </div>
        </form>
    </div>
@endsection