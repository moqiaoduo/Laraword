<?php

namespace App\Http\Controllers\Admin;

use App\Media;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Content;
use Storage;

class MediaController extends Controller
{
    public function index(Request $request){
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Content::where('type','attachment')->paginate(10);
        foreach ($data as $key=>$val){
            $data[$key]['author']=User::find($val['uid'])['name'];
            $data[$key]['slave']=Content::find($val['parent'])['title'];
        }
        return view('admin.media.list')->with('data',$data)->with('info',$info)->with('alert',$alert);
    }

    public function create(){
        return view('admin.media.create');
    }

    public function store(Request $request){
        $submit=$request->post('submit');
        $files=json_decode($request->post('files'),true);
        $title=$request->post('title');
        $slug=$request->post('slug');
        $page=new Content;
        $page->uid=$request->user()->id;
        $page->title=$title;
        $page->type='attachment';
        $page->content=$request->post('content');
        if(empty($slug)) $slug=str_replace(' ', '', $title);
        $page->slug=$slug;
        if($submit=='publish') $page->status=0;
        elseif($submit=='save') $page->status=1;
        $page->save();
        return redirect()->route('admin::media.index',['info'=>'页面已保存或发布','alert'=>'success']);
    }

    public function edit(Request $request){
        $id=$request->route('medium');
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Content::where('type','attachment')->find($id);
        $t=json_decode($data['content'],true);
        $data['filename']=$t['filename'];
        $data['description']=$t['description'];
        $disk=Storage::disk('uploads');
        $data['size']=toSize($disk->size($t['filename']));
        $url=$disk->url($t['filename']);
        $media=$this->setMediaContent($disk->path($t['filename']),$url);
        return view('admin.media.edit')->with('url',$url)->with('data',$data)->with('info',$info)->with('alert',$alert)->with('media',$media);
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
        $page=Content::where('type','attachment')->find($id);
        $page->slug=$slug;
        $page->files=$files;
        $page->title=$title;
        $page->content=$content;
        $page->save();
        return redirect()->route('admin::media.edit',[$id,'info'=>'页面已保存或发布','alert'=>'success']);
    }

    public function destroy(Request $request){
        Content::destroy($request->route('page'));
        return redirect()->route('admin::media.index');
    }

    public function delete(Request $request){
        Content::destroy($request->post('del'));
        return redirect()->route('admin::media.index');
    }
}
