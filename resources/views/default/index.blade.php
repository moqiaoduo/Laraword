@extends('layout')

@section('content')
    <link rel="stylesheet" href="{{theme('css/list.css')}}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @foreach($data as $v)
                    <div class="card">
                        <div class="card-body">
                            <a href="{{getCustomRoute($route,$v)}}"><h1>{{$v['title']}}</h1></a>
                            <div class="post-meta">
                                <span class="post-time">发表于 {{$v['created_at']}}</span>
                                <span class="post-category">
                                &nbsp; | &nbsp; 分类于 <span>
                                        {!! $v['category'] !!}
                                    </span>
                                </span>
                                <span class="post-comments-count">
                                &nbsp; | &nbsp; <a rel="nofollow" href="{{getCustomRoute($route,$v).'#comments'}}">暂无评论</a></span>
                            </div>
                            <p>{{$v['content']}}</p>
                        </div>
                    </div>
                @endforeach
                    {{ $data->links() }}
            </div>
        </div>
    </div>
@endsection
