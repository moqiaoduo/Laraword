@extends('admin.layout')

@section('title',__('admin.new_post'))

@section('head')
    @include($head)
    <style>
        .slug{
            height: 20px;
            padding: 2px;
            background: #FFFBCC;
            border: none;
            margin-left: 2px;
        }
    </style>
@endsection

@section('js')
    @include($js)
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">@lang('admin.new_post')</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">
        <form class="form-horizontal" role="form" method="post">
            <div class="col-sm-8">
                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="title"
                               placeholder="请输入标题">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        @if($have_slug)
                        <p>{!! $url !!}</p>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <!-- 加载编辑器的容器 -->
                        @component($editor_container)

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