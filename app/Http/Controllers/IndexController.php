<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Content;
use App\Meta;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request){
        $routeTable=json_decode(getSetting('routeTable'),true);
        $post=getCustomUri($routeTable,'post');
        $category=getCustomUri($routeTable,'category');
        $params1=$this->matchRoute($request->getRequestUri(),$post,$request->route()->parameters);
        if(!empty($params1)){
            $data=$this->getContent($params1);$comments=[];
            if(!empty($data)){
                $comments=Comment::where('cid',$data['cid'])->get();
                $data['categories']=$this->getCategoriesHTML($category,$data['cid']);
            }
            if(!empty($data)) return view('post')->with('data',$data)->with('route',$post)->with('comments',$comments);
        }
        $page=getCustomUri($routeTable,'page');
        $params2=$this->matchRoute($request->getRequestUri(),$page,$request->route()->parameters);
        if(!empty($params2)){
            $data=$this->getContent($params2,'page');$comments=[];
            if(!empty($data)) $comments=Comment::where('cid',$data['cid'])->get();
            if(!empty($data)) return view(getPageTemplateName($data))->with('data',$data)->with('route',$page)->with('comments',$comments);
        }
        $params3=$this->matchRoute($request->getRequestUri(),$category,$request->route()->parameters);
        if(!empty($params3)){
            $data=$this->getMetaPost($category,$params3);
            if(!empty($data)) return view('articles')->with('data',$data['data'])->with('category',$data['category'])->with('route',$post);
        }
        $articles=getCustomUri($routeTable,'articleList');
        $indexPage=getSetting('indexPage',0);
        $showArticleList=getSetting('showArticleList');
        if($request->getRequestUri()==$articles && $indexPage>0 && $showArticleList){
            $postsListSize=getSetting('postsListSize',10);
            $data=Content::where('type','post')->whereIn('status',['publish','password'])->orderBy('created_at','desc')->paginate($postsListSize);
            $data=$this->contentDealWith($category,$data);
            return view('articles')->with('data',$data)->with('route',$post);
        }
        if(empty($request->route()->parameters)){
            if($indexPage>0){
                $data=$this->getContent(["cid"=>$indexPage],'page');
                if(!empty($data)) return view('content')->with('data',$data);
            }else{
                $postsListSize=getSetting('postsListSize',10);
                $data=Content::where('type','post')->whereIn('status',['publish','password'])->orderBy('created_at','desc')->paginate($postsListSize);
                $data=$this->contentDealWith($category,$data);
                return view('articles')->with('data',$data)->with('route',$post);
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

    protected function getContent($params,$type='post'){
        $data=array();
        if(!empty($params['cid'])){
            $data=Content::where('type',$type)->find($params['cid']);
        }
        else{
            if(!empty($params['category'])){
                $category=Meta::where('slug',$params['category'])->first();
                if($params['slug'])
                if(!empty($category))$data=$category->metaContent()->where('type',$type)->where(function ($query) use($params) {
                    if(!empty($params['slug'])) $query->where('slug',$params['slug']);
                    if(!empty($params['cid'])) $query->where('cid',$params['cid']);
                })->first();
            }elseif(!empty($params['slug'])){
                $query=Content::where('type',$type)->where('slug',$params['slug']);
                if(!empty($params['year'])) $query->whereYear('created_at',$params['year']);
                if(!empty($params['month'])) $query->whereMonth('created_at',$params['month']);
                if(!empty($params['day'])) $query->whereDay('created_at',$params['day']);
                if(!empty($params['date'])) $query->whereDate('created_at',$params['date']);
                $data=$query->first();
            }
        }
        if(empty($data) || !in_array($data['status'],['publish','password'])) return [];
        return $data;
    }

    protected function getMetaPost($cr,$params,$type='category'){
        @$id=$params['mid'];@$slug=$params['slug'];$info=[];
        $info=Meta::where('type',$type)->where('slug',$slug)->orWhere('mid',$id)->first();
        if(empty($info)) return [];
        $id=$info['mid'];
        $category=$info['name'];
        $postsListSize=getSetting('postsListSize',10);
        $data=Meta::where('type',$type)->find($id)->metaContent()->paginate($postsListSize);
        $data=$this->contentDealWith($cr,$data);
        return ["data"=>$data,"category"=>$category];
    }

    protected function contentDealWith($cr,$data){
        foreach ($data as $key=>$val){
            $data[$key]['categories']=$this->getCategoriesHTML($cr,$val['cid']);
            $data[$key]['category']=Content::find($val['cid'])->contentMeta()->first()['slug'];
            if($val['status']=='password') $data[$key]['content']='文章加密，需要输入密码';
            $data[$key]['content']=strip_tags($val['content']);
        }
        return $data;
    }
}