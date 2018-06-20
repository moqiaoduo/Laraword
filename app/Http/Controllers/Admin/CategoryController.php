<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use App\Meta;

class CategoryController extends Controller
{
    protected $selected=[];

    public function index(Request $request){
        $parent=$request->get('parent');
        if($parent===null) $parent=0;
        $data=Meta::where('type','category')->where('parent',$parent)->paginate(10);
        foreach ($data as $key=>$val) {
            $data[$key]['sub']=Meta::where('type','category')->where('parent',$val['id'])->count();
        }
        $breadcrumb=$this->getBreadCrumb($parent,true);
        //dd($data);
        return view('admin.category.list',['data'=>$data,'parent'=>$parent,'parent_parent'=>Meta::where('type','category')->where('mid',$parent)->first(),'info'=>$request->get('info'),'alert'=>$request->get('alert')])->with('breadcrumb',$breadcrumb);
    }

    protected function getBreadCrumb($parent,$first=false){
        $html='';$active='';$t='';
        if($first) $active='active';
        if($parent>0){
            $data=Category::find($parent);
            if($first) $t="<li class=\"breadcrumb-item {$active}\">{$data['title']}</li>";
            else $t="<li class=\"breadcrumb-item {$active}\"><a href=\"".route('admin::category.index',['parent'=>$data['id']])."\">{$data['title']}</a></li>";
            $html=$t.$html;
            $html=$this->getBreadCrumb($data['parent']).$html;
        }else{
            //dd($first);
            if($first) $t="<li class=\"breadcrumb-item {$active}\">".__('admin.category')."</li>";
            else $t="<li class=\"breadcrumb-item {$active}\"><a href=\"".route('admin::category.index')."\">".__('admin.category')."</a></li>";
            $html=$t.$html;
        }
        return $html;
    }

    public function getCategories(Request $request){
        if(!is_null($request->get('selected')))$this->selected=$request->get('selected');
        if(!$request->ajax()) return [];
        //return [];
        return $this->getAllCategories();
    }

    protected function getAllCategories(){
        $data=Category::where('parent',0)->get()->toArray();
        $json=array();
        if(!empty($data)){
            foreach ($data as $val) {
                $t=["text"=>$val['title'],"id"=>$val['id'],"nodes"=>$this->getCategoriesNode($val['id'])];
                if(in_array($val['id'],$this->selected)){$t['state']['checked']=true;$t['state']['selected']=true;}
                if(count($t['nodes'])<=0) unset($t['nodes']);
                array_push($json,$t);
            }
        }
        return $json;
    }

    protected function getCategoriesNode($parent){
        $data=Category::where('parent',$parent)->get()->toArray();
        $json=array();
        if(!empty($data)){
            foreach ($data as $val) {
                $t=["text"=>$val['title'],"id"=>$val['id'],"nodes"=>$this->getCategoriesNode($val['id'])];
                if(in_array($val['id'],$this->selected)){$t['state']['checked']=true;$t['state']['selected']=true;}
                if(count($t['nodes'])<=0) unset($t['nodes']);
                array_push($json,$t);
            }
        }
        return $json;
    }

    public function delete(Request $request){
        Category::destroy($request->post('del'));
        return redirect()->route('admin::category.index');
    }

    public function create(){
        $route=getSetting('route.post','/archive/{id}');
        $url=config('app.url').str_replace('{slug}',self::loadSlugInput(),$route);
        $editor=self::loadEditor();
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
        $data=Category::find($id);
        $breadcrumb=$this->getBreadCrumb($data['parent']);
        return view('admin.category.edit')->with('data',$data)->with('info',$info)->with('alert',$alert)->with('parent_options',$this->getParentOptions(0,$data['parent'],$data['id']))->with('breadcrumb',$breadcrumb);
    }

    public function update(Request $request){
        $id=$request->route('category');
        $slug=$request->post('slug');
        if(empty($slug)) $slug=$id;
        $category=Category::find($id);
        $category->title=$request->post('title');
        $category->description=$request->post('description');
        $category->parent=$request->post('parent');
        $category->slug=$slug;
        $category->save();
        return redirect()->route('admin::category.edit',[$id,'info'=>'分类已保存','alert'=>'success']);
    }

    public function destroy(Request $request){
        Post::destroy($request->route('category'));
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
