<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') - {{config('app.name','Laraword')}} - Laraword</title>
    <!-- Bootstrap core CSS-->
    <link href="{{vendor('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="{{vendor('font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="{{asset('css/sb-admin.css')}}" rel="stylesheet">
    <script>
        var browser = {
            versions: function() {
                var u = navigator.userAgent, app = navigator.appVersion;
                return {     //移动终端浏览器版本信息
                    trident: u.indexOf('Trident') > -1, //IE内核
                    presto: u.indexOf('Presto') > -1, //opera内核
                    webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
                    gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核
                    mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
                    ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
                    android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或uc浏览器
                    iPhone: u.indexOf('iPhone') > -1, //是否为iPhone或者QQHD浏览器
                    iPad: u.indexOf('iPad') > -1, //是否iPad
                    webApp: u.indexOf('Safari') == -1 //是否web应该程序，没有头部与底部
                };
            } (),
            language: (navigator.browserLanguage || navigator.language).toLowerCase()
        }
    </script>
    @yield('head')
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="{{route('admin::index')}}">{{config('app.name','Laraword')}}</a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="@lang('Dashboard')">
                <a class="nav-link" href="{{route('admin::index')}}">
                    <i class="fa fa-fw fa-dashboard"></i>
                    <span class="nav-link-text">@lang('Dashboard')</span>
                </a>
            </li>
            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="@lang('Posts')">
                <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#posts" data-parent="#posts">
                    <i class="fa fa-fw fa-book"></i>
                    <span class="nav-link-text">@lang('Posts')</span>
                </a>
                <ul class="sidenav-second-level collapse" id="posts">
                    <li>
                        <a href="{{route('admin::post.index')}}">@lang('All Posts')</a>
                    </li>
                    <li>
                        <a href="{{route('admin::post.create')}}">@lang('Write Post')</a>
                    </li>
                    <li>
                        <a href="{{route('admin::category.index')}}">@lang('Category')</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="@lang('Media')">
                <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#media" data-parent="#media">
                    <i class="fa fa-fw fa-medium"></i>
                    <span class="nav-link-text">@lang('Media')</span>
                </a>
                <ul class="sidenav-second-level collapse" id="media">
                    <li>
                        <a href="{{route('admin::media.index')}}">@lang('Media Library')</a>
                    </li>
                    <li>
                        <a href="{{route('admin::media.create')}}">@lang('Add')</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="@lang('Pages')">
                <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#page" data-parent="#page">
                    <i class="fa fa-fw fa-file"></i>
                    <span class="nav-link-text">@lang('Pages')</span>
                </a>
                <ul class="sidenav-second-level collapse" id="page">
                    <li>
                        <a href="{{route('admin::page.index')}}">@lang('All Pages')</a>
                    </li>
                    <li>
                        <a href="{{route('admin::page.create')}}">@lang('New Page')</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="@lang('Comments')">
                <a class="nav-link" href="{{route('admin::comment')}}">
                    <i class="fa fa-fw fa-comments"></i>
                    <span class="nav-link-text">@lang('Comments')</span>
                </a>
            </li>
            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="@lang('Themes')">
                <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#theme" data-parent="#theme">
                    <i class="fa fa-fw fa-file"></i>
                    <span class="nav-link-text">@lang('Themes')</span>
                </a>
                <ul class="sidenav-second-level collapse" id="theme">
                    <li>
                        <a href="{{route('admin::theme.index')}}">@lang('Themes')</a>
                    </li>
                    <li>
                        <a href="{{route('admin::theme.edit',env('APP_THEME','default'))}}">@lang('Edit Current Theme')</a>
                    </li>
                    <li>
                        <a href="{{route('admin::theme.create')}}">@lang('Add')</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="@lang('Settings')">
                <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#settings" data-parent="#settings">
                    <i class="fa fa-fw fa-wrench"></i>
                    <span class="nav-link-text">@lang('Settings')</span>
                </a>
                <ul class="sidenav-second-level collapse" id="settings">
                    <li>
                        <a href="{{route('admin::setting','generic')}}">@lang('Generic')</a>
                    </li>
                    <li>
                        <a href="{{route('admin::setting','read')}}">@lang('Read')</a>
                    </li>
                    <li>
                        <a href="{{route('admin::setting','comment')}}">@lang('Comments')</a>
                    </li>
                    <li>
                        <a href="{{route('admin::setting','link')}}">@lang('Links')</a>
                    </li>
                </ul>
            </li>
        </ul>
        <ul class="navbar-nav sidenav-toggler">
            <li class="nav-item">
                <a class="nav-link text-center" id="sidenavToggler">
                    <i class="fa fa-fw fa-angle-left"></i>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <img src="https://secure.gravatar.com/avatar/{{md5(Auth::user()->email)}}?s=20">
                    {{Auth::user()->name}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('main')}}" target="_blank">{{ __('Home') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="modal" data-target="#logoutModal">
                    <i class="fa fa-fw fa-sign-out"></i>{{ __('Logout') }}</a>
            </li>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </ul>
    </div>
</nav>
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('admin::index')}}">@lang('Dashboard')</a>
            </li>
            @yield('breadcrumb')
        </ol>
        @yield('content')
    </div>
    <!-- /.container-fluid-->
    <!-- /.content-wrapper-->
    <footer class="sticky-footer">
        <div class="container">
            <div class="text-center">
                <small>Copyright © {{config('app.name','Laraword')}} 2018 | Powered by <a target="_blank" href="https://github.com/moqiaoduo/Laraword">Laraword</a></small>
            </div>
        </div>
    </footer>
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fa fa-angle-up"></i>
    </a>
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('Logout')</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">@lang('message.ready_to_logout')</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">@lang('Cancel')</button>
                    <a class="btn btn-primary" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="{{vendor('jquery/jquery.min.js')}}"></script>
    <script src="{{vendor('bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{vendor('jquery-easing/jquery.easing.min.js')}}"></script>
    <!-- Page level plugin JavaScript-->
    <script src="{{vendor('chart.js/Chart.min.js')}}"></script>
    <script src="{{vendor('datatables/jquery.dataTables.js')}}"></script>
    <script src="{{vendor('datatables/dataTables.bootstrap4.js')}}"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{asset('js/sb-admin.min.js')}}"></script>

    @yield('js')

    <script>
        var nav_selected=false;
        $('#mainNav').find('a').each(function () {
            if (this.href == document.location.origin+document.location.pathname) {
                nav_selected=true;
                $(this).parent().parent().parent().children('a').attr('aria-expanded',true);
                $(this).parent().parent().parent().children('a').removeClass('collapsed');
                $(this).parent().parent().addClass('show');
                $(this).parent().addClass('active'); // this.className = 'active';
            }
        });
        if(!nav_selected){
            $('#mainNav').find('a').each(function () {
                if (document.location.href.search(this.href) >= 0 && this.href!='{{route('admin::index')}}' && !nav_selected) {
                    nav_selected=true;
                    $(this).parent().parent().parent().children('a').attr('aria-expanded',true);
                    $(this).parent().parent().parent().children('a').removeClass('collapsed');
                    $(this).parent().parent().addClass('show');
                    $(this).parent().addClass('active'); // this.className = 'active';
                }
            });
        }
    </script>
</div>
</body>

</html>
