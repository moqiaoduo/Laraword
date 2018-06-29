<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;

class ThemeController extends Controller
{
    public function index(){
        $themes=Storage::disk('assets')->directories();
        $data=[];
        foreach ($themes as $val){
            if(Storage::disk('views')->exists($val)){
                $json=json_decode(Storage::disk('views')->read($val."/theme.json"),true);
                array_push($data,$json);
            }
        }
    }

    public function edit(Request $request,$theme){
        $type=$request->get('type','views');
        $file=$request->get('file','articles.blade.php');
        $json=json_decode(Storage::disk('views')->read($theme."/theme.json"),true);
        $dir['assets']=[];
        $dir['views']=[];
        foreach (Storage::disk('assets')->allFiles($theme) as $val) array_push($dir['assets'],mb_substr($val,mb_strlen($theme,'utf-8')+1,null,'utf-8'));
        foreach (Storage::disk('views')->allFiles($theme) as $val) array_push($dir['views'],mb_substr($val,mb_strlen($theme,'utf-8')+1,null,'utf-8'));
        $content=Storage::disk($type)->read($theme."/".$file);
        return view('admin.theme.edit',['info'=>null,'alert'=>null])->with('data',$json)->with('file',$file)->with('dir',$dir)->with('content',$content)->with('theme',$theme);
    }
}
