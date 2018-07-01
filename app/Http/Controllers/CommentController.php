<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function addComment(Request $request){
        $id=$request->post('id');
        $author=$request->post('author');
        $email=$request->post('email');
        $url=$request->post('url');
        $content=$request->post('content');
        if(!preg_match("/^(.*?):/", $url) && !empty($url)){
            $url = 'http://'.$url;
        }
        return ["id"=>$id,"author"=>$author,"email"=>$email,"url"=>$url,"content"=>$content,"avatar"=>"https://secure.gravatar.com/avatar/".md5($email)."?s=40"];
    }
}
