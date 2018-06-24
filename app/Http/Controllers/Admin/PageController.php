<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Content;

class PageController extends Controller
{
    public function index(Request $request){
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Content::where('type','page')->orWhere(function ($query) {
            $query->where('type', "page_draft")
                ->where('parent', 0);
        })->paginate(10);
        foreach ($data as $key=>$val){
            if(Content::where('type','page_draft')->where('parent',$val['cid'])->count()>0 || $val['type']=='page_draft') $data[$key]['title']='(草稿)'.$val['title'];
            $data[$key]['author']=User::find($val['uid'])['name'];
        }
        return view('admin.page.list')->with('data',$data)->with('info',$info)->with('alert',$alert);
    }

    public function show($id){
        $data=Content::where('type','page')->find($id);
        return view('content')->with('data',$data);
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
        $page=new Content;
        $page->uid=$request->user()->id;
        $page->title=$title;
        $page->content=$request->post('content');
        if(empty($slug)) $slug=str_replace(' ', '', $title);
        $page->slug=$slug;
        if($submit=='publish') $page->type='page';
        elseif($submit=='save') $page->type='page_draft';
        $page->save();
        return redirect()->route('admin::page.index',['info'=>'页面已保存或发布','alert'=>'success']);
    }

    public function edit(Request $request){
        $id=$request->route('page');
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Content::where('type','page')->find($id);
        $draft=Content::where('type','page_draft')->where('parent',$id)->first();
        $route=getSetting('route.page','/page/{slug}');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput($data['slug']),$route);
        if(!empty($draft)) $url=config('app.url').str_replace('{slug}',self::loadSlugInput($draft['slug']),$route);
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
        $page=Content::find($id);
        if($submit=='publish'){
            $page->title=$title;
            $page->content=$content;
            $page->type='page';
            $page->slug=$slug;
            Content::where('type','page_draft')->where('parent',$id)->delete();
        }elseif($submit=='save'){
            if($page->type=='page')
                Content::updateOrCreate(['parent'=>$id,'type'=>'page_draft'],['uid'=>$uid,'title'=>$title,'content'=>$content,'slug'=>$slug]);
            else{
                $page->title=$title;
                $page->content=$content;
            }
        }
        $page->save();
        return redirect()->route('admin::page.edit',[$id,'info'=>'页面已保存或发布','alert'=>'success']);
    }

    public function destroy(Request $request){
        Content::destroy($request->route('page'));
        return redirect()->route('admin::page.index');
    }

    public function delete(Request $request){
        Content::destroy($request->post('del'));
        return redirect()->route('admin::page.index');
    }
}
