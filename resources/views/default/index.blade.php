@extends('layout')

@section('content')
    <link rel="stylesheet" href="{{theme('css/list.css')}}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @foreach($data as $v)
                    <div class="card">

                        <div class="card-body">
                            <h1>{{$v['title']}}</h1>
                        </div>
                    </div>
                @endforeach

                    {{ $data->links() }}
            </div>

        </div>
    </div>
@endsection
