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

@section('breadcrumb')
    <li class="breadcrumb-item active">@lang('admin.category')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('admin.category')</h1>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form action="{{route('admin::category.del')}}" method="post" name="operations">
                @csrf
                <div class="dropdown">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                        @lang('admin.selected_item')
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript:document.operations.submit();">@lang('admin.delete')</a>
                    </div>
                    <a href="javascript:refresh()" class="btn btn-success" style="margin-left: 5px;">@lang('admin.new_post')</a>
                </div>
                <div class="row"><br></div>
                @if($parent>0)
                    <a href="{{route('admin::category.index',['parent'=>$parent_parent['parent']])}}" class="btn btn-success" style="margin-left: 5px;">&lt; @lang('admin.back')</a><div class="row"><br></div>
                @endif
                <table width="100%" class="table">
                    <thead>
                    <tr>
                        <th>
                            @if(count($data)>1)
                                <input type="checkbox" id="all">
                            @endif
                        </th>
                        <th>@lang('admin.title')</th>
                        <th>@lang('admin.slug')</th>
                        <th>@lang('admin.sub')</th>
                        <th>@lang('admin.category')</th>
                        <th>@lang('admin.created_at')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $v)
                        <tr>
                            <td><input type="checkbox" name="del[]" value="{{$v['id']}}"></td>
                            <td><a href="{{route('admin::post.edit',[$v['id']])}}">{{$v['title']}}</a></td>
                            <td>{{$v['slug']}}</td>
                            <td>
                                @if($v['sub']>0)
                                    <a href="{{route('admin::category.index',['parent'=>$v['id']])}}">查看子分类</a>
                                @endif
                            </td>
                            <td>{{$v['c']}}</td>
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