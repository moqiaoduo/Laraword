@extends('admin.layout')

@section('title',__('Edit').$data['name'])

@section("head")
    <style>
        li {list-style-type:none;}
    </style>
@endsection

@section('js')
    @include('editor.none.js')
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('admin::theme.index')}}">@lang('Themes')</a></li>
    <li class="breadcrumb-item active">@lang('Edit Theme')</li>
@endsection

@section('content')
    @if($info!='' && $alert!='')
        @include('admin.alert',['type'=>$alert,'info'=>$info])
    @endif
    <div class="row">
        <div class="col-lg-12">
            <h4>
                @lang('Edit') {{$file}}
                <i style="font-size: 15px;font-weight: bold">
                    @if($file=='theme.json')
                        您最好不要更改它，除非您知道有什么风险
                    @endif
                </i>
            </h4>
            <hr>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <form class="form-horizontal" role="form" method="post" action="{{route('admin::theme.update',[$theme,"file"=>$file,"type"=>$type])}}">
        <div class="row">
            {{ method_field('PUT') }}
            @csrf
            <input type="hidden" name="category" id="categories">
            <div class="col-md-8 col-xl-9">
                <div class="form-group">
                    <div class="col-sm-12">
                        <!-- 加载编辑器的容器 -->
                        @component("editor.none.container")
                            {{$content}}
                        @endcomponent
                    </div>
                </div>
                <div class="form-group">
                    <div style="text-align: right" class="col-sm-12">
                        <button type="submit" class="btn btn-primary" name="submit" value="publish">保存模板</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-3">
                <div class="card">
                    <div class="card-header">@lang('File List')</div>
                    <div class="card-body">
                        <p>静态资源</p>
                        <ul role="assets">
                            @foreach($dir['assets'] as $val)
                                @php($ext=pathinfo($val)['extension'])
                                @if($ext=='css' || $ext=='js')
                                    <li>
                                        <a href="{{route('admin::theme.edit',[$theme,"file"=>$val,"type"=>"assets"])}}">{{$val}}</a>
                                        @switch($val)
                                            @case('css/app.css')
                                            @case('css/style.css')
                                            <br><i style="text-indent:2em;display:block;">主题样式</i> @break
                                        @endswitch
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <p>模板文件</p>
                        <ul role="views">
                            @foreach($dir['views'] as $val)
                                @php($ext=pathinfo($val)['extension'])
                                @if($ext=='php' || $ext=='json')
                                    <li>
                                        <a href="{{route('admin::theme.edit',[$theme,"file"=>$val,"type"=>"views"])}}">{{$val}}</a>
                                        @switch($val)
                                            @case('articles.blade.php')
                                            <br><i style="text-indent:2em;display:block;">文章列表</i> @break
                                            @case('post.blade.php')
                                            <br><i style="text-indent:2em;display:block;">文章内容页</i> @break
                                            @case('layout.blade.php')
                                            <br><i style="text-indent:2em;display:block;">模板布局</i> @break
                                            @case('errors/404.blade.php')
                                            <br><i style="text-indent:2em;display:block;">404错误页</i> @break
                                            @case('pages/default.blade.php')
                                            <br><i style="text-indent:2em;display:block;">默认页面模板</i> @break
                                            @case('theme.json')
                                            <br><i style="text-indent:2em;display:block;">主题信息</i> @break
                                            @case('config.json')
                                            <br><i style="text-indent:2em;display:block;">主题配置</i> @break
                                            @case('comment.blade.php')
                                            <br><i style="text-indent:2em;display:block;">评论模板(单条)</i> @break
                                        @endswitch
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="laraword_file" name="files">
    </form>
    <input type="file" id="laraword_upload_files" style="display:none;" multiple/>
@endsection