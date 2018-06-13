<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;

class CategoryController extends Controller
{
    protected $selected=[];

    public function index(Request $request){
        $parent=$request->get('parent');
        if($parent===null) $parent=0;
        $data=Category::where('parent',$parent)->paginate(10);
        foreach ($data as $key=>$val) {
            $data[$key]['sub']=Category::where('parent',$val['id'])->count();
        }
        //dd($data);
        return view('admin.category.list',['data'=>$data,'parent'=>$parent,'parent_parent'=>Category::find($parent)]);
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
                $t=["text"=>$val['title'],"id"=>$val['id'],"selectable"=>"false","nodes"=>$this->getCategoriesNode($val['id'])];
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
                $t=["text"=>$val['title'],"id"=>$val['id'],"selectable"=>"false","nodes"=>$this->getCategoriesNode($val['id'])];
                if(in_array($val['id'],$this->selected)){$t['state']['checked']=true;$t['state']['selected']=true;}
                if(count($t['nodes'])<=0) unset($t['nodes']);
                array_push($json,$t);
            }
        }
        return $json;
    }

    public function delete(Request $request){
        Category::destroy($request->post('del'));
    }
}
