@foreach($comment as $val)
    <div class="row">
        <div class="col-1"><img src="https://secure.gravatar.com/avatar/"></div>
        <div class="col-11">
            <label class="laraword-label">{{$val['name']}}</label><br>
            <label>{{$val['created_at']}}</label>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <p>{{$val['content']}}</p>
            @if(!empty($val['sub']))
                @include('comments',$val['sub'])
            @endif
        </div>
    </div>
    <br>
@endforeach