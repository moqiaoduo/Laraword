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
    if(empty($key)) $val=DB::table('settings')->get();
    else $val=DB::table('settings')->where('key',$key)->get();
    if(empty($val)) return $default;
    return $val;
}

function setSetting($key,$val){
    DB::table('settings')->where('key',$key)->delete();
    return DB::table('settings')->insert(['key'=>$key,'val'=>$val]);
}