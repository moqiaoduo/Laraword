@extends('admin.layout')

@section('title',__('Generic').' - '.__('Settings'))

@section('head')
    <style>
        label{font-weight: bold}
        .description {
            margin: .5em 0 0;
            color: #999;
            font-size: .92857em;
        }
    </style>
@endsection

@section('js')
    <script>
        $("#articleList").on("click",function () {
            $("#showArticleList").prop("checked",true);
        })
        $("#indexPage").change(function () {
            if($(this).val()>0) $("#articleListItem").show()
            else $("#articleListItem").hide()
        })
    </script>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">@lang('Edit Settings')</li>
@endsection

@section('content')
    @if($info!='' && $alert!='')
        @include('admin.alert',['type'=>$alert,'info'=>$info])
    @endif
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('Edit Settings')</h1>
            <hr>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <form class="form-horizontal" role="form" method="post" action="{{route('admin::setting.save',"read")}}">
        <div class="row">
            @csrf
            <div class="col-6 offset-3">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label>站点首页</label>
                        <div>使用<select name="options[indexPage]" id="indexPage">
                                <option value="0">文章列表</option>
                                @foreach($data['pages'] as $val)
                                    <option @if($data['indexPage']==$val['cid']) selected @endif value="{{$val['cid']}}">{{$val['title']}}</option>
                                @endforeach
                            </select>页面作为首页
                            <label id="articleListItem" style="text-indent:25px;@if(!$data['indexPage']) display:none; @endif"><input type="checkbox" name="options[showArticleList]" @if($data['showArticleList']) checked @endif id="showArticleList">同时将文章列表页路径更改为<input type="text" name="routeTable[articleList]" id="articleList" value="{{$data['routeTable']['articleList']}}"></label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="postsListSize">文章列表数目</label>
                        <input type="text" class="form-control" id="postsListSize" name="options[postsListSize]"
                               placeholder="10" value="{{$data['postsListSize']}}">
                        <span class="description">此数目用于指定显示在侧边栏中的文章列表数目.</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary">保存</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection