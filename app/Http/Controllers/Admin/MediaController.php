<?php

namespace App\Http\Controllers\Admin;

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
        $data=Content::where('type','attachment')->orderBy("created_at",'desc')->paginate(10);
        foreach ($data as $key=>$val){
            $data[$key]['author']=User::find($val['uid'])['name'];
            $data[$key]['slave']=Content::find($val['parent'])['title'];
        }
        return view('admin.media.list')->with('data',$data)->with('info',$info)->with('alert',$alert);
    }

    public function show($id){
        $data=Content::where('type','attachment')->find($id);
        if(!empty($data)){
            $t=json_decode($data['content'],true);
            $filename=$t['filename'];
            $description=$t['description'];
            $path=Storage::disk('uploads')->path($filename);
            $url=Storage::disk('uploads')->url($filename);
            $data['content']=$this->setContent($path,$url,$data['title'],$description);
            return view('content')->with('data',$data);
        }
    }

    protected function setContent($path,$url,$title,$description){
        return $this->setMediaContent($path,$url).'<br>'.$description.'<br>附件下载：<a target=_blank href="'.$url.'">'.$title.'</a>';
    }

    public function create(){
        return view('admin.media.create');
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
        $slug=$request->post('slug');
        $id=$request->route('medium');
        $title=$request->post('title');
        if(empty($slug)) $slug=str_replace(".","_",$title);
        $media=Content::where('type','attachment')->find($id);
        $media->slug=$this->autoRenameSlug($slug);
        $media->title=$title;
        $media->uid=$request->user()->id;
        $media->content=json_encode(["filename"=>json_decode($page->content,true)['filename'],"description"=>$request->post('description')]);
        $media->save();
        return redirect()->route('admin::media.edit',[$id,'info'=>'页面已保存或发布','alert'=>'success']);
    }

    public function destroy(Request $request){
        Content::destroy($request->route('medium'));
        return redirect()->route('admin::media.index');
    }

    public function delete(Request $request){
        $data=Content::findMany($request->post('del'));
        foreach ($data as $val){
            $val->delete();
            Storage::disk('uploads')->delete(json_decode($val['content'],true)['filename']);
        }
        return redirect()->route('admin::media.index');
    }
}
