<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Page;
use App\Draft;
use App\User;

class PageController extends Controller
{
    public function index(Request $request){
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Page::paginate(10);
        foreach ($data as $key=>$val){
            if(Draft::where('type','page')->where('post_id',$val['id'])->count()>0) $data[$key]['title']='(草稿)'.$val['title'];
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
        $files=json_decode($request->post('files'),true);
        $title=$request->post('title');
        $slug=$request->post('slug');
        $page=new Page;
        $page->uid=$request->user()->id;
        $page->title=$title;
        $page->files=$files;
        $page->content=$request->post('content');
        if(empty($slug)) $slug=str_replace(' ', '', $title);
        $page->slug=$slug;
        if($submit=='publish') $page->status=0;
        elseif($submit=='save') $page->status=1;
        $page->save();
        return redirect()->route('admin::page.index',['info'=>'页面已保存或发布','alert'=>'success']);
    }

    public function edit(Request $request){
        $id=$request->route('page');
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Page::find($id);
        $draft=Draft::where('type','page')->where('post_id',$id)->get()->toArray();
        $route=getSetting('route.page','/archive/{id}');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput($data['slug']),$route);
        $url=str_replace('{id}',$id,$url);
        $editor=self::loadEditor();
        return view('admin.page.edit')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2])->with('data',$data)->with('draft',$draft)->with('info',$info)->with('alert',$alert);
    }

    public function update(Request $request){
        $submit=$request->post('submit');
        $slug=$request->post('slug');
        $files=json_decode($request->post('files'),true);
        $id=$request->route('page');
        $title=$request->post('title');
        if(empty($slug)) $slug=str_replace(' ', '', $title);
        $content=$request->post('content');
        $uid=$request->user()->id;
        $page=Page::find($id);
        $page->slug=$slug;
        $page->files=$files;
        if($submit=='publish'){
            $page->title=$title;
            $page->content=$content;
            $page->status=0;
            Draft::where('type','page')->where('post_id',$id)->delete();
        }elseif($submit=='save'){
            if($page->status==0)
                Draft::updateOrCreate(['post_id'=>$id,'type'=>'page'],['uid'=>$uid,'title'=>$title,'content'=>$content]);
            else{
                $page->title=$title;
                $page->content=$content;
            }
        }
        $page->save();
        return redirect()->route('admin::page.edit',[$id,'info'=>'页面已保存或发布','alert'=>'success']);
    }

    public function destroy(Request $request){
        Page::destroy($request->route('page'));
        return redirect()->route('admin::page.index');
    }

    public function delete(Request $request){
        Page::destroy($request->post('del'));
        return redirect()->route('admin::page.index');
    }
}
