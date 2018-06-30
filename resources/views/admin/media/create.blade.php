@extends('admin.layout')

@section('title',__('Add Media'))

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

        function checkExtAllow(upFileName) {
            var index1=upFileName.lastIndexOf(".");
            var index2=upFileName.length;
            var suffix=upFileName.substring(index1+1,index2);//后缀名
            ext = suffix;
            allowType=JSON.parse('{!! json_encode(explode(",",getSetting('attachmentTypes'))) !!}');
            var allow=false;
            allowType.forEach(function (val,index) {
                if(val.toLowerCase()==ext.toLowerCase()) allow=true;
            })
            return allow;
        }

        //ajax上传文件
        function ajaxUpload(){
            var data=Dragfiles(); //获取formData
            data.append('_token','{{csrf_token()}}')
            upFileName=data.get('file[]').name
            if(!checkExtAllow(upFileName)){data.deleteAll();alert('文件格式不支持');data.deleteAll();return false;}
            $.ajax({
                url: '{{route('admin::upload')}}',
                type: 'POST',
                data: data,
                async: true,
                cache: false,
                contentType:false,
                processData:false,
                dataType: "json",
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
                success: function (json) {
                    $("#progress_bar").hide()
                    data.deleteAll(); //清空formData
                    if(json[0].state=='FAIL'){
                        alert('上传失败！原因：'+json[0].reason)
                    }else window.location.href="../media/"+json[0].id+"/edit";
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
    <li class="breadcrumb-item active">@lang('Add')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('Add')</h1>
            <hr>
        </div>
        <!-- /.col-lg-12 -->
    </div>
        <div class="row">
            <div class="col-12">
                <div class="card" id="drag_upload">
                    <div class="card-body">
                        <ul class="list-group" style="text-align: center">
                            <p>拖放文件到这里<br>或者 <a href="javascript:callUploads();">点击这里上传</a></p>
                            <p>本次只能上传一个文件</p>
                            <div class="progress" id="progress_bar">
                                <div id="progress" class="progress-bar"></div>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <input type="file" id="laraword_upload_files" style="display:none;"/>
@endsection