@extends('admin.layout')

@section('title',__('Generic').' - '.__('Settings'))

@section('head')
    <style>
        label{font-weight: bold}
        .description {
            margin: .5em 0 0;
            color: #999;
            font-size: .92857em;
        }
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
                        <label for="name">网站名称</label>
                        <input type="text" class="form-control" id="name" name="env[APP_NAME]"
                               value="{{env('APP_NAME')}}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="url">网站地址</label>
                        <input type="text" class="form-control" id="url" name="env[APP_URL]"
                               value="{{env('APP_URL')}}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="description">站点描述</label>
                        <input type="text" class="form-control" id="description" name="env[APP_DESCRIPTION]"
                               value="{{env('APP_DESCRIPTION')}}">
                        <span class="description">站点描述将显示在网页代码的头部.</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="keyword">关键词</label>
                        <input type="text" class="form-control" id="keyword" name="env[APP_KEYWORD]"
                               value="{{env('APP_KEYWORD')}}">
                        <span class="description">请以半角逗号 "," 分割多个关键字.</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label>是否允许注册</label>
                        <div class="radio">
                            <label class="radio-inline"><input type="radio" @if(!$data['allow_register']) checked @endif name="options[allow_register]" value="0">不允许</label>
                            <label class="radio-inline"><input type="radio" @if($data['allow_register']) checked @endif name="options[allow_register]" value="1">允许</label>
                        </div>
                        <span class="description">允许访问者注册到你的网站, 默认的注册用户不享有任何写入权限.</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="timezone">时区</label>
                        <input type="text" class="form-control" id="timezone" name="env[APP_TIMEZONE]"
                               placeholder="Asia/Shanghai" value="{{env('APP_TIMEZONE')}}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="attachmentTypes">允许上传的文件类型</label>
                        <textarea class="form-control" id="attachmentTypes" name="options[attachmentTypes]" rows="5">{{$data['attachmentTypes']}}</textarea>
                        <span class="description">用逗号 "," 将后缀名隔开, 例如: cpp, h, mak</span>
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