<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
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
                $meta=Meta::find($v->mid);
                if($meta['type']=='category') $t.=$meta['name'].',';
            }
            $data[$key]['category']=substr($t,0,strlen($t)-1);
        }
        return view('admin.post.list')->with('data',$data)->with('info',$info)->with('alert',$alert);
    }

    protected function updateCategory($cid,$category){
        if(empty($category)) $category=[getSetting('default_category',0)];
        $old=DB::table('relationships')->where('cid',$cid)->get();
        if($old->count()>0){
            foreach ($old as $key=>$val){
                $s=array_search($val->mid,$category);
                if($s===false){
                    DB::table('relationships')->where('mid',$val->mid)->delete();
                }else{
                    unset($category[$s]);
                }
            }
        }
        foreach ($category as $val){
            if($val>0) DB::table('relationships')->insert(["cid"=>$cid,"mid"=>$val]);
        }
    }

    public function show($id){
        $data=Content::where('type','post')->find($id);
        return view('content')->with('data',$data);
    }

    public function create(){
        $route=getCustomUri(json_decode(getSetting('routeTable'),true),'post');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput(),$route);
        $editor=self::loadEditor();
        return view('admin.post.create')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2]);
    }

    public function store(Request $request){
        $submit=$request->post('submit');
        $files=json_decode($request->post('files'),true);
        $categories=json_decode($request->post('category'),true);
        if(empty($categories)) $categories=[1];
        $slug=$request->post('slug');
        $post=new Content;
        $post->uid=$request->user()->id;
        $post->title=$request->post('title');
        $post->content=$request->post('content');
        $post->slug='';
        if($submit=='save') $post->type="post_draft";
        $post->save();
        $id=$post->cid;
        if(empty($slug)) $slug=$id;
        $post->slug=$this->autoRenameSlug($slug);
        $post->save();
        $this->setSlaveFile($id,$files);
        $post->contentMeta()->sync($categories);
        $this->updateCategoryCount();
        return redirect()->route('admin::post.index',['info'=>'文章已保存或发布','alert'=>'success']);
    }

    protected function setSlaveFile($cid,$files){
        if(!empty($files)) Content::whereIn('cid',$files)->update(['parent'=>$cid]);
    }

    public function edit(Request $request){
        $id=$request->route('post');
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Content::find($id);
        $draft=Content::where('type','post_draft')->where('parent',$id)->first();
        $route=getCustomUri(json_decode(getSetting('routeTable'),true),'post');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput($data['slug']),$route);
        $data['category']=$data->contentMeta()->first()['slug'];
        if(!empty($draft)) $url=config('app.url').str_replace('{slug}',self::loadSlugInput(substr($draft['slug'],1)),$route);
        $url=getCustomRoute($url,$data);
        $url=str_replace('{id}',$id,$url);
        $editor=self::loadEditor();
        return view('admin.post.edit')->with('url',$url)->with('head',$editor[0])->with('editor_container',$editor[1])->with('js',$editor[2])->with('data',$data)->with('draft',$draft)->with('info',$info)->with('alert',$alert);
    }

    public function update(Request $request){
        $submit=$request->post('submit');
        $slug=$request->post('slug');
        $files=json_decode($request->post('files'),true);
        $categories=json_decode($request->post('category'),true);
        if(empty($categories)) $categories=[1];
        $id=$request->route('post');
        $title=$request->post('title');
        $content=$request->post('content');
        $uid=$request->user()->id;
        $post=Content::find($id);
        if(empty($slug)) $slug=$post->slug;
        if($submit=='publish'){
            $post->slug=$this->autoRenameSlug($post->cid,$slug);
            $post->title=$title;
            $post->content=$content;
            $post->type='post';
            Content::where('type','post_draft')->where('parent',$id)->delete();
        }elseif($submit=='save'){
            if($post->status==0)
                Content::updateOrCreate(['parent'=>$id, 'type'=>'post_draft'],['uid'=>$uid,'title'=>$title,'content'=>$content,'slug'=>$this->autoRenameSlug($post->cid,"@".$slug)]);
            else{
                $post->title=$title;
                $post->content=$content;
                $post->slug=$this->autoRenameSlug($post->cid,$slug);
            }
        }
        $post->save();
        $this->setSlaveFile($id,$files);
        $post->contentMeta()->sync($categories);
        $this->updateCategoryCount();
        return redirect()->route('admin::post.edit',[$id,'info'=>'文章已保存或发布','alert'=>'success']);
    }

    public function destroy(Request $request){
        Content::destroy($request->route('post'));
        return redirect()->route('admin::post.index');
    }

    public function delete(Request $request){
        Content::destroy($request->post('del'));
        return redirect()->route('admin::post.index');
    }
}
