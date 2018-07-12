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

function setSetting($arr){
    foreach ($arr as $key=>$val){
        DB::table('options')->where('name',$key)->delete();
        $bool=DB::table('options')->insert(['name'=>$key,'value'=>$val]);
        if(!$bool) return false;
    }
    return true;
}

function theme($file){
    return env('APP_URL').'/theme/'.env('APP_THEME','default').'/'.$file;
}

function getCustomRoute($route,$arr=array()){
    return @str_replace(['{cid}','{mid}','{year}','{month}','{day}','{date}','{slug}','{category}'],[$arr['cid'],$arr['mid'],date("Y",strtotime($arr['created_at'])),date("m",strtotime($arr['created_at'])),date("d",strtotime($arr['created_at'])),date("Ymd",strtotime($arr['created_at'])),$arr['slug'],$arr['category']],$route);
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
        case "post": return "/archives/{cid}";
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

function getThemeConfig(){
    $file=Storage::disk('views')->read(env('APP_THEME','default')."/config.json");
    return json_decode($file);
}

function recurse_copy($src,$dst)
{  // 原目录，复制到的目录
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function get_ip() {
    //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $res =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    return $res;
    //dump(phpinfo());//所有PHP配置信息
}

function getPageTemplateName($page){
    $theme=env('APP_THEME','default');
    if(empty($page['template']) || Storage::disk('views')->exists($theme.'/pages/'.$page['template'])) return 'pages.default';
    return 'pages.'.$page;
}

//cleanHtmlByLaraword
function cHBL($html,$tags=''){
    $sa = new cleanHtml;
    $sa->allow = array();

    $list = array();    //这里存放结果map
    $c1 = preg_match_all('/<[^\/].*?>/', $tags, $m1);  //先取出所有img标签文本
    for($i=0; $i<$c1; $i++) {    //对所有的img标签进行取属性
        $c2 = preg_match_all('/(\w+)\s*=\s*(?:(?:(["\'])(.*?)(?=\2))|([^\/\s]*))/', $m1[0][$i], $m2);   //匹配出所有的属性
        preg_match('/<(.*?)[ |>]/', $m1[0][$i], $tag_name);
        $list[$tag_name[1]]=$m2[1];
    }
    $tags='';

    foreach ($list as $key=>$val) $tags.="<{$key}>";

    $sa->exceptions = $list;

    return strip_tags($sa->strip( $html ),$tags);
}

function reg_escape( $str )
{
    $conversions = array( "^" => "\^", "[" => "\[", "." => "\.", "$" => "\$", "{" => "\{", "*" => "\*", "(" => "\(", "\\" => "\\\\", "/" => "\/", "+" => "\+", ")" => "\)", "|" => "\|", "?" => "\?", "<" => "\<", ">" => "\>" );
    return strtr( $str, $conversions );
}

/**
 * Strip attribute Class
 * Remove attributes from XML elements
 * @author David (semlabs.co.uk)
 * @version 0.2.1
 */

class cleanHtml{

    public $str         = '';
    public $allow       = array();
    public $exceptions  = array();
    public $ignore      = array();

    public function strip( $str )
    {
        $this->str = $str;

        if( is_string( $str ) && strlen( $str ) > 0 )
        {
            $res = $this->findElements();
            if( is_string( $res ) )
                return $res;
            $nodes = $this->findAttributes( $res );
            $this->removeAttributes( $nodes );
        }

        return $this->str;
    }

    private function findElements()
    {

        # Create an array of elements with attributes
        $nodes = array();
        preg_match_all( "/<([^ !\/\>\n]+)([^>]*)>/i", $this->str, $elements );
        foreach( $elements[1] as $el_key => $element )
        {
            if( $elements[2][$el_key] )
            {
                $literal = $elements[0][$el_key];
                $element_name = $elements[1][$el_key];
                $attributes = $elements[2][$el_key];
                if( is_array( $this->ignore ) && !in_array( $element_name, $this->ignore ) )
                    $nodes[] = array( 'literal' => $literal, 'name' => $element_name, 'attributes' => $attributes );
            }
        }

        # Return the XML if there were no attributes to remove
        if( !$nodes[0] )
            return $this->str;
        else
            return $nodes;
    }

    private function findAttributes( $nodes )
    {

        # Extract attributes
        foreach( $nodes as &$node )
        {
            preg_match_all( "/([^ =]+)\s*=\s*[\"|']{0,1}([^\"']*)[\"|']{0,1}/i", $node['attributes'], $attributes );
            if( $attributes[1] )
            {
                foreach( $attributes[1] as $att_key => $att )
                {
                    $literal = $attributes[0][$att_key];
                    $attribute_name = $attributes[1][$att_key];
                    $value = $attributes[2][$att_key];
                    $atts[] = array( 'literal' => $literal, 'name' => $attribute_name, 'value' => $value );
                }
            }
            else
                $node['attributes'] = null;

            $node['attributes'] = $atts;
            unset( $atts );
        }

        return $nodes;
    }

    private function removeAttributes( $nodes )
    {

        # Remove unwanted attributes
        foreach( $nodes as $node )
        {

            # Check if node has any attributes to be kept
            $node_name = $node['name'];
            $new_attributes = '';
            if( is_array( $node['attributes'] ) )
            {
                foreach( $node['attributes'] as $attribute )
                {
                    if( ( is_array( $this->allow ) && in_array( $attribute['name'], $this->allow ) ) || $this->isException( $node_name, $attribute['name'], $this->exceptions ) )
                        $new_attributes = $this->createAttributes( $new_attributes, $attribute['name'], $attribute['value'] );
                }
            }
            $replacement = ( $new_attributes ) ? "<$node_name $new_attributes>" : "<$node_name>";
            $this->str = preg_replace( '/'. reg_escape( $node['literal'] ) .'/', $replacement, $this->str );
        }

    }

    private function isException( $element_name, $attribute_name, $exceptions )
    {
        if( array_key_exists($element_name, $this->exceptions) )
        {
            if( in_array( $attribute_name, $this->exceptions[$element_name] ) )
                return true;
        }

        return false;
    }

    private function createAttributes( $new_attributes, $name, $value )
    {
        if( $new_attributes )
            $new_attributes .= " ";
        $new_attributes .= "$name=\"$value\"";

        return $new_attributes;
    }

}