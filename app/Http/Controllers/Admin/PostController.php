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
    public function index(Request $request){
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Post::paginate(10);
        foreach ($data as $key=>$val){
            if(Draft::where('type','post')->where('post_id',$val['id'])->count()>0) $data[$key]['title']='(草稿)'.$val['title'];
            $data[$key]['author']=User::find($val['uid'])['name'];
            $t='';
            foreach ($val['category'] as $k=>$v){
                $t=$t.Category::find($v)['title'];
                if($k<count($val['category'])-1) $t.=',';
            }
            $data[$key]['c']=$t;
        }
        return view('admin.post.list')->with('data',$data)->with('info',$info)->with('alert',$alert);
    }

    public function create(){
        $route=getSetting('route.post','/archive/{id}');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput(),$route);
        $editor=self::loadEditor();
        return view('admin.post.create')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2]);
    }

    public function store(Request $request){
        $submit=$request->post('submit');
        $categories=json_decode($request->post('category'),true);
        if(empty($categories)) $categories=[0];
        $slug=$request->post('slug');
        $post=new Post;
        $post->uid=$request->user()->id;
        $post->title=$request->post('title');
        $page->category=$categories;
        $post->content=$request->post('content');
        $post->slug='';
        if($submit=='publish') $post->status=0;
        elseif($submit=='save') $post->status=1;
        $post->save();
        if(empty($slug)) $slug=$post->id;
        $post->slug=$slug;
        $post->save();
        return redirect()->route('admin::post.index',['info'=>'文章已保存或发布','alert'=>'success']);
    }

    public function edit(Request $request){
        $id=$request->route('post');
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Post::find($id);
        $draft=Draft::where('type','post')->where('post_id',$id)->get()->toArray();
        $route=getSetting('route.post','/archive/{id}');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput($data['slug']),$route);
        $url=str_replace('{id}',$id,$url);
        $editor=self::loadEditor();
        return view('admin.post.edit')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2])->with('data',$data)->with('draft',$draft)->with('info',$info)->with('alert',$alert);
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
            $post->status=0;
            Draft::where('type','post')->where('post_id',$id)->delete();
        }elseif($submit=='save'){
            if($post->status==0)
            Draft::updateOrCreate(['post_id'=>$id],['uid'=>$uid,'title'=>$title,'content'=>$content]);
            else{
                $post->title=$title;
                $post->content=$content;
            }
        }
        $post->save();
        return redirect()->route('admin::post.edit',[$id,'info'=>'文章已保存或发布','alert'=>'success']);
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
