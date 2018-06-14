<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class PostController extends Controller
{
    public function content(Request $request){
        $id=$request->route('id');
        $year=$request->route('year');
        $month=$request->route('month');
        $day=$request->route('day');
        $date=$request->route('date');
        $slug=$request->route('slug');
        $category=$request->route('category');
        $data=array();
        if(!empty($id)) $data=Post::find($id);
        else{
            if(!empty($slug)){
                $query=Post::where('slug',$slug);
                if(!empty($year) && count($query->get()->toArray())>1) $query->whereYear('created_at',$year);
                if(!empty($month) && count($query->get()->toArray())>1) $query->whereMonth('created_at',$month);
                if(!empty($day) && count($query->get()->toArray())>1) $query->whereDay('created_at',$day);
                if(!empty($date) && count($query->get()->toArray())>1) $query->whereDate('created_at',$date);
                if(!empty($category) && $category!='uncategorized' && count($query->get()->toArray())>1){
                    $category=Category::where('slug',$category)->get()->toArray()[0];
                    if(!empty($category)) $query->whereJsonContains('category',$category['id']);
                }
                $data=$query->get()->toArray()[0];
            }
        }
        return view('content')->with('data',$data);
    }
}
