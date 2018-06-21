<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;
use App\Page;
use App\Content;
use DB;
use App\Meta;

class IndexController extends Controller
{
    public function index(Request $request){
        $post=getSetting('route.post','/archive/{id}');
        $params1=$this->matchRoute($request->getRequestUri(),$post,$request->route()->parameters);
        if(!empty($params1)){
            $data=$this->getContent($params1);
            if(!empty($data)) return view('content')->with('data',$data)->with('route',$post);
        }
        $page=getSetting('route.page','/page/{slug}');
        $params2=$this->matchRoute($request->getRequestUri(),$page,$request->route()->parameters);
        if(!empty($params2)){
            $data=$this->getContent($params2,'page');
            if(!empty($data)) return view('content')->with('data',$data)->with('route',$page);
        }
        $category=getSetting('route.category','/category/{slug}');
        $params3=$this->matchRoute($request->getRequestUri(),$category,$request->route()->parameters);
        if(!empty($params3)){
            $data=$this->getMetaPost($category,$params3);
            if(!empty($data)) return view('categorized')->with('data',$data['data'])->with('category',$data['category'])->with('route',$post);
        }
        if(empty($request->route()->parameters)){
            $indexPage=getSetting('indexPage',0);
            if($indexPage>0){
                $data=$this->getContent(["id"=>$indexPage],'page');
                if(!empty($data)) return view('content')->with('data',$data)->with('route',$page);
            }else{
                $data=Content::where('type','post')->whereIn('status',[0,3])->paginate(10);
                $cr=getSetting('route.category','/category/{slug}');
                foreach ($data as $key=>$val){
                    $data[$key]['id']=$val['cid'];
                    $data[$key]['category']=$this->getCategoriesHTML($cr,$val['cid']);
                    if($val['status']=='password') $data[$key]['content']='文章加密，需要输入密码';
                    $val['content']=strip_tags($val['content']);
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
        if(strtok(urldecode($uri), '?')==$rs) return $dp;
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

    public function getContent($params,$type='post'){
        $data=array();
        if(!empty($params['id'])){
            $data=Content::where('type',$type)->find($params['id']);
        }
        else{
            if(!empty($params['slug'])){
                $query=Content::where('type',$type)->where('slug',$params['slug']);
                if(!empty($params['year']) && $query->count()>1) $query->whereYear('created_at',$params['year']);
                if(!empty($params['month']) && $query->count()>1) $query->whereMonth('created_at',$params['month']);
                if(!empty($params['day']) && $query->count()>1) $query->whereDay('created_at',$params['day']);
                if(!empty($params['date']) && $query->count()>1) $query->whereDate('created_at',$params['date']);
                $data=$query->first();
                if(!empty($params['category']) && $query->count()>1){
                    $content=Meta::where('slug',$params['category'])->metaContent()->where('type',$type)->where('slug',$params['slug'])->first();
                    if(!empty($content)) $data=$content;
                }
            }
        }
        if(!in_array($data['status'],['publish','password'])) return [];
        return $data;
    }

    public function getMetaPost($cr,$params,$type='category'){
        @$id=$params['id'];@$slug=$params['slug'];$info=[];
        $info=Meta::where('type',$type)->where('slug',$slug)->orWhere('mid',$id)->first();
        if(empty($info)) return [];
        $id=$info['mid'];
        $category=$info['name'];
        $data=Meta::where('type',$type)->find($id)->metaContent()->paginate(10);
        foreach ($data as $key=>$val){
            $data[$key]['id']=$val['cid'];
            $data[$key]['category']=$this->getCategoriesHTML($cr,$val['cid']);
            if($val['status']=='password') $data[$key]['content']='文章加密，需要输入密码';
            $val['content']=strip_tags($val['content']);
            $data[$key]['content']=mb_substr($val['content'],0,150,'UTF-8').'...';
        }
        return ["data"=>$data,"category"=>$category];
    }
}