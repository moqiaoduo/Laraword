@extends('admin.layout')

@section('title',__('Comments'))

@section('head')
    <style>
        .laraword-label{font-weight: bold;}
        .comment-options{visibility: hidden;}
        .comment-reply{display: none;}
        .comment-reply textarea{width: 100%;}
    </style>
@endsection

@section('js')
    <script>
        $(".table input[name='del[]']").change(function () {
            if(!$(this).is(':checked')) $("#all").prop("checked",false);
            else if(checkAllChecked()) $("#all").prop("checked",true);;
        })
        $("#all").change(function () {
            if($(this).is(':checked')) $(".table input[name='del[]']").prop("checked",true);
            else $(".table input[name='del[]']").prop("checked",false);
        })
        function checkAllChecked() {
            var allChecked=true;
            $(".table input[name='del[]']").each(function () {
                if(!$(this).is(':checked')){allChecked=false;return false;}
            })
            return allChecked;
        }
        $(document).ready(function(){
            $("tr").hover(function(){
                $(this).children('td').children('.comment-options').css('visibility','visible')
            },function(){
                $(this).children('td').children('.comment-options').css('visibility','hidden')
            });
        });
        function switchReplyWindow(id) {
            reply=$("#comment-"+id).children('td').children('.comment-reply');
            if(reply.is(":hidden")){
                reply.show();    //如果元素为隐藏,则将它显现
            }else{
                reply.hide();     //如果元素为显现,则将其隐藏
            }
        }
        function hideReplyWindow(id) {
            $("#comment-"+id).children('td').children('.comment-reply').hide()
        }

        function showEditWindow(id) {
            url=$("#comment-author-"+id).attr("href");
            $("#comment-"+id).after('<tr id="commit-edit-'+id+'"><td></td><td colspan="2" valign="top"><label>用户名<input class="form-control" id="author-edit-'+id+'" type="text" value="'+$("#comment-author-"+id).html()+'"></label><label>电子邮箱<input class="form-control" id="email-edit-'+id+'" type="text" value="'+$("#comment-email-"+id).html()+'"></label><label>个人主页<input class="form-control" id="url-edit-'+id+'" type="text" value="'+(url==null||url==undefined?'':url)+'"></label></td><td valign="top"><p><label>内容</label><textarea id="content-edit-'+id+'" rows="5" class="form-control" style="width:100%;">'+$("#comment-content-"+id).html()+'</textarea></p><p><button type="button" onclick="postEditComment('+id+')" class="btn btn-primary">提交</button> <button type="button" onclick="hideEditWindow('+id+')" class="btn">取消</button></p></td></tr>');
            $("#comment-"+id).hide()
        }
        
        function hideEditWindow(id) {
            $("#commit-edit-"+id).remove()
            $("#comment-"+id).show()
        }

        function postEditComment(id) {
            $.post("{{route('admin::comment.edit')}}",
                {
                    id:id,
                    author:$("#author-edit-"+id).val(),
                    email:$("#email-edit-"+id).val(),
                    url:$("#url-edit-"+id).val(),
                    content:$("#content-edit-"+id).val(),
                    _token:"{{csrf_token()}}"
                },function (data) {
                    freshComment(data);
            },"json");

        }

        function freshComment(data) {
            $("#comment-author-"+data.id).attr("href",data.url);
            if(data.url===null || data.url===undefined || data.url==='') $("#comment-author-"+data.id).removeAttr("href");
            $("#comment-author-"+data.id).html(data.author)
            $("#comment-content-"+data.id).html(data.content)
            $("#comment-email-"+data.id).attr("href","mailto:"+data.email)
            $("#comment-email-"+data.id).html(data.email)
            $("#comment-avatar-"+data.id).attr("src",data.avatar)
            hideEditWindow(data.id);
        }

        function addReply(id,cid) {
            console.log('starting...')
            $.post("{{route('comment.add')}}",
                {
                    cid:cid,
                    parent:id,
                    content:$("#reply-content-"+id).val(),
                    _token:"{{csrf_token()}}"
                },function (data) {
                console.log('end.')
                    window.location.href="{{route('admin::comment')}}";
                },"json");
        }
    </script>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">@lang('Comments')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('Comments')</h1>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form action="{{route('admin::comment.del')}}" method="post" name="operations">
                @csrf
                <div class="dropdown">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                        @lang('Selected Items')
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript:document.operations.submit();">@lang('Delete')</a>
                    </div>
                </div>
                <div class="row"><br></div>
            <table class="table table-responsive-sm">
                <thead>
                <tr>
                    <th width="15px">
                        @if(count($data)>1)
                            <input type="checkbox" id="all">
                        @endif
                    </th>
                    <th width="60px">@lang('Author')</th>
                    <th width="15%"></th>
                    <th>@lang('Content')</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $v)
                    <tr id="comment-{{$v['id']}}">
                        <td><input type="checkbox" name="del[]" value="{{$v['id']}}"></td>
                        <td><img id="comment-avatar-{{$v['id']}}" src="https://secure.gravatar.com/avatar/{{md5($v['email'])}}?s=40"></td>
                        <td>
                            <span class="laraword-label"><a id="comment-author-{{$v['id']}}" @if(!empty($v['url']))href="{{$v['url']}}@endif">{{$v['name']}}</a></span><br>
                            <span><a id="comment-email-{{$v['id']}}" href="mailto:{{$v['email']}}">{{$v['email']}}</a></span><br>
                            <span>{{$v['ip']}}</span>
                        </td>
                        <td>
                            <p>{{$v['created_at']}} 在 <a href="{{getCustomRoute($route,$v['cid_data'])}}#comment-{{$v['id']}}">{{$v['cid_data']['title']}}</a></p>
                            <p id="comment-content-{{$v['id']}}">{!! $v['content'] !!}</p>
                            <div class="comment-reply">
                                <p><textarea rows="5" class="form-control" id="reply-content-{{$v['id']}}"></textarea></p>
                                <p><button type="button" class="btn btn-primary" onclick="addReply({{$v['id']}},{{$v['cid']}})">回复</button>
                                    <button type="button" onclick="hideReplyWindow({{$v['id']}})" class="btn">取消</button></p>
                            </div>
                            <div class="comment-options">
                                <span><a @if($v['status']!='approved') href="{{route('admin::comment.status',[$v['id'],'approved'])}}" @endif>通过</a></span>
                                <span><a @if($v['status']!='pending') href="{{route('admin::comment.status',[$v['id'],'pending'])}}" @endif>待审核</a></span>
                                <span><a @if($v['status']!='spam') href="{{route('admin::comment.status',[$v['id'],'spam'])}}" @endif>垃圾</a></span>
                                <span><a href="javascript:showEditWindow({{$v['id']}});">编辑</a></span>
                                <span><a href="javascript:switchReplyWindow({{$v['id']}});">回复</a></span>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </form>
            {{$data->links()}}
        </div>
    </div>
@endsection