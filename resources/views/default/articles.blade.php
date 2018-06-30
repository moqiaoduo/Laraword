@extends('layout')

@section('content')
    <link rel="stylesheet" href="{{theme('css/list.css')}}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if(!empty($category))
                    <h1>{{$category}} 分类下的文章</h1>
                @endif
                @foreach($data as $v)
                    <div class="card">
                        <div class="card-body">
                            <a href="{{getCustomRoute($route,$v)}}"><h2>{{$v['title']}}</h2></a>
                            <div class="post-meta">
                                <span class="post-time">发表于 {{$v['created_at']}}</span>
                                <span class="post-category">
                                &nbsp; | &nbsp; 分类于 <span>{!! $v['categories'] !!}</span>
                                </span>
                                <span class="post-comments-count">
                                &nbsp; | &nbsp; <a rel="nofollow" href="{{getCustomRoute($route,$v).'#comments'}}">暂无评论</a></span>
                            </div>
                            <p>{{mb_strlen($v['content'], 'utf-8') > 150 ? mb_substr($v['content'], 0, 150, 'utf-8').'...' : $v['content']}}</p>
                        </div>
                    </div>
                @endforeach
                {{ $data->links() }}
            </div>
        </div>
    </div>
@endsection