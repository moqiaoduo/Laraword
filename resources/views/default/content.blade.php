@extends('layout')

@section('content')
    <link rel="stylesheet" href="{{theme('css/list.css')}}">
    <div class="container">
        <div class="row justify-content-center">
            <h1>{{$data['title']}}</h1>
        </div>
    </div>
@endsection
