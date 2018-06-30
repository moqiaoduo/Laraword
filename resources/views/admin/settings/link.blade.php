@extends('admin.layout')

@section('title',__('Read').' - '.__('Settings'))

@section('head')
    <style>
        .laraword-label{font-weight: bold}
        .description {
            margin: .5em 0 0;
            color: #999;
            font-size: .92857em;
        }
    </style>
@endsection

@section('js')
    <script>
        $("#postRoute").on("click",function () {
            $("#custom_post_route").prop("checked",true);
        })
        $("#postRoute").on("input propertychange",function () {
            $("#custom_post_route").attr("value",$(this).val())
        })
    </script>
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
    <form class="form-horizontal" role="form" method="post" action="{{route('admin::setting.save',"link")}}">
        <div class="row">
            @csrf
            <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="laraword-label">自定义文章路径</label>
                        @php
                        switch ($data['routeTable']['post']){
                            case '':
                            case '/archives/{cid}': $selected_post=1;break;
                            case '/archives/{slug}.html': $selected_post=2;break;
                            case '/archives/{year}/{month}/{day}/{slug}.html': $selected_post=3;break;
                            case '/{category}/{slug}.html': $selected_post=4;break;
                            default: $selected_post=5;
                        }
                        @endphp
                        <div class="radio">
                            <label><input type="radio" name="routeTable[post]" checked value="/archives/{cid}">默认风格 /archives/{cid}</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="routeTable[post]" @if($selected_post==2) checked @endif value="/archives/{slug}.html">wordpress风格 /archives/{slug}.html</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="routeTable[post]" @if($selected_post==3) checked @endif value="/archives/{year}/{month}/{day}/{slug}.html">按日期归档 /archives/{year}/{month}/{day}/{slug}.html</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="routeTable[post]" @if($selected_post==4) checked @endif value="/{category}/{slug}.html">按分类归档 /{category}/{slug}.html</label>
                        </div>
                        <div class="radio form-inline">
                            <div class="input-group">
                                <label><input type="radio" id="custom_post_route" name="routeTable[post]" @if($selected_post==5) checked @endif>个性化定义</label>
                                <input type="text" class="form-control" id="postRoute" value="@if($selected_post==5){{$data['routeTable']['post']}}@endif">
                            </div>
                        </div>
                        <span class="description">可用参数: {cid} 文章 ID, {slug} 文章别名, {category} 分类, {year} 年, {month} 月, {day} 日<br>
选择一种合适的文章静态路径风格, 使得你的网站链接更加友好.<br>
一旦你选择了某种链接风格请不要轻易修改它.</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="pagePattern" class="laraword-label">独立页面路径</label>
                        <input type="text" class="form-control" id="pagePattern" name="routeTable[page]"
                               value="{{$data['routeTable']['page']}}">
                        <span class="description">可用参数: {cid} 页面 ID, {slug} 页面缩略名<br>
请在路径中至少包含上述的一项参数.</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="categoryPattern" class="laraword-label">分类路径</label>
                        <input type="text" class="form-control" id="categoryPattern" name="routeTable[category]"
                               value="{{$data['routeTable']['category']}}">
                        <span class="description">可用参数: {mid} 分类 ID, {slug} 分类缩略名<br>
请在路径中至少包含上述的一项参数.</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary">保存</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection