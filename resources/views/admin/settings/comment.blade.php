@extends('admin.layout')

@section('title',__('Comments').' - '.__('Settings'))

@section('head')
    <style>
        .laraword-label{font-weight: bold}
        .description {
            margin: .5em 0 0;
            color: #999;
            font-size: .92857em;
        }
    </style>
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
    <form class="form-horizontal" role="form" method="post" action="{{route('admin::setting.save',"comment")}}">
        <div class="row">
            @csrf
            <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="commentDateFormat" class="laraword-label">评论日期格式</label>
                        <input type="text" class="form-control" id="commentDateFormat" name="options[commentDateFormat]"
                               placeholder="10" value="">
                        <span class="description">这是一个默认的格式,当你在模板中调用显示评论日期方法时, 如果没有指定日期格式, 将按照此格式输出.<br>
具体写法请参考 <a href="http://www.php.net/manual/zh/function.date.php" target="_blank">PHP 日期格式写法</a>.</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="laraword-label">评论显示</label><br>
                        <label><input type="checkbox" name="commentsShow[]" value="commentsMarkdown">在评论中使用 Markdown 语法</label><br>
                        <label><input type="checkbox" name="commentsShow[]" value="commentsShowUrl">评论者名称显示时自动加上其个人主页链接</label><br>
                        <label><input type="checkbox" name="commentsShow[]" value="commentsUrlNofollow">对评论者个人主页链接使用 <a href="http://en.wikipedia.org/wiki/Nofollow" target="_blank">nofollow 属性</a></label><br>
                        <label><input type="checkbox" name="commentsShow[]" value="commentsAvatar">启用
                            <a href="http://gravatar.com" target="_blank">Gravatar</a> 头像服务, 最高显示评级为 <select name="options[commentsAvatarRating]">
                                <option value="G" selected="true">G - 普通</option>
                                <option value="PG">PG - 13岁以上</option>
                                <option value="R">R - 17岁以上成人</option>
                                <option value="X">X - 限制级</option></select> 的头像</label><br>
                        <label><input type="checkbox" name="commentsShow[]" value="commentsMarkdown">启用分页, 并且每页显示 <input type="text" value="" style="width: 40px;" name="commentsPageSize"> 篇评论, 在列出时将 <select id="commentsShow-commentsPageDisplay" name="commentsPageDisplay">
                                <option value="first">第一页</option>
                                <option value="last">最后一页</option></select> 作为默认显示</label><br>
                        <label><input type="checkbox" name="commentsShow[]" value="commentsMarkdown">启用评论回复, 以 <input name="commentsMaxNestingLevels" type="text" style="width: 40px;" value="">层作为每个评论最多的回复层数</label><br>
                        <label>将 <select name="commentsOrder">
                            <option value="DESC">较新的</option>
                            <option value="ASC">较旧的</option></select> 的评论显示在前面</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="laraword-label">评论显示</label><br>
                        <label><input type="checkbox" name="commentsPost[]" value="commentsRequireModeration">所有评论必须经过审核</label><br>
                        <label><input type="checkbox" name="commentsPost[]" value="commentsWhitelist">评论者之前须有评论通过了审核</label><br>
                        <label><input type="checkbox" name="commentsPost[]" value="commentsRequireMail">必须填写邮箱</label><br>
                        <label><input type="checkbox" name="commentsPost[]" value="commentsRequireURL">必须填写网址</label><br>
                        <label><input type="checkbox" name="commentsPost[]" value="commentsCheckReferer">检查评论来源页 URL 是否与文章链接一致</label><br>
                        <label><input type="checkbox" name="commentsPost[]" value="commentsAntiSpam" disabled>开启反垃圾保护</label><br>
                        <label><input type="checkbox" name="commentsPost[]" value="commentsAutoClose">在文章发布
                            <input type="text" value="" style="width: 40px;" name="commentsPostTimeout">
                            天以后自动关闭评论</label><br>
                        <label><input type="checkbox" name="commentsPost[]" value="commentsMarkdown">同一 IP 发布评论的时间间隔限制为
                            <input type="text" value="" style="width: 40px;" name="commentsPostInterval">
                            分钟</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="attachmentTypes" class="laraword-label">允许使用的HTML标签和属性</label>
                        <textarea class="form-control" id="attachmentTypes" name="options[attachmentTypes]" rows="5" style="width: 100%"></textarea>
                        <span class="description">默认的用户评论不允许填写任何的HTML标签, 你可以在这里填写允许使用的HTML标签.<br>比如: : <code>&lt;a href=""&gt; &lt;img src=""&gt; &lt;blockquote&gt;</code></span>
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