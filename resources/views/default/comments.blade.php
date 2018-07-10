<hr>
<div class="row">
    <div class="col-1"><img src="https://secure.gravatar.com/avatar/{{md5($comment['email'])}}"></div>
    <div class="col-11">
        <label class="laraword-label">{{$comment['name']}}</label><br>
        <label>{{$comment['created_at']}}</label>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <p>{!! $comment['content'] !!}</p>
        <div class="col-11 offset-1">{!! $sub_comments !!}</div>
    </div>
</div>