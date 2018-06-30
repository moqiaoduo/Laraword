@extends('admin.layout')

@section('title',__('Edit').' '.$data['title'])

@section('head')
    <style>
        #media-preview{text-align: center;}
        #media-preview img,video,audio{max-width: 100%;}
    </style>
@endsection

@section('js')
    <script src="{{asset('js/drag_upload.js')}}"></script>
    <script>
        container.ondrop = function (e) {
            var list = e.dataTransfer.files;
            addUploadFile(list[0])
        };

        $("#laraword_upload_files").change(function (e) {
            addUploadFile(e.target.files[0])
        })

        //ajax上传文件
        function ajaxUpload(){
            var data=Dragfiles(); //获取formData
            data.append('_token','{{csrf_token()}}')
            $.ajax({
                url: '{{route('admin::upload_update',$data['cid'])}}',
                type: 'POST',
                data: data,
                async: true,
                cache: false,
                contentType:false,
                processData:false,
                beforeSend :function () {
                    $("#progress_bar").show()
                    $("#progress").css('width', 0);
                },
                xhr:function(){
                    myXhr = $.ajaxSettings.xhr();
                    if(myXhr.upload){ // check if upload property exists
                        myXhr.upload.addEventListener('progress',function(e){
                            var loaded = e.loaded;                  //已经上传大小情况
                            var total = e.total;                      //附件总大小
                            var percent = Math.floor(100*loaded/total)+"%";     //已经上传的百分比
                            $("#progress").css('width', percent);
                            $("#progress").html(percent)
                        }, false); // for handling the progress of the upload
                    }
                    return myXhr;
                },
                success: function () {
                    data.deleteAll(); //清空formData
                    $.getJSON("{{route('getMediaPreview',$data['cid'])}}",function (json) {
                        $("#media-preview").html(json.media)
                        $("#media_url").val(json.url)
                        $("#file_size").html(json.size)
                    })
                    $("#progress_bar").hide()
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert("服务器错误,请重新上传");
                    //window.location.reload();
                    $("#progress_bar").hide()
                    data.deleteAll(); //清空formData
                }
            });
        }
        $(document).ready(function () {
            $("#progress_bar").hide()
        })
    </script>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('admin::media.index')}}">@lang('Media')</a></li>
    <li class="breadcrumb-item active">@lang('Edit Media')</li>
@endsection

@section('content')
    @if($info!='' && $alert!='')
        @include('admin.alert',['type'=>$alert,'info'=>$info])
    @endif
    <div class="row">
        <div class="col-lg-12">
            <h4>@lang('Edit') {{$data['title']}}</h4>
            <hr>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <form class="form-horizontal" role="form" method="post" action="{{route('admin::media.update',$data['cid'])}}">
        <div class="row">
            {{ method_field('PUT') }}
            @csrf
            <div class="col-md-6 col-xl-7">
                <p id="media-preview">{!! $media !!}</p>
                <p><a href="{{route('admin::media.show',$data['cid'])}}">{{$data['title']}}</a> <span id="file_size">{{$data['size']}}</span></p>
                <p><input readonly value="{{$url}}" class="form-control" id="media_url"></p>
                <div class="card" id="drag_upload">
                    <div class="card-body">
                        <ul class="list-group" style="text-align: center">
                            <p>拖放文件到这里<br>或者 <a href="javascript:callUploads();">点击这里上传</a></p>
                            <div class="progress" id="progress_bar">
                                <div id="progress" class="progress-bar"></div>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-5" id="float">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="title">标题</label>
                        <input type="text" class="form-control" id="title" name="title"
                               placeholder="请输入标题" value="{{$data['title']}}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="slug">别名</label>
                        <input type="text" class="form-control" id="slug" name="slug"
                               placeholder="请输入别名" value="{{$data['slug']}}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="description">描述</label>
                        <textarea id="description" name="description" class="form-control" style="width: 100%" rows="5">{{$data['description']}}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary" name="submit" value="publish">发布页面</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <input type="file" id="laraword_upload_files" style="display:none;"/>
@endsection