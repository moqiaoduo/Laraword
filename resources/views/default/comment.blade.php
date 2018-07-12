@if(!$comments->isEmpty())
    <div class="row justify-content-center comments-laraword" style="margin-top: 40px;">
        <div class="col-10">
            <h4>评论</h4>
            <div id="comment-0"></div>
        </div>
    </div>
@endif
<form method="post" action="{{route('comment.add')}}" id="comment-post">
    <div class="row justify-content-center" style="margin-top: 40px;">
        <div class="col-10" id="comment-width">
            <h4 class="comment-title">添加新评论 <span id="comment-reply-cancel"><a href="javascript:reply(0)">取消回复</a></span></h4>
            <hr>
            <textarea rows="8" id="comment-content" name="content" class="form-control" style="width: 100%"></textarea>
            <div style="max-width: 400px;margin-top: 20px;">
                @csrf
                @guest
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="{{__('Nick Name')}}" name="author">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-fw fa-envelope"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="{{__('E-Mail Address')}}" name="email">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-fw fa-globe"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="{{__('Your Website')}} ({{__('Optional')}})" name="url">
                    </div>
                @else
                    <p>登录身份: {{ Auth::user()->name }}. <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">退出 »</a></p>
                @endguest
                <input type="submit" name="comment_submit" value="提交评论" class="btn btn-primary" style="width: 100%">
                <input type="hidden" name="redirect" value="{{getCustomRoute($route,$data)}}">
                <input type="hidden" name="cid" value="{{$data['cid']}}">
                <input type="hidden" name="parent" value="0">
            </div>
        </div>
    </div>
</form>

<script>
    var page=1;var perPage=10;
    $(document).ready(function () {
        getComments()
    })
    function getComments(parent) {
        var url='';
        if(parent!=null || parent!=undefined) url='/'+parent;
        $.get("{{route('getComments')}}",{cid:{{$data['cid']}},page:page,perpage:perPage},function (data) {
            $("#comment-0").html(data)
        })
    }
    function switchPage(this_page) {
        page=this_page
        if(this_page<1) page=1;
        if(this_page>pageCount) page=pageCount;
        getComments(0);
    }
    function reply(id) {
        $("input[name='parent']").val(id)
        $("#comment-reply-cancel").css("display","inline")
        $("#comment-"+id).after($("#comment-post"))
        $("#comment-width").attr("class","col-12")
        if(id==0){$("#comment-reply-cancel").css("display","none")}
        $("#comment-content").focus()
    }
</script>