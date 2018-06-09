<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index(){
        return view('admin.post.list');
    }

    public function create(){

    }

    public function store(Request $request){

    }

    public function show($id){
        dd($id);
    }

    public function edit($id){

    }

    public function update(Request $request){

    }

    public function destroy(Request $request){

    }
}
