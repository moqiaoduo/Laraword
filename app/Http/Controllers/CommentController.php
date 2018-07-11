<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function addComment(Request $request){
        if(!$request->post('comment_submit')) return null;
        $cid=$request->post('cid');
        $user=$request->user();
        $uid=0;
        if($user)$uid=$user->id;
        $author=$request->post('author');
        $email=$request->post('email');
        $url=$request->post('url');
        if($uid){
            $author=$user->name;
            $email=$user->email;
            $url=$user->url;
        }
        $content=$request->post('content');
        $redirect=$request->post('redirect');
        $Agent = $_SERVER['HTTP_USER_AGENT'];
        if(!preg_match("/^(.*?):/", $url) && !empty($url)){
            $url = 'http://'.$url;
        }
        $comment=new Comment;
        $comment->cid=$cid;
        $comment->name=$author;
        $comment->email=$email;
        $comment->url=$url;
        $comment->content=cHBL($content,getSetting('commentsAllowedHTML'));
        $comment->agent=$Agent;
        $comment->ip=get_ip();
        $comment->save();
        if($request->ajax()) return ["id"=>$comment->id,"cid"=>$comment->cid,"author"=>$comment->name,"email"=>$comment->email,"url"=>$comment->url,"content"=>$comment->content,"avatar"=>"https://secure.gravatar.com/avatar/".md5($comment->email)."?s=40"];
        elseif(!empty($redirect)) return redirect($redirect."#comment-1");
    }

    public function getSubComments(Request $request,$cid,$parent=0){
        $page=$request->get('page',1);
        $perPage=$request->get('perpage',10);
        return $this->getSubComments_this($cid,$parent,$page,$perPage);
    }

    protected function getSubComments_this($cid,$parent=0,$page=1,$perPage=10){
        if($parent==0) return Comment::where('cid',$cid)->where('parent',0)->forPage($page,$perPage)->get();
        else return Comment::where('cid',$cid)->where('parent',$parent)->get();
    }

    protected function collectComments($cid,$parent=0,$page=1,$perPage=10){
        $data=$this->getSubComments_this($cid,$parent,$page,$perPage);$html='';
        if($data->isNotEmpty()){
            foreach ($data as $val){
                $sub_comments=$this->collectComments($cid,$val['id'],$page,$perPage);
                $html.=view('comments')->with('comment',$val)->with('sub_comments',$sub_comments);
            }
        }
        return $html;
    }

    public function getComments(Request $request){
        return $this->collectComments($request->get('cid'),0,$request->get('page'),$request->get('perpage'));
    }
}
