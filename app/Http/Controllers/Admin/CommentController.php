<?php

namespace App\Http\Controllers\Admin;

use App\Comment;
use App\Content;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function index(){
        $data=Comment::paginate(10);
        $routeTable=json_decode(getSetting('routeTable'),true);
        $post=getCustomUri($routeTable,'post');
        foreach ($data as $key=>$val){
            $data[$key]['cid_data']=Content::find($val['cid']);
            if(!empty($data[$key]['cid_data'])) $data[$key]['cid_data']['category']=Content::find($val['cid'])->contentMeta()->first()['slug'];
        }
        return view('admin.comment')->with('data',$data)->with('route',$post);
    }

    public function delete(Request $request){
        Comment::destroy($request->post('del'));
        return redirect()->route('admin::comment');
    }

    public function save(Request $request){
        $id=$request->post('id');
        $author=$request->post('author');
        $email=$request->post('email');
        $url=$request->post('url');
        $content=@cHBL($request->post('content'),getSetting('commentsAllowedHTML'));
        if(!preg_match("/^(.*?):/", $url) && !empty($url)){
            $url = 'http://'.$url;
        }
        $comment=Comment::find($id);
        $comment->name=$author;
        $comment->email=$email;
        $comment->url=$url;
        $comment->content=$content;
        $comment->save();
        return ["id"=>$id,"author"=>$author,"email"=>$email,"url"=>$url,"content"=>$content,"avatar"=>"https://secure.gravatar.com/avatar/".md5($email)."?s=40"];
    }

    public function update($id,$action){
        switch ($action){
            case "approve":
                $status='approve';break;
            case "spam":
                $status='spam';break;
            default:$status='pending';
        }
        Comment::find($id)->update(['status'=>$status]);
        return redirect()->route('admin::comment');
    }
}
