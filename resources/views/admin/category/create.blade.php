@extends('admin.layout')

@section('title',__('New Category'))

@section('head')
    <style>
        label{font-weight: bold}
    </style>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('admin::category.index')}}">@lang('Category')</a></li>
    <li class="breadcrumb-item active">@lang('New Category')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1>@lang('New Category')</h1>
            <hr>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <form class="form-horizontal" role="form" method="post" action="{{route('admin::category.store')}}">
        <div class="row">
            @csrf
            <div class="col-6 offset-3">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="title">分类名称 *</label>
                        <input type="text" class="form-control" id="title" name="title"
                               placeholder="请输入名称">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="slug">分类别名</label>
                        <input type="text" class="form-control" id="slug" name="slug"
                               placeholder="请输入别名">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="parent">父级分类</label>
                        <select id="parent" name="parent" class="form-control">
                            <option value="0">根目录</option>
                            {!! $parent_options !!}}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="description">分类描述</label>
                        <textarea id="description" name="description" class="form-control" style="width: 100%" rows="5"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div style="text-align: right" class="col-sm-12">
                        <button type="submit" class="btn btn-primary">保存</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection