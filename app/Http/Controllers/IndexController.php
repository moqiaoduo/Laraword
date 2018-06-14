<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;
use App\Page;

class IndexController extends Controller
{
    public function index(Request $request){
        $post=getSetting('route.post','/archive/{id}');
        $params1=$this->matchRoute($request->getRequestUri(),$post,$request->route()->parameters);
        if(!empty($params1)){
            $data=$this->getPostContent($params1);
            if(!empty($data)) return view('content')->with('data',$data)->with('route',$post);
        }
        $page=getSetting('route.page','/page/{slug}');
        $params2=$this->matchRoute($request->getRequestUri(),$page,$request->route()->parameters);
        if(!empty($params2)){
            $data=$this->getPageContent($params2);
            if(!empty($data)) return view('content')->with('data',$data)->with('route',$page);
        }
        $category=getSetting('route.category','/category/{category}');
        $params3=$this->matchRoute($request->getRequestUri(),$category,$request->route()->parameters);
        if(!empty($params3)){
            $cr=getSetting('route.category','/category/{category}');
            $data=$this->getCategorizedPost($cr,$params3);
            return view('categorized')->with('data',$data['data'])->with('category',$data['category'])->with('route',$post);
        }
        if(empty($request->route()->parameters)){
            if(getSetting('indexPage',0)>0){

            }else{
                $data=Post::where('status',0)->orWhere('status',3)->paginate(10);
                $cr=getSetting('route.category','/category/{category}');
                foreach ($data as $key=>$val){
                    $data[$key]['cid']=getPostCategory($val['category'][0]);
                    $data[$key]['category']=$this->getCategories($cr,$val['category']);
                    $data[$key]['content']=strip_tags($val['content']);
                    $data[$key]['content']=mb_substr($val['content'],0,150,'UTF-8').'...';
                }
                return view('index')->with('data',$data)->with('route',getSetting('route.post','/archive/{id}'));
            }
        }
        return view('errors.404');
    }

    protected function matchRoute($uri,$route,$params){
        $arr=array_filter(explode('/',$route));
        if(count($arr)!=count($params))return 0;
        $i=1;$rs='';$dp=array();
        foreach ($params as $val){
            $t=$val;
            preg_match('/{([^{]+)}/',$arr[$i],$matches);
            if($arr[$i]!=$val && !empty($matches)){
                if(preg_replace('/{([^{]+)}/','',$arr[$i])===''){
                    $dp[$matches[1]]=$val;
                }else{
                    $dp[$matches[1]]=$this->removeDuplication($val,$arr[$i]);
                    $t=preg_replace('/{([^{]+)}/',$dp[$matches[1]],$arr[$i]);
                }

            }
            $rs.='/'.$t;
            $i++;
        }
        if(strtok($uri, '?')==$rs) return $dp;
        return [];
    }

    protected function removeDuplication($str1,$str2)
    {
        for ($i = strlen($str1)-1; $i >= 0 ; $i--) {
            for ($j = strlen($str2)-1; $j >= 0 ; $j--) {
                if($str1[$i]===$str2[$j]) return str_replace($this->getDuplication($str1,$str2,$i,$j),'',$str1);
            }
        }
        return $str1;
    }

    protected function getDuplication($str1,$str2,$s1,$s2){
        $str='';
        if(strlen($str1)<$s1 || strlen($str2)<$s2) return '';
        if(@$str1[$s1]===@$str2[$s2]){
            $str=@$str1[$s1];
            $str=$this->getDuplication($str1,$str2,$s1-1,$s2+-1).$str;
        }
        return $str;
    }

    public function getPostContent($params){
        $data=array();
        if(!empty($params['id'])) $data=Post::find($params['id']);
        else{
            if(!empty($params['slug'])){
                $query=Post::where('slug',$params['slug']);
                if(!empty($params['year']) && count($query->get()->toArray())>1) $query->whereYear('created_at',$params['year']);
                if(!empty($params['month']) && count($query->get()->toArray())>1) $query->whereMonth('created_at',$params['month']);
                if(!empty($params['day']) && count($query->get()->toArray())>1) $query->whereDay('created_at',$params['day']);
                if(!empty($params['date']) && count($query->get()->toArray())>1) $query->whereDate('created_at',$params['date']);
                if(!empty($params['category']) && $params['category']!='uncategorized' && count($query->get()->toArray())>1){
                    $category=Category::where('slug',$params['category'])->get()->toArray()[0];
                    if(!empty($category)) $query->whereJsonContains('category',$category['id']);
                }
                $data=$query->get()->toArray()[0];
            }
        }
        return $data;
    }

    public function getCategorizedPost($cr,$params){
        $category=$params['category'];$info=[];
        if($category!='uncategorized' && $category!='0') $info=Category::where('slug',$category)->orWhere('id',$category)->get()->toArray();
        if(empty($info) && ($category!='uncategorized' && $category!='0')) return [];
        $category='uncategorized';$id=0;
        if(!empty($info)){
            $id=$info[0]['id'];
            $category=$info[0]['title'];
        }
        $data=Post::whereRaw("JSON_CONTAINS(category, '[{$id}]')")->paginate(10);
        foreach ($data as $key=>$val){
            $data[$key]['category']=$this->getCategoriesHTML($cr,$val['category']);
            $data[$key]['content']=strip_tags($val['content']);
            $data[$key]['content']=mb_substr($val['content'],0,150,'UTF-8').'...';
        }
        return ["data"=>$data,"category"=>$category];
    }

    public function getPageContent($params){
        $query=Page::where('slug',$params['slug']);
        $data=$query->get()->toArray()[0];
        return $data;
    }
}