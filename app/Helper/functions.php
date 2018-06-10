<?php
/**
 * Common Functions
 * Powered by Laraword
 */

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

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
    if(empty(DB::select("SELECT table_name FROM information_schema.TABLES WHERE table_name ='settings';"))) return $default;
    if(empty($key)) $val=DB::table('settings')->get();
    else{
        $val=DB::table('settings')->where('key',$key)->get()->toArray();
        if(!empty($val)){
            $val=$val[0];
            $val=$val->val;
        }
    }
    if(empty($val)) return $default;
    return $val;
}

function setSetting($key,$val){
    DB::table('settings')->where('key',$key)->delete();
    return DB::table('settings')->insert(['key'=>$key,'val'=>$val]);
}

function theme($file){
    return env('APP_URL').'/theme/'.env('APP_THEME','default').'/'.$file;
}

function getCustomContentRoute($arr=array()){
    $route=getSetting('route.post','/archive/{id}');
    return str_replace(['{id}','{year}','{month}','{day}','{date}','{slug}','{category}'],[$arr['id'],date("Y",strtotime($arr['created_at'])),date("m",strtotime($arr['created_at'])),date("d",strtotime($arr['created_at'])),date("Ymd",strtotime($arr['created_at'])),$arr['slug'],getPostCategory($arr['category'])],$route);
}

function getPostCategory($category){
    if(empty($category) || $category[0]==0) return 'uncategoried';
    return DB::table('category')->find($category[0])->slug;
}

function vendor($file){
    return env('APP_URL').'/vendor/'.$file;
}