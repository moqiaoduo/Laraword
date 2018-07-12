@extends('content')

@section('subheader')
    <div style="margin-top: 5px;
    margin-bottom: 20px;
    color: #999;
    font-size: 12px;">
        <span class="post-time">发表于 {{$data['created_at']}}</span>
        <span class="post-category">
                                &nbsp; | &nbsp; 分类于 <span>{!! $data['categories'] !!}</span>
                                </span>
        <span class="post-comments-count">
                                &nbsp; | &nbsp; <a rel="nofollow" href="{{getCustomRoute($route,$data).'#comments'}}">暂无评论</a></span>
    </div>
@endsection

@section('comments')
    @include('comment')
@endsection