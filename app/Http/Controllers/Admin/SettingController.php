<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index(Request $request,$page){
        $data=[];
        if($page=='generic'){
            $data='';
        }
        return view("admin.settings.{$page}",['info'=>$request->get('request'),'alert'=>$request->get('alert')])->with('data',$data);
    }

    public function update(){

    }
}
