<?php

namespace App\Http\Controllers\Admin;

use App\Media;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    public function index(Request $request){
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Media::paginate(10);
        foreach ($data as $key=>$val){
            $data[$key]['author']=User::find($val['uid'])['name'];
        }
        return view('admin.media.list')->with('data',$data)->with('info',$info)->with('alert',$alert);
    }

    public function create(){
        $route=getSetting('route.page','/page/{slug}');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput(),$route);
        $editor=self::loadEditor();
        return view('admin.media.create')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2]);
    }

    public function store(Request $request){
        $submit=$request->post('submit');
        $files=json_decode($request->post('files'),true);
        $title=$request->post('title');
        $slug=$request->post('slug');
        $page=new Media;
        $page->uid=$request->user()->id;
        $page->title=$title;
        $page->files=$files;
        $page->content=$request->post('content');
        if(empty($slug)) $slug=str_replace(' ', '', $title);
        $page->slug=$slug;
        if($submit=='publish') $page->status=0;
        elseif($submit=='save') $page->status=1;
        $page->save();
        return redirect()->route('admin::media.index',['info'=>'页面已保存或发布','alert'=>'success']);
    }

    public function edit(Request $request){
        $id=$request->route('page');
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Media::find($id);
        $route=getSetting('route.page','/archive/{id}');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput($data['slug']),$route);
        $url=str_replace('{id}',$id,$url);
        $editor=self::loadEditor();
        return view('admin.media.edit')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2])->with('data',$data)->with('info',$info)->with('alert',$alert);
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
        $page=Media::find($id);
        $page->slug=$slug;
        $page->files=$files;
        $page->title=$title;
        $page->content=$content;
        $page->save();
        return redirect()->route('admin::media.edit',[$id,'info'=>'页面已保存或发布','alert'=>'success']);
    }

    public function destroy(Request $request){
        Media::destroy($request->route('page'));
        return redirect()->route('admin::media.index');
    }

    public function delete(Request $request){
        Media::destroy($request->post('del'));
        return redirect()->route('admin::media.index');
    }
}
