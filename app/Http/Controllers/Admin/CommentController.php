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
            $data[$key]['cid_data']['category']=Content::find($val['cid'])->contentMeta()->first()['slug'];
        }
        return view('admin.comment')->with('data',$data)->with('route',$post);
    }
}
