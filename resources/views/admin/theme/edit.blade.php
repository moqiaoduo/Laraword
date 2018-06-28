@extends('admin.layout')

@section('title',__('Edit').$data['name'])

@section("head")
    <style>
        li {list-style-type:none;}
    </style>
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
            <h4>@lang('Edit') {{$file}}</h4>
            <hr>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <form class="form-horizontal" role="form" method="post" action="{{route('admin::theme.update',$theme)}}">
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
                                <li>{{$val}}</li>
                            @endforeach
                        </ul>
                        <p>模板文件</p>
                        <ul role="views">
                            @foreach($dir['views'] as $val)
                                <li>{{$val}}</li>
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