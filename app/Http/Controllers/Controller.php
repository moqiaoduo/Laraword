<?php

namespace App\Http\Controllers;

use App\Content;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Category;
use App\Post;
use App\Media;
use App\Meta;
use DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function loadEditor(){
        $editor=env('APP_EDITOR','none');
        if(!is_dir(resource_path("views/editor/{$editor}"))) $editor='none';
        $editor_head="editor.{$editor}.head";
        $editor_container="editor.{$editor}.container";
        $editor_js="editor.{$editor}.js";
        return array($editor_head,$editor_container,$editor_js);
    }

    protected function loadSlugInput($value=''){
        return <<<EOT
        <div style="position: relative; display: inline-block;margin-left: -5px;"><input type="text" id="slug" name="slug" autocomplete="off" value="{$value}" class="mono" style="left: 0px; top: 0px; min-width: 5px; position: absolute; width: 100%;"><pre id="preview" style="display: block; visibility: hidden; height: 15px; padding: 0px 2px; margin: 0px;white-space: pre-wrap;font-size: 1em;-ms-word-break: break-word;word-break: break-word;overflow-y:hidden;">{$value}</pre></div>
EOT;
    }

    protected function getCategoriesHTML($cr,$data){
        $html='';
        foreach ($data as $key=>$val){
            $info=Meta::where('type','category')->find($val);
            if(empty($info)){
                $info['title']='uncategorized';
                $info['slug']='uncategorized';
            }
            $html.="<a href=\"".getCustomRoute($cr,["category"=>$info['slug']])."\">{$info['title']}</a>,";
        }
        return substr($html,0,strlen($html)-1);;
    }

    protected function insertUploadRecord($title,$filename){
        $user=request()->user();
        $media=new Content;
        $media->uid=$user->id;
        $media->type='attachment';
        $media->title=$title;
        $media->content=json_encode(['filename'=>$filename,'description'=>"Created by {$user->name}"]);
        $media->save();
        return $media->cid;
    }

    protected function updateCategoryCount(){
        $data=Meta::where('type','category')->get();
        foreach ($data as $val){
            Meta::find($val->mid)->update(['count'=>DB::table('relationships')->where('mid',$val->mid)->count()]);
        }
    }

}
