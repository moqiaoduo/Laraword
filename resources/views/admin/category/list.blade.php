@extends('admin.layout')

@section('title',__('Category'))

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
    {!! $breadcrumb !!}
@endsection

@section('content')
    @if($info!='' && $alert!='')
        @include('admin.alert',['type'=>$alert,'info'=>$info])
    @endif
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('Category')</h1>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form action="{{route('admin::category.del')}}" method="post" name="operations">
                @csrf
                <div class="dropdown">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                        @lang('Selected Items')
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript:document.operations.submit();">@lang('Delete')</a>
                    </div>
                    <a href="{{route('admin::category.create')}}" class="btn btn-success" style="margin-left: 5px;">@lang('Add')</a>
                </div>
                <div class="row"><br></div>
                @if($parent>0)
                    <a href="{{route('admin::category.index',['parent'=>$parent_parent['parent']])}}" class="btn btn-success" style="margin-left: 5px;">&lt; @lang('Back')</a><div class="row"><br></div>
                @endif
                <table style="width:100%;" class="table table-responsive">
                    <thead>
                    <tr>
                        <th>
                            @if(count($data)>1)
                                <input type="checkbox" id="all">
                            @endif
                        </th>
                        <th>@lang('Name')</th>
                        <th>@lang('Slug')</th>
                        <th>@lang('Sub Category')</th>
                        <th>@lang('Posts Count')</th>
                        <th>@lang('Created At')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $v)
                        <tr>
                            <td><input type="checkbox" name="del[]" value="{{$v['mid']}}"></td>
                            <td><a href="{{route('admin::category.edit',[$v['mid']])}}">{{$v['name']}}</a></td>
                            <td>{{$v['slug']}}</td>
                            <td>
                                @if($v['sub']>0)
                                    <a href="{{route('admin::category.index',['parent'=>$v['mid']])}}">查看子分类</a>
                                @else
                                    无
                                @endif
                            </td>
                            <td><a title="点击查看分类文章" href="{{route('admin::category.show',$v['mid'])}}">{{$v['count']}}</a></td>
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