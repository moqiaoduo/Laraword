<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Page;

class PageController extends Controller
{
    public function index(Request $request){
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Page::paginate(10);
        foreach ($data as $key=>$val){
            $data[$key]['author']=User::find($val['uid'])['name'];
        }
        return view('admin.page.list')->with('data',$data)->with('info',$info)->with('alert',$alert);
    }

    public function create(){
        $route=getSetting('route.page','/page/{slug}');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput(),$route);
        $editor=self::loadEditor();
        return view('admin.page.create')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2]);
    }

    public function store(Request $request){
        $submit=$request->post('submit');
        $title=$request->post('title');
        $categories=json_decode($request->post('category'),true);
        if(empty($categories)) $categories=[0];
        $slug=$request->post('slug');
        $page=new Page;
        $page->uid=$request->user()->id;
        $page->title=$title;
        $page->content=$request->post('content');
        if(empty($slug)) $slug=$title;
        $page->slug=$slug;
        $page->updated_at=now();
        $page->category=$categories;
        if($submit=='publish') $post->status=0;
        elseif($submit=='save') $post->status=1;
        $post->save();
        return redirect()->route('admin::page.index',['info'=>'文章已保存或发布','alert'=>'success']);
    }

    public function edit(Request $request){
        $id=$request->route('post');
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Post::find($id);
        $draft=Draft::where('post_id',$id)->get()->toArray();
        $route=getSetting('route.post','/archive/{id}');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput($data['slug']),$route);
        $url=str_replace('{id}',$id,$url);
        $editor=self::loadEditor();
        return view('admin.page.edit')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2])->with('data',$data)->with('draft',$draft)->with('info',$info)->with('alert',$alert);
    }

    public function update(Request $request){
        $submit=$request->post('submit');
        $slug=$request->post('slug');
        $categories=json_decode($request->post('category'),true);
        if(empty($categories)) $categories=[0];
        $id=$request->route('post');
        if(empty($slug)) $slug=$id;
        $title=$request->post('title');
        $content=$request->post('content');
        $uid=$request->user()->id;
        $post=Post::find($id);
        $post->slug=$slug;
        $post->category=$categories;
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
        return redirect()->route('admin::page.edit',[$id,'info'=>'文章已保存或发布','alert'=>'success']);
    }

    public function destroy(Request $request){
        Post::destroy($request->route('post'));
        return redirect()->route('admin::page.index');
    }

    public function delete(Request $request){
        Post::destroy($request->post('del'));
        return redirect()->route('admin::page.index');
    }
}
