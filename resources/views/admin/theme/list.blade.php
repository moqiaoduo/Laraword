@extends('admin.layout')

@section('title',__('Themes'))

@section('head')
    <style>
        table img{width: 358px;}
    </style>
@endsection

@section('js')
    <script>
        function deleteTheme($slug) {
            if(confirm('{{__('message.ready_to_delete_theme')}}')){
                document.getElementById('delete_'+$slug).submit();
            }
        }
    </script>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">@lang('Themes')</li>
@endsection

@section('content')
    @if($info!='' && $alert!='')
        @include('admin.alert',['type'=>$alert,'info'=>$info])
    @endif
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('Themes')</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <table style="width:100%;" class="table table-responsive">
                <thead>
                <tr>
                    <th width="358px">Preview</th>
                    <th>Detail</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $v)
                    <tr>
                        <td><img src="{{$v['preview']}}" alt="{{$v['slug']}}"></td>
                        <td>
                            <h4>{{$v['name']}}</h4>
                            <p>@lang('Author'): <a href="{{$v['url']}}" target="_blank">{{$v['author']}}</a> 版本: {{$v['ver']}}</p>
                            <p>{{$v['description']}}</p>
                            <p><a href="{{route("admin::theme.edit",$v['slug'])}}">编辑</a> @if($current!=$v['slug'])<a href="{{route("admin::theme.show",$v['slug'])}}">启用</a> <a href="{{route("admin::theme.destroy",$v['slug'])}}" onclick="event.preventDefault();deleteTheme('{{$v['slug']}}');">删除</a>@endif</p>
                        </td>
                    </tr>
                    <form method="post" id="delete_{{$v['slug']}}" action="{{route('admin::theme.destroy',$v['slug'])}}">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection