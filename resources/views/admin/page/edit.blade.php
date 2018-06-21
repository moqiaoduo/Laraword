@extends('admin.layout')

@section('title',__('admin.edit_page'))

@section('head')
    @include($head)
    <link rel="stylesheet" href="{{asset('css/post.css')}}">
@endsection

@section('js')
    @include($js)
    @include('admin.post')
    <script type="text/javascript" src="{{asset('js/post.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/pages.js')}}"></script>
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
                    @if(!empty($draft))
                        您正在编辑的是该页面的草稿
                    @endif
                </span>
            </h1>
            <hr>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <form class="form-horizontal" role="form" method="post" action="{{route('admin::page.update',$data['cid'])}}">
        <div class="row">
            {{ method_field('PUT') }}
            @csrf
            <input type="hidden" name="category" id="categories">
            <div class="col-md-8 col-xl-9">
                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="title" name="title"
                               placeholder="请输入标题" value="@if(empty($draft)){{$data['title']}}@else{{$draft['title']}}@endif">
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
                            @if(empty($draft))
                                {!! $data['content'] !!}
                            @else
                                {!! $draft['content'] !!}
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