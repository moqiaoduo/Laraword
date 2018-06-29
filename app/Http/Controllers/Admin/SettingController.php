<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;

class SettingController extends Controller
{
    public function index(Request $request,$page){
        $data=[];
        if($page=='generic'){
            $data['allow_register']=getSetting('allow_register',0);
            $data['attachmentTypes']=getSetting('attachmentTypes');
        }
        return view("admin.settings.{$page}",['info'=>$request->get('request'),'alert'=>$request->get('alert')])->with('data',$data);
    }

    public function update(Request $request,$page){
        $env=$request->post('env');
        $options=$request->post('options');
        modifyEnv($env);
        setSetting($options);
        return redirect()->route('admin::setting',$page);
    }
}
