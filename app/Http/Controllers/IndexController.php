<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class IndexController extends Controller
{
    public function index(Request $request){
        $params1=$this->matchRoute($request->getRequestUri(),getSetting('route.post','/archive/{id}'),$request->route()->parameters);
        if(!empty($params1)){

            return 'post';
        }
        $params2=$this->matchRoute($request->getRequestUri(),getSetting('route.page','/page/{id}'),$request->route()->parameters);
        if(!empty($params2)){
            return 'page';
        }
        $params3=$this->matchRoute($request->getRequestUri(),getSetting('route.category','/category/{category}'),$request->route()->parameters);
        if(!empty($params3)){
            return 'category';
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
                $dp[$matches[1]]=$this->removeDuplication($val,$arr[$i]);
                $t=preg_replace('/{([^{]+)}/',$dp[$matches[1]],$arr[$i]);
            }
            $rs.='/'.$t;
            $i++;
        }
        if(strtok($uri, '?')==$rs) return $dp;
        return [];
    }

    protected function removeDuplication($str1,$str2)
    {
        for ($i = 0; $i < strlen($str1); $i++) {
            for ($j = 0; $j < strlen($str2); $j++) {
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
            $str.=$this->getDuplication($str1,$str2,$s1+1,$s2+1);
        }
        return $str;
    }

    public function content($params){
        $month=$request->route('month');
        $day=$request->route('day');
        $date=$request->route('date');
        $slug=$request->route('slug');
        $category=$request->route('category');
        $data=array();
        if(!empty($params['id'])) $data=Post::find($params['id']);
        else{
            if(!empty($slug)){
                $query=Post::where('slug',$slug);
                if(!empty($params['year']) && count($query->get()->toArray())>1) $query->whereYear('created_at',$params['year']);
                if(!empty($params['month']) && count($query->get()->toArray())>1) $query->whereMonth('created_at',$params['month']);
                if(!empty($params['day']) && count($query->get()->toArray())>1) $query->whereDay('created_at',$params['day']);
                if(!empty($date) && count($query->get()->toArray())>1) $query->whereDate('created_at',$date);
                if(!empty($category) && $category!='uncategorized' && count($query->get()->toArray())>1){
                    $category=Category::where('slug',$category)->get()->toArray()[0];
                    if(!empty($category)) $query->whereJsonContains('category',$category['id']);
                }
                $data=$query->get()->toArray()[0];
            }
        }
        return view('content')->with('data',$data);
    }
}