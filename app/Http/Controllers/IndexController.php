<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class IndexController extends Controller
{
    public function index(){
        if(getSetting('indexPage',0)>0){

        }else{
            $data=Post::where('status',0)->orWhere('status',3)->paginate(10);
            foreach ($data as $key=>$val){
                $data[$key]['category']=$this->getCategories($val['category']);
                $data[$key]['content']=strip_tags($val['content']);
                $data[$key]['content']=mb_substr($val['content'],0,150,'UTF-8').'...';
            }
            return view('index')->with('data',$data);
        }
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