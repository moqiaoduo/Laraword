@extends('admin.layout')

@section('title',__('admin.posts'))

@section('js')
    <script>
        $(".table input[name='del[]']").change(function () {
            if(!$(this).is(':checked')) $("#all").prop("checked",false);
            else if(checkAllChecked()) $("#all").prop("checked",true);;
        })
        $("#all").change(function () {
            if($(this).is(':checked')) $(".table input[name='del[]']").prop("checked",true);
            else $(".table input[name='del[]']").prop("checked",false);
        })
        function checkAllChecked() {
            var allChecked=true;
            $(".table input[name='del[]']").each(function () {
                if(!$(this).is(':checked')){allChecked=false;return false;}
            })
            return allChecked;
        }
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">@lang('admin.posts')</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle"
                        data-toggle="dropdown">
                    @lang('admin.selected_item') <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">@lang('admin.delete')</a></li>
                </ul>
            </div>
            <div class="btn-group">
                <a href="{{route('admin::post.create')}}" class="btn btn-success">@lang('admin.new_post')</a>
            </div>
            <table width="100%" class="table">
                <thead>
                <tr>
                    <th>
                        @if(count($data)>1)
                            <input type="checkbox" id="all">
                        @endif
                    </th>
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