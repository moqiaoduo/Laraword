@extends('admin.layout')

@section('title',__('admin.posts'))

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">@lang('admin.posts')</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">
        <div class="col-lg-12">
            <table width="100%" class="table">
                <thead>
                <tr>
                    <th><input type="checkbox"></th>
                    <th>@lang('admin.title')</th>
                    <th>@lang('admin.author')</th>
                    <th>@lang('admin.category')</th>
                    <th>@lang('admin.created_at')</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $v)
                    <tr>
                        <td><input type="checkbox" name="del[]" value="{{$v['id']}}"></td>
                        <td><a href="{{route('admin::post.edit',[$v['id']])}}">{{$v['title']}}</a></td>
                        <td>{{$v['author']}}</td>
                        <td>{{$v['c']}}</td>
                        <td>{{$v['created_at']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$data->links()}}
        </div>
    </div>
@endsection