@extends('layout')

@section('content')
    <link rel="stylesheet" href="{{theme('css/list.css')}}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @foreach($data as $v)
                    <div class="card">

                        <div class="card-body">
                            <a href="{{getCustomContentRoute($v)}}"><h1>{{$v['title']}}</h1></a>
                            <p>{{$v['content']}}</p>
                        </div>
                    </div>
                @endforeach
                    {{ $data->links() }}
            </div>

        </div>
    </div>
@endsection
