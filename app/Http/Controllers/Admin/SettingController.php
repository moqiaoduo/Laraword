<?php

namespace App\Http\Controllers\Admin;

use App\Content;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;

class SettingController extends Controller
{
    public function index(Request $request,$page){
        $data['routeTable']=getCustomUri(json_decode(getSetting('routeTable'),true),["post","page","category","articleList"]);
        if($page=='generic'){
            $data['allow_register']=getSetting('allow_register',0);
            $data['attachmentTypes']=getSetting('attachmentTypes');
        }elseif($page=='read'){
            $data['showArticleList']=getSetting('showArticleList',0);
            $data['pages']=Content::where('type','page')->get();
            $data['indexPage']=getSetting('indexPage',0);
            $data['postsListSize']=getSetting('postsListSize',10);
        }elseif($page=='link'){

        }
        return view("admin.settings.{$page}",['info'=>$request->get('request'),'alert'=>$request->get('alert')])->with('data',$data);
    }

    protected function checkboxOptions($arr=[],&$options){
        foreach ($arr as $val){
            if(empty($options[$val])) $options[$val]=0;
        }
    }

    public function update(Request $request,$page){
        $env=$request->post('env');
        $options=$request->post('options');
        $checkbox=array("showArticleList");
        $this->checkboxOptions($checkbox,$options);
        if(isset($options['attachmentTypes'])) $options['attachmentTypes']=preg_replace('# #','',$options['attachmentTypes']);
        $routeTable_modified=$request->post('routeTable');
        if(is_array($env)) modifyEnv($env);
        if(is_array($options)) setSetting($options);
        $routeTable=json_decode(getSetting('routeTable'),true);
        if(is_array($routeTable_modified))foreach ($routeTable_modified as $key=>$val) $routeTable[$key]=$val;
        setSetting(["routeTable"=>json_encode($routeTable)]);
        return redirect()->route('admin::setting',$page);
    }
}
