<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class IndexController extends Controller
{
    public function index(){
        $data=Post::paginate(10);
        foreach ($data as $key=>$val){
            $data[$key]['content']=strip_tags($val['content']);
            $data[$key]['content']=mb_substr($val['content'],0,150,'UTF-8').'...';
        }
        return view('index')->with('data',$data);
    }
}