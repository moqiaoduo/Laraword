<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;

class CategoryController extends Controller
{
    public function index(){
        return view('admin.category.list');
    }

    public function getCategories(Request $request){
        //if(!$request->ajax()) return [];
        $data=Category::where('parent',0)->get()->toArray();
        $json=array();
        if(!empty($data)){
            foreach ($data as $val) {
                $t=["text"=>$val['title'],"selectable"=>"false","nodes"=>$this->getCategoriesNode($val['id'])];
                if(count($t['nodes'])<=0) unset($t['nodes']);
                array_push($json,$t);
            }
        }
        return $json;
    }

    public function getCategoriesNode($parent){
        $data=Category::where('parent',$parent)->get()->toArray();
        $json=array();
        if(!empty($data)){
            foreach ($data as $val) {
                $t=["text"=>$val['title'],"selectable"=>"false","nodes"=>$this->getCategoriesNode($val['id'])];
                if(count($t['nodes'])<=0) unset($t['nodes']);
                array_push($json,$t);
            }
        }
        return $json;
    }
}
