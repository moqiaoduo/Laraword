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
        })->orderBy("created_at",'desc')->paginate(10);
        foreach ($data as $key=>$val){
            if(Content::where('type','page_draft')->where('parent',$val['cid'])->count()>0 || $val['type']=='page_draft') $data[$key]['title']='(草稿)'.$val['title'];
            $data[$key]['author']=User::find($val['uid'])['name'];
        }
        return view('admin.page.list')->with('data',$data)->with('info',$info)->with('alert',$alert);
    }

    public function show($id){
        $data=Content::where('type','page')->find($id);
        return view(getPageTemplateName($data))->with('data',$data);
    }

    public function create(){
        $route=getCustomUri(json_decode(getSetting('routeTable'),true),'page');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput(),$route);
        $editor=self::loadEditor();
        return view('admin.page.create')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2]);
    }

    public function store(Request $request){
        $submit=$request->post('submit');
        $files=json_decode($request->post('files'),true);
        $title=$request->post('title');
        $status=$request->post('status');
        $password=$request->post('password');
        $slug=$request->post('slug');
        $created_at=$request->post('created_at');
        if(empty($created_at)) $created_at=date("Y-m-d H:i:s");
        $page=new Content;
        $page->uid=$request->user()->id;
        $page->title=$title;
        $page->content=$request->post('content');
        if(empty($slug)) $slug=str_replace(' ', '', $title);
        $page->slug=$this->autoRenameSlug(0,$slug);
        $page->status=$status;
        $page->password=$password;
        if($submit=='publish') $page->type='page';
        elseif($submit=='save') $page->type='page_draft';
        $page->created_at=str_replace('T',' ',$created_at);
        $page->save();
        $this->setSlaveFile($page->cid,$files);
        return redirect()->route('admin::page.index',['info'=>'页面已保存或发布','alert'=>'success']);
    }

    protected function setSlaveFile($cid,$files){
        if(!empty($files)) Content::whereIn('cid',$files)->update(['parent'=>$cid]);
    }

    public function edit(Request $request){
        $id=$request->route('page');
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Content::where('type','page')->find($id);
        $draft=Content::where('type','page_draft')->where('parent',$id)->first();
        $route=getCustomUri(json_decode(getSetting('routeTable'),true),'page');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput($data['slug']),$route);
        if(!empty($draft)) $url=config('app.url').str_replace('{slug}',self::loadSlugInput(substr($draft['slug'],1)),$route);
        $url=str_replace('{id}',$id,$url);
        $editor=self::loadEditor();
        return view('admin.page.edit')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2])->with('data',$data)->with('draft',$draft)->with('info',$info)->with('alert',$alert);
    }

    public function update(Request $request){
        $submit=$request->post('submit');
        $slug=$request->post('slug');
        $files=json_decode($request->post('files'),true);
        $created_at=$request->post('created_at');
        $password=$request->post('password');
        $status=$request->post('status');
        if(empty($created_at)) $created_at=date("Y-m-d H:i:s");
        $created_at=str_replace('T',' ',$created_at);
        $id=$request->route('page');
        $title=$request->post('title');
        $content=$request->post('content');
        $uid=$request->user()->id;
        $page=Content::find($id);
        if(empty($slug)) $slug=$page->slug;
        if($submit=='publish'){
            $page->title=$title;
            $page->content=$content;
            $page->type='page';
            $page->slug=$this->autoRenameSlug($page->cid,$slug);
            $page->created_at=$created_at;
            $page->status=$status;
            $post->password=$password;
            Content::where('type','page_draft')->where('parent',$id)->delete();
        }elseif($submit=='save'){
            if($page->type=='page')
                Content::updateOrCreate(['parent'=>$id,'type'=>'page_draft'],['uid'=>$uid,'title'=>$title,'content'=>$content,'slug'=>$this->autoRenameSlug($page->cid,"@".$slug),'created_at'=>$created_at,'status'=>$status,'password'=>$password]);
            else{
                $page->title=$title;
                $page->content=$content;
                $page->slug=$this->autoRenameSlug($page->cid,$slug);
                $page->created_at=$created_at;
                $page->status=$status;
                $post->password=$password;
            }
        }
        $page->save();
        $this->setSlaveFile($page->cid,$files);
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
