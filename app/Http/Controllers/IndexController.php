<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(){
        view('default.index')->setPath(resource_path('view\\'.env('APP_THEME','default')));
        return view('index');
    }
}