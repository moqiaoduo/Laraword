@extends('layout')

@section('content')
    <link rel="stylesheet" href="{{theme('css/list.css')}}">
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
            </div>
        </div>
    </div>
@endsection
