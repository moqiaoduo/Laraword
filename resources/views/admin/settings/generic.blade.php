@extends('admin.layout')

@section('title',__('Generic').' - '.__('Settings'))

@section('head')
    <style>
        label{font-weight: bold}
    </style>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">@lang('Edit Settings')</li>
@endsection

@section('content')
    @if($info!='' && $alert!='')
        @include('admin.alert',['type'=>$alert,'info'=>$info])
    @endif
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('Edit Settings')</h1>
            <hr>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <form class="form-horizontal" role="form" method="post" action="{{route('admin::setting.save',"generic")}}">
        <div class="row">
            @csrf
            <div class="col-6 offset-3">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="title">网站名称</label>
                        <input type="text" class="form-control" id="title" name="env[APP_NAME]"
                               placeholder="请输入名称" value="{{env('APP_NAME')}}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="title">网站地址</label>
                        <input type="text" class="form-control" id="title" name="env[APP_URL]"
                               placeholder="请输入名称" value="{{env('APP_URL')}}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="title">网站名称</label>
                        <input type="text" class="form-control" id="title" name="title"
                               placeholder="请输入名称" value="">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="title">网站名称</label>
                        <input type="text" class="form-control" id="title" name="title"
                               placeholder="请输入名称" value="">
                    </div>
                </div>
                <div class="form-group">
                    <div style="text-align: right" class="col-sm-12">
                        <button type="submit" class="btn btn-primary">保存</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection