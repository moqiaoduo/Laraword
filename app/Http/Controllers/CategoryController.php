<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Post;

class CategoryController extends Controller
{
    public function index(){

    }

    public function show($category){
        $id=0;
        $info=Category::where('slug',$category)->orWhere('id',$category)->get()->toArray();
        $category='uncategorized';
        if(!empty($info)){
            $id=$info[0]['id'];
            $category=$info[0]['title'];
        }
        $data=Post::whereRaw("JSON_CONTAINS(category, '[{$id}]')")->paginate(10);
        foreach ($data as $key=>$val){
            $data[$key]['category']=$this->getCategories($val['category']);
            $data[$key]['content']=strip_tags($val['content']);
            $data[$key]['content']=mb_substr($val['content'],0,150,'UTF-8').'...';
        }
        return view('categoried')->with('data',$data)->with('category',$category);
    }

    protected function getCategories($data){
        $html='';
        foreach ($data as $key=>$val){
            $info=Category::find($val);
            if(empty($info)){
                $info['title']='uncategorized';
            }
            $html.="<a href=\"".route('category',$val)."\">{$info['title']}</a>,";
        }
        return substr($html,0,strlen($html)-1);;
    }
}
