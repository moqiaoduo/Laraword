@extends('admin.layout')

@section('title',__('New Page'))

@section('head')
    @include($head)
    <link rel="stylesheet" href="{{asset('css/post.css')}}">
@endsection

@section('js')
    @include($js)
    @include('admin.post')
    <script type="text/javascript" src="{{asset('js/post.js')}}"></script>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('admin::page.index')}}">@lang('Pages')</a></li>
    <li class="breadcrumb-item active">@lang('New Page')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('New Page')</h1>
            <hr>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <form role="form" method="post" action="{{route('admin::page.store')}}">
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
                        <button type="submit" class="btn btn-primary" name="submit" value="publish">发布页面</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-3">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#tab-options" role="tab" data-toggle="tab">@lang('Options')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tab-media" role="tab" data-toggle="tab">@lang('Media')</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-options">
                        <div class="form-group">
                            <label class="laraword-label">@lang('Published At')</label>
                            <input class="form-control" type="datetime-local" step="1" name="created_at">
                        </div>
                        <div class="form-group">
                            <label class="laraword-label">@lang('Tags') (NO FUNCTION)</label>
                            <input class="form-control" type="text">
                        </div>
                        <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#advanced">@lang('Advanced Options') <i class="fa fa-caret-down" aria-hidden="true"></i></button>
                        <div id="advanced" class="collapse">
                            <div class="form-group">
                                <label class="laraword-label">@lang('Status')</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="publish">公开</option>
                                    <option value="hidden">隐藏</option>
                                    <option value="password">密码保护</option>
                                    <option value="private">私密</option>
                                    <option value="waiting">待审核</option>
                                </select>
                                <input type="hidden" placeholder="内容密码" class="form-control" name="password" id="password">
                            </div>
                            <div class="form-group">
                                <label class="laraword-label">@lang('Authority') (NO FUNCTION)</label>
                                <label class="form-inline"><input type="checkbox">允许评论</label>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab-media">
                        <br>
                        <div class="progress" id="progress_bar">
                            <div id="progress" class="progress-bar"></div>
                        </div>
                        <div id="drag_upload" class="drag_upload">
                            拖放文件到这里<br>或者 <a href="javascript:callUploads();">选择文件上传</a>
                        </div>
                        <ul class="list-group" id="larawordFileList">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="laraword_file" name="files">
    </form>
    <input type="file" id="laraword_upload_files" style="display:none;" multiple/>
@endsection