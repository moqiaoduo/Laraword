<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use App\Category;
use App\User;

class PostController extends Controller
{
    public function index(){
        $data=Post::paginate(10);
        foreach ($data as $key=>$val){
            $data[$key]['author']=User::find($val['uid'])['name'];
            $t='';
            foreach ($val['category'] as $k=>$v){
                $t=$t.Category::find($v)['title'];
                if($k<count($val['category'])-1) $t.=',';
            }
            $data[$key]['c']=$t;
        }
        return view('admin.post.list')->with('data',$data);
    }

    public function create(){
        $have_slug=false;
        $route=getSetting('route.post','/archive/{id}');
        if(strpos($route,'{slug}')) $have_slug=true;
        $url=config('app.url').str_replace('{slug}','<input type="text" name="slug" class="slug">',$route);
        return view('admin.post.create')->with('have_slug',$have_slug)->with('url',$url);
    }

    public function store(Request $request){

    }

    public function show($id){

    }

    public function edit($id){

    }

    public function update(Request $request){

    }

    public function destroy(Request $request){

    }
}
