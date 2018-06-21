<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use App\Category;
use App\User;
use App\Draft;
use App\Content;
use App\Meta;
use DB;

class PostController extends Controller
{
    public function index(Request $request){
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Content::where('type','post')->orWhere(function ($query) {
            $query->where('type', "post_draft")
                ->where('parent', 0);
        })->paginate(10);
        foreach ($data as $key=>$val){
            if(Content::where('type','post_draft')->where('parent',$val['cid'])->count()>0 || $val['type']=='post_draft') $data[$key]['title']='(草稿)'.$val['title'];
            $data[$key]['author']=User::find($val['uid'])['name'];
            $category=DB::table('relationships')->where('cid',$val['cid'])->get();
            $t='';
            foreach ($category as $k=>$v){
                $t.=Meta::find($v->mid)['name'];
                if($k<count($category)-1) $t.=',';
            }
            $data[$key]['category']=$t;
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
        $files=json_decode($request->post('files'),true);
        $categories=json_decode($request->post('category'),true);
        if(empty($categories)) $categories=[0];
        $slug=$request->post('slug');
        $post=new Content;
        $post->uid=$request->user()->id;
        $post->title=$request->post('title');
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
        $data=Content::find($id);
        $draft=Content::where('type','post_draft')->where('post_id',$id)->get()->toArray();
        $route=getSetting('route.post','/archive/{id}');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput($data['slug']),$route);
        $url=str_replace('{id}',$id,$url);
        $editor=self::loadEditor();
        return view('admin.post.edit')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2])->with('data',$data)->with('draft',$draft)->with('info',$info)->with('alert',$alert);
    }

    public function update(Request $request){
        $submit=$request->post('submit');
        $slug=$request->post('slug');
        $files=json_decode($request->post('files'),true);
        $categories=json_decode($request->post('category'),true);
        if(empty($categories)) $categories=[0];
        $id=$request->route('post');
        if(empty($slug)) $slug=$id;
        $title=$request->post('title');
        $content=$request->post('content');
        $uid=$request->user()->id;
        $post=Content::find($id);
        if($submit=='publish'){
            $post->slug=$slug;
            $post->title=$title;
            $post->content=$content;
            $post->type='post';
            Content::where('type','post_draft')->where('parent',$id)->delete();
        }elseif($submit=='save'){
            if($post->status==0)
                Content::updateOrCreate(['parent'=>$id, 'type'=>'post_draft'],['uid'=>$uid,'title'=>$title,'content'=>$content,'slug'=>$slug]);
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
