<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function addComment(Request $request){
        $id=$request->post('id');
        $cid=$request->post('cid');
        $author=$request->post('author');
        $email=$request->post('email');
        $url=$request->post('url');
        $content=$request->post('content');
        $redirect=$request->post('redirect');
        if(!preg_match("/^(.*?):/", $url) && !empty($url)){
            $url = 'http://'.$url;
        }
        if($request->ajax()) return ["id"=>$id,"cid"=>$cid,"author"=>$author,"email"=>$email,"url"=>$url,"content"=>$content,"avatar"=>"https://secure.gravatar.com/avatar/".md5($email)."?s=40"];
        elseif(!empty($redirect)) return redirect($redirect);
    }
}
