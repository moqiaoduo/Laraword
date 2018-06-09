@extends('layout')

@section('content')
    <link rel="stylesheet" href="{{theme('css/list.css')}}">
    <div class="container">
        <div class="row justify-content-center">
            <h1>{{$data['title']}}</h1>
        </div>
        <div class="row justify-content-center">
            <div class="col-10">
                {!! $data['content'] !!}
            </div>
        </div>
    </div>
@endsection
