<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use App\Category;
use App\User;
use App\Draft;

class PostController extends Controller
{
    public function index(){
        $data=Post::paginate(10);
        foreach ($data as $key=>$val){
            if(Draft::where('post_id',$val['id'])->count()>0) $data[$key]['title']='(草稿)'.$val['title'];
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
        $route=getSetting('route.post','/archive/{id}');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput(),$route);
        $editor=self::loadEditor();
        return view('admin.post.create')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2]);
    }

    public function store(Request $request){
        $submit=$request->post('submit');
        $slug=$request->post('slug');
        $post=new Post;
        $post->uid=$request->user()->id;
        $post->title=$request->post('title');
        $post->category=[0];
        $post->content=$request->post('content');
        $post->slug='';
        $post->updated_at=now();
        if($submit=='publish') $post->status=0;
        elseif($submit=='save') $post->status=1;
        $post->save();
        if(empty($slug)) $slug=$post->id;
        $post->slug=$slug;
        $post->save();
        return redirect()->route('admin::post.index');
    }

    public function edit($id){
        $data=Post::find($id);
        $draft=Draft::where('post_id',$id)->get()->toArray();
        $route=getSetting('route.post','/archive/{id}');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput($data['slug']),$route);
        $url=str_replace('{id}',$id,$url);
        $editor=self::loadEditor();
        return view('admin.post.edit')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2])->with('data',$data)->with('draft',$draft);
    }

    public function update(Request $request){
        $submit=$request->post('submit');
        $slug=$request->post('slug');
        $id=$request->route('post');
        if(empty($slug)) $slug=$id;
        $title=$request->post('title');
        $content=$request->post('content');
        $uid=$request->user()->id;
        $post=Post::find($id);
        $post->slug=$slug;
        if($submit=='publish'){
            $post->title=$title;
            $post->content=$content;
            $post->updated_at=now();
            $post->status=0;
            Draft::where('post_id',$id)->delete();
        }elseif($submit=='save'){
            if($post->status==0)
            Draft::updateOrCreate(['post_id'=>$id],['uid'=>$uid,'title'=>$title,'content'=>$content]);
            else{
                $post->title=$title;
                $post->content=$content;
                $post->updated_at=now();
            }
        }
        $post->save();
        return redirect()->route('admin::post.edit',$id);
    }

    public function destroy(Request $request){
        Post::destroy($request->route('post'));
        return redirect()->route('admin::post.index');
    }

    public function delete(Request $request){
        Post::destroy($request->post('del'));
        return redirect()->route('admin::post.index');
    }
}
