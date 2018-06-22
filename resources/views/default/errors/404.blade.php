<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404页面</title>
    <base target="_self">
    <style>
        *{
            margin:0;
            padding:0;
            font-family:"微软雅黑";
            color: #34495e;
        }
        .errorpPage-wrap,body{
            width:100%;
            height:100%;
        }
        .errorpPage-box{
            width:800px;
            height:400px;
            position:absolute;
            top:50%;
            margin-top:-200px;
            left:50%;
            margin-left:-400px;
        }
        .errorpPage-tip h3{
            font-size: 24px;
            font-weight: 300;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .errorpPage-tip p{
            margin: 20px 0;
        }
        .errorpPage-operate a{
            margin-left:10px;
            color:#06F;
            text-decoration:none;
        }
        .errorpPage-operate a:first-child{
            margin-left:0!important;
        }
        .errorpPage-img,.errorpPage-tip,.errorpPage-operate{
            text-align:center;
        }
    </style>
</head>
<body >
<div class="errorpPage-wrap">
    <div class="errorpPage-box">
        <div class="errorpPage-img">
        </div>
        <div class="errorpPage-tip">
            <h3>404 Not Found</h3>
        </div>
        <div class="errorpPage-operate">
            <a href="{{route('main')}}">回到首页</a>
            <a href="javascript:window.location.reload()"><span class="glyphicon glyphicon-refresh"></span>刷新试试</a>
            <a href="#" onclick="history.go(-1)"><span class="glyphicon glyphicon-repeat"></span>返回上一页</a>
        </div>

    </div>
</div>
</body>
</html>