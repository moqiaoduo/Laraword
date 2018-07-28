<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Meta;
use DB;
use App\Content;
use App\User;

class CategoryController extends Controller
{
    public function index(Request $request){
        $parent=$request->get('parent');
        if($parent===null) $parent=0;
        $data=Meta::where('type','category')->where('parent',$parent)->paginate(10);
        foreach ($data as $key=>$val) {
            $data[$key]['sub']=Meta::where('type','category')->where('parent',$val['mid'])->count();
        }
        $breadcrumb=$this->getBreadCrumb($parent,true);
        return view('admin.category.list',['data'=>$data,'parent'=>$parent,'parent_parent'=>Meta::where('type','category')->find($parent),'info'=>$request->get('info'),'alert'=>$request->get('alert')])->with('breadcrumb',$breadcrumb);
    }

    public function show($id){
        $data=Meta::find($id)->metaContent()->paginate(10);
        foreach ($data as $key=>$val){
            if(Content::where('type','post_draft')->where('parent',$val['cid'])->count()>0 || $val['type']=='post_draft') $data[$key]['title']='(草稿)'.$val['title'];
            $data[$key]['author']=User::find($val['uid'])['name'];
            $data[$key]['category']=$this->getCategoriesHTML(env('APP_URL').'/admin/category/{mid}',$val['cid']);
        }
        return view('admin.post.list',['info'=>null,'alert'=>null])->with('data',$data)->with('category',$id);
    }

    protected function getBreadCrumb($parent,$first=false){
        $html='';$active='';$t='';
        if($first) $active='active';
        if($parent>0){
            $data=Meta::where('type','category')->find($parent);
            if($first) $t="<li class=\"breadcrumb-item {$active}\">{$data['name']}</li>";
            else $t="<li class=\"breadcrumb-item {$active}\"><a href=\"".route('admin::category.index',['parent'=>$data['mid']])."\">{$data['name']}</a></li>";
            $html=$t.$html;
            $html=$this->getBreadCrumb($data['parent']).$html;
        }else{
            if($first) $t="<li class=\"breadcrumb-item {$active}\">".__('Category')."</li>";
            else $t="<li class=\"breadcrumb-item {$active}\"><a href=\"".route('admin::category.index')."\">".__('Category')."</a></li>";
            $html=$t.$html;
        }
        return $html;
    }

    public function getCategories(Request $request){
        if(!$request->ajax()) return [];
        $data=DB::table('relationships')->where('cid',$request->get('cid'))->get();
        $category=[];
        foreach ($data as $val) array_push($category,$val->mid);
        return $this->getAllCategories($category);
    }

    protected function getAllCategories($selected=[]){
        $data=Meta::where('type','category')->where('parent',0)->get()->toArray();
        $json=array();
        if(!empty($data)){
            foreach ($data as $val) {
                $t=["text"=>$val['name'],"id"=>$val['mid'],"nodes"=>$this->getCategoriesNode($val['mid'],$selected)];
                if(in_array($val['mid'],$selected)){$t['state']['checked']=true;$t['state']['selected']=true;}
                if(count($t['nodes'])<=0) unset($t['nodes']);
                array_push($json,$t);
            }
        }
        return $json;
    }

    protected function getCategoriesNode($parent,$selected=[]){
        $data=Meta::where('type','category')->where('parent',$parent)->get()->toArray();
        $json=array();
        if(!empty($data)){
            foreach ($data as $val) {
                //dd($selected);
                $t=["text"=>$val['name'],"id"=>$val['mid'],"nodes"=>$this->getCategoriesNode($val['mid'],$selected)];
                if(in_array($val['mid'],$selected)){$t['state']['checked']=true;$t['state']['selected']=true;}
                if(count($t['nodes'])<=0) unset($t['nodes']);
                array_push($json,$t);
            }
        }
        return $json;
    }

    public function delete(Request $request){
        Meta::destroy($request->post('del'));
        return redirect()->route('admin::category.index');
    }

    public function create(){
        return view('admin.category.create')->with('parent_options',$this->getParentOptions(0));
    }

    public function store(Request $request){
        $slug=$request->post('slug');
        $category=new Meta;
        $category->name=$request->post('title');
        $category->description=$request->post('description');
        $category->parent=$request->post('parent');
        $category->type='category';
        $category->save();
        if(empty($slug)) $slug=$category->mid;
        $category->slug=$slug;
        $category->save();
        return redirect()->route('admin::category.index',['info'=>'分类已保存','alert'=>'success']);
    }

    public function edit(Request $request){
        $id=$request->route('category');
        $info=$request->get('info');
        $alert=$request->get('alert');
        $data=Meta::where('type','category')->find($id);
        $breadcrumb=$this->getBreadCrumb($data['parent']);
        return view('admin.category.edit')->with('data',$data)->with('info',$info)->with('alert',$alert)->with('parent_options',$this->getParentOptions(0,$data['parent'],$data['mid']))->with('breadcrumb',$breadcrumb);
    }

    public function update(Request $request){
        $id=$request->route('category');
        $slug=$request->post('slug');
        if(empty($slug)) $slug=$id;
        $category=Meta::where('type','category')->find($id);
        $category->name=$request->post('title');
        $category->description=$request->post('description');
        $category->parent=$request->post('parent');
        $category->slug=$slug;
        $category->save();
        return redirect()->route('admin::category.edit',[$id,'info'=>'分类已保存','alert'=>'success']);
    }

    public function destroy(Request $request){
        Meta::destroy($request->route('category'));
        return redirect()->route('admin::category.index');
    }

    protected function getParentOptions($parent,$selected=0,$except=-1,$deep=0){
        $data=Meta::where('type','category')->where('parent',$parent)->get()->toArray();
        $html='';
        foreach ($data as $val){
            if($val['mid']==$except) continue;
            $prefix='';$active='';
            if($selected==$val['mid']) $active=' selected';
            for($i=0;$i<$deep;$i++) $prefix.='--';
            $html.='<option'.$active.' value="'.$val['mid'].'">'.$prefix.$val['name'].'</option>';
            $html.=$this->getParentOptions($val['mid'],$selected,$except,$deep+1);
        }
        return $html;
    }
}
