<?php
/**
 * Common Functions
 * Powered by Laraword
 */

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

function modifyEnv(array $data)
{
    $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';
    $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));
    $contentArray->transform(function ($item) use ($data){
        foreach ($data as $key => $value){
            if(str_contains($item, $key)){
                return $key . '=' . $value;
            }
        }
        return $item;
    });
    $content = implode($contentArray->toArray(), "\n");
    File::put($envPath, $content);
}

function getSetting($key='',$default=''){
    if(empty($key)) $val=DB::table('settings')->where('user',0)->get();
    else{
        $val=DB::table('options')->where('name',$key)->get()->toArray();
        if(!empty($val)){
            $val=$val[0];
            $val=$val->value;
        }
    }
    if(empty($val)) return $default;
    return $val;
}

function setSetting($key,$val){
    DB::table('options')->where('name',$key)->delete();
    return DB::table('settings')->insert(['key'=>$key,'val'=>$val]);
}

function theme($file){
    return env('APP_URL').'/theme/'.env('APP_THEME','default').'/'.$file;
}

function getCustomRoute($route,$arr=array()){
    return @str_replace(['{id}','{year}','{month}','{day}','{date}','{slug}','{category}'],[$arr['id'],date("Y",strtotime($arr['created_at'])),date("m",strtotime($arr['created_at'])),date("d",strtotime($arr['created_at'])),date("Ymd",strtotime($arr['created_at'])),$arr['slug'],$arr['category']],$route);
}

/*
 * @param array $routeTable
 * @param string|array $routeName
 * */
function getCustomUri($routeTable,$routeName){
    $routes=[];
    if(is_array($routeName)){
        foreach ($routeName as $val){
            @$route=$routeTable[$val];
            if(empty($route)) $route=getDefaultRoute($val);
            $routes[$val]=$route;
        }
    }else{
        @$routes=$routeTable[$routeName];
        if(empty($routes)) $routes=getDefaultRoute($routeName);
    }
    return $routes;
}

function getDefaultRoute($routeName){
    switch ($routeName){
        case "post": return "/archive/{id}";
        case "page": return "/page/{slug}";
        case "category": return "/category/{slug}";
        case "articleList": return "/articles";
        default: return "/";
    }
}

function getCustomRoutes($routes=array()){
    $tmp=array();
    $route='';
    foreach ($routes as $val){
        $tmp[]=array_filter(explode('/',$val));
    }
    $max=0;
    foreach ($tmp as $val){
        if($max<count($val)) $max=count($val);
    }
    for($i=1;$i<=$max;$i++){
        $route.='/{param'.$i.'?}';
    }
    return $route;
}

function getPostCategory($category){
    if(empty($category) || $category[0]==0) return 'uncategorized';
    return DB::table('category')->find($category[0])->slug;
}

function vendor($file){
    return env('APP_URL').'/vendor/'.$file;
}

// ========== doMoveDir函数 START ==========
function doMoveDir($source,$target)
{
    if(is_dir($source))
    {
        $dest_name=basename($source);
        if(!@mkdir($target.$dest_name))
        {
            return false;
        }
        $d=dir($source);
        while(($entry=$d->read())!==false)
        {
            if(is_dir($source.$entry))
            {
                if($entry=="."||$entry=="..")
                {
                    continue;
                }
                else
                {
                    doMoveDir("$source$entry//","$target$dest_name//");
                }
            }
            else
            {
                if(!copy("$source$entry","$target$dest_name//$entry"))
                {
                    return false;
                }
            }
        }
    }
    else
    {
        if(!copy("$source$entry","$target$dest_name//"))
        {
            return false;
        }
    }
    return true;
}
// ========== doMoveDir函数 END ==========

function delDir($directory){//自定义函数递归的函数整个目录
    if(file_exists($directory)){//判断目录是否存在，如果不存在rmdir()函数会出错
        if($dir_handle=@opendir($directory)){//打开目录返回目录资源，并判断是否成功
            while($filename=readdir($dir_handle)){//遍历目录，读出目录中的文件或文件夹
                if($filename!='.' && $filename!='..'){//一定要排除两个特殊的目录
                    $subFile=$directory."/".$filename;//将目录下的文件与当前目录相连
                    if(is_dir($subFile)){//如果是目录条件则成了
                        delDir($subFile);//递归调用自己删除子目录
                    }
                    if(is_file($subFile)){//如果是文件条件则成立
                        unlink($subFile);//直接删除这个文件
                    }
                }
            }
            closedir($dir_handle);//关闭目录资源
            rmdir($directory);//删除空目录
        }
    }
}

function array_depth($array) {
    $max_depth = 1;
    foreach ($array as $value) {
        if (is_array($value)) {
            $depth = array_depth($value) + 1;


            if ($depth > $max_depth) {
                $max_depth = $depth;
            }
        }
    }
    return $max_depth;
}

function toSize($bytes,$prec=2){
    $rank=0;
    $size=$bytes;
    $unit="B";
    while($size>1024){
        $size=$size/1024;
        $rank++;
    }
    $size=round($size,$prec);
    switch ($rank){
        case "1":
            $unit="KB";
            break;
        case "2":
            $unit="MB";
            break;
        case "3":
            $unit="GB";
            break;
        case "4":
            $unit="TB";
            break;
        default :

    }
    return $size." ".$unit;
}

function installTheme($file){
    $ext=substr(strrchr($file, '.'), 1);
    if(!Storage::disk('theme')->exists($file)) return("文件不存在，无法继续。\nFile doesn't exist, please try another file. \n");
    if($ext!=='zip') return("文件类型不符，无法继续。\nFile extension isn't zip, please try another file. \n");
    $zip = new ZipArchive;
    $zip->open(storage_path('app/theme/'.$file));
    $toDir = storage_path('app/theme/tmp');
    $zip->extractTo($toDir);
    $json=json_decode(Storage::disk('theme')->read('tmp/theme.json'),true);
    if(Storage::disk('theme')->exists('tmp/assets') && Storage::disk('theme')->exists('tmp/views')){
        Storage::disk('theme')->rename('tmp/assets','tmp/'.$json['slug']);
        doMoveDir(storage_path('app/theme/tmp/'.$json['slug'].'/'),public_path('theme/'));
        delDir(storage_path('app/theme/tmp/'.$json['slug'].'/'));
        Storage::disk('theme')->rename('tmp/views','tmp/'.$json['slug']);
        doMoveDir(storage_path('app/theme/tmp/'.$json['slug'].'/'),resource_path('views/'));
        rename(storage_path('app/theme/tmp/theme.json'),resource_path('views/'.$json['slug'].'/theme.json'));
        $rs= "安装成功。";
    }else{
        $rs= "压缩包内目录格式不正确，安装失败。";
    }
    delDir(storage_path('app/theme/tmp/'));
    return $rs;
}