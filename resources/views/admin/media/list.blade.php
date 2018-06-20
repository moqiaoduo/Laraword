@extends('admin.layout')

@section('title',__('admin.media'))

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

@section('breadcrumb')
    <li class="breadcrumb-item active">@lang('admin.media')</li>
@endsection

@section('content')
    @if($info!='' && $alert!='')
        @include('admin.alert',['type'=>$alert,'info'=>$info])
    @endif
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('admin.media')</h1>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form action="{{route('admin::media.del')}}" method="post" name="operations">
                @csrf
                <div class="dropdown">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                        @lang('admin.selected_item')
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript:document.operations.submit();">@lang('admin.delete')</a>
                    </div>
                    <a href="{{route('admin::media.create')}}" class="btn btn-success" style="margin-left: 5px;">@lang('admin.new_media')</a>
                </div>
                <div class="row"><br></div>
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
                    <th>@lang('admin.slave')</th>
                    <th>@lang('admin.created_at')</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $v)
                    <tr>
                        <td><input type="checkbox" name="del[]" value="{{$v['id']}}"></td>
                        <td><a href="{{route('admin::post.edit',[$v['id']])}}">{{$v['title']}}</a></td>
                        <td>{{$v['author']}}</td>
                        <td></td>
                        <td>{{$v['created_at']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </form>
            {{$data->links()}}
        </div>
    </div>
@endsection