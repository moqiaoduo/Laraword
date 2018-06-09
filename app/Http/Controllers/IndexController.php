<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class IndexController extends Controller
{
    public function index(){
        $data=Post::paginate(10);
        return view('index')->with('data',$data);
    }
}