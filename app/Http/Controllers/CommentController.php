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
        $comment->content=$content;
        $comment->agent=$Agent;
        $comment->ip=get_ip();
        $comment->save();
        if($request->ajax()) return ["id"=>$comment->id,"cid"=>$comment->cid,"author"=>$comment->name,"email"=>$comment->email,"url"=>$comment->url,"content"=>$comment->content,"avatar"=>"https://secure.gravatar.com/avatar/".md5($comment->email)."?s=40"];
        elseif(!empty($redirect)) return redirect($redirect."#comment-1");
    }
}
