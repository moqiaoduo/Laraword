@extends('layout')

@section('content')
    <link rel="stylesheet" href="{{theme('css/content.css')}}">
    <link rel="stylesheet" href="{{theme('css/font-awesome.min.css')}}">
    <div class="container">
        <div class="row justify-content-center">
            <h1>{{$data['title']}}</h1>
        </div>
        <div class="row justify-content-center">
            <div class="col-10">
                @if(env('APP_SHOW')=='markdown')
                    <div id="doc-content">
                        <textarea style="display:none;">{!! $data['content'] !!}</textarea>
                    </div>
                    <script src="/vendor/editormd/js/editormd.js"></script>
                    <script src="/vendor/editormd/lib/marked.min.js"></script>
                    <script src="/vendor/editormd/lib/prettify.min.js"></script>
                    <script src="/vendor/editormd/lib/raphael.min.js"></script>
                    <script src="/vendor/editormd/lib/underscore.min.js"></script>
                    <script src="/vendor/editormd/lib/sequence-diagram.min.js"></script>
                    <script src="/vendor/editormd/lib/flowchart.min.js"></script>
                    <script src="/vendor/editormd/lib/jquery.flowchart.min.js"></script>
                    <script type="text/javascript">
                        var testEditor;
                        $(function () {
                            testEditor = editormd.markdownToHTML("doc-content", {//注意：这里是上面DIV的id
                                htmlDecode: "style,script,iframe",
                                emoji: true,
                                taskList: true,
                                tex: true, // 默认不解析
                                flowChart: true, // 默认不解析
                                sequenceDiagram: true, // 默认不解析
                                codeFold: true,
                            });});
                    </script>
                @else
                    {!! $data['content'] !!}
                @endif

                @php
                    $prev=0;$next=0;
                    if($data['type']=='post'){
                    $prev=\App\Content::where('type','post')->where('cid','<',$data['cid'])->max('cid');
                    $next=\App\Content::where('type','post')->where('cid','>',$data['cid'])->min('cid');
                    if($prev>0){
                    $prev_data=\App\Content::find($prev);
                    $prev_data['category']=\App\Content::find($prev)->contentMeta()->first()['slug'];
                    }
                    if($next>0){
                    $next_data=\App\Content::find($next);
                    $next_data['category']=\App\Content::find($next)->contentMeta()->first()['slug'];
                    }
                    }
                @endphp
                    <div style="height: 40px;"></div>
                    <hr>
                    <div class="row">
                        <div class="col-6">@if($prev>0)<a href="{{getCustomRoute($route,$prev_data)}}">&laquo; 上一篇：{{$prev_data['title']}}</a>@endif</div>
                        <div class="col-6" style="text-align: right">@if($next>0)<a href="{{getCustomRoute($route,$next_data)}}">下一篇：{{$next_data['title']}} &raquo;</a>@endif</div>
                    </div>
            </div>
        </div>
        <div class="row justify-content-center" style="margin-top: 40px;">
            <div class="col-10">
                <h4>评论</h4>
                <hr>
                @include('comments')
            </div>
        </div>
        <div class="row justify-content-center" style="margin-top: 40px;">
            <div class="col-10">
                <h4>添加新评论</h4>
                <hr>
                <textarea rows="8" name="comment" class="form-control" style="width: 100%"></textarea>
                <div style="max-width: 400px;margin-top: 20px;">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="{{__('Nick Name')}}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-fw fa-envelope"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="{{__('E-Mail Address')}}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-fw fa-globe"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="{{__('Your Website')}} ({{__('Optional')}})">
                    </div>
                    <input type="submit" name="comment_submit" value="提交评论" class="btn btn-primary" style="width: 100%">
                </div>
            </div>
        </div>
    </div>
@endsection
