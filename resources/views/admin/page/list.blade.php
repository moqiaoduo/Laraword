@extends('admin.layout')

@section('title',__('Page'))

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
    <li class="breadcrumb-item active">@lang('Pages')</li>
@endsection

@section('content')
    @if($info!='' && $alert!='')
        @include('admin.alert',['type'=>$alert,'info'=>$info])
    @endif
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('All Pages')</h1>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form action="{{route('admin::page.del')}}" method="post" name="operations">
                @csrf
                <div class="dropdown">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                        @lang('Selected Items')
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript:document.operations.submit();">@lang('Delete')</a>
                    </div>
                    <a href="{{route('admin::page.create')}}" class="btn btn-success" style="margin-left: 5px;">@lang('Add')</a>
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
                    <th>@lang('Title')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Author')</th>
                    <th>@lang('Created At')</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $v)
                    <tr>
                        <td><input type="checkbox" name="del[]" value="{{$v['cid']}}"></td>
                        <td><a href="{{route('admin::page.edit',[$v['cid']])}}">{{$v['title']}}</a></td>
                        <td>
                            @switch($v['status'])
                                @case(0) 已发布 @break
                                @case(1) 未发布 @break
                                @case(2) 隐藏 @break
                                @case(3) 加密 @break
                            @endswitch
                        </td>
                        <td>{{$v['author']}}</td>
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