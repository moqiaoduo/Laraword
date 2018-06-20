<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/6/20
 * Time: 13:15
 */

namespace App\Upgrade;
use DB;

class Upgrade20180620
{
    public function run(){
        $category_old=DB::table('category')->get();
        foreach ($category_old as $val){
            DB::table('metas')->insert([
                "mid"=>$val->id,
                "name"=>$val->title,
                "slug"=>$val->slug,
                "created_at"=>$val->created_at,
                "updated_at"=>$val->updated_at,
                "description"=>$val->description,
                "type"=>"category",
            ]);
        }
        $post_old=DB::table('posts')->get();
        foreach ($post_old as $val){
            $category=json_decode($val->category,true);
            switch ($val->status){
                case 0: $status="publish";break;
                case 1: $status="publish";break;
                case 2: $status="hidden";break;
                case 3: $status="password";break;
                case 4: $status="private";break;
                case 5: $status="waiting";break;
                default: $status='unknown';
            }
            $type='post';
            if($val->status==1) $type='post_draft';
            $id=DB::table('contents')->insertGetId([
                "title"=>$val->title,
                "slug"=>$val->slug,
                "created_at"=>$val->created_at,
                "updated_at"=>$val->updated_at,
                "content"=>$val->content,
                "uid"=>$val->uid,
                "status"=>$status,
                "type"=>$type,
            ]);
            $draft=DB::table('drafts')->where('type','post')->where('post_id',$val->id);
            if($draft->exists()){
                $post_draft=$draft->get()->toArray()[0];
                DB::table('contents')->insert([
                    "title"=>$post_draft->title,
                    "slug"=>$val->slug,
                    "created_at"=>$post_draft->created_at,
                    "updated_at"=>$post_draft->updated_at,
                    "content"=>$post_draft->content,
                    "uid"=>$post_draft->uid,
                    "status"=>$status,
                    "type"=>"post_draft",
                    "parent"=>$id,
                ]);
            }
            foreach ($category as $v){
                if($v>0) DB::table('relationships')->insert(["cid"=>$id,"mid"=>$v]);
            }
        }
        $page_old=DB::table('pages')->get();
        foreach ($page_old as $val){
            switch ($val->status){
                case 0: $status="publish";break;
                case 1: $status="publish";break;
                case 2: $status="hidden";break;
                case 3: $status="password";break;
                case 4: $status="private";break;
                case 5: $status="waiting";break;
                default: $status='unknown';
            }
            $type='page';
            if($val->status==1) $type='page_draft';
            $id=DB::table('contents')->insertGetId([
                "title"=>$val->title,
                "slug"=>$val->slug,
                "created_at"=>$val->created_at,
                "updated_at"=>$val->updated_at,
                "content"=>$val->content,
                "uid"=>$val->uid,
                "status"=>$status,
                "type"=>$type,
            ]);
            $draft=DB::table('drafts')->where('type','page')->where('post_id',$val->id);
            if($draft->exists()){
                $post_draft=$draft->get()->toArray()[0];
                DB::table('contents')->insert([
                    "title"=>$post_draft->title,
                    "slug"=>$val->slug,
                    "created_at"=>$post_draft->created_at,
                    "updated_at"=>$post_draft->updated_at,
                    "content"=>$post_draft->content,
                    "uid"=>$post_draft->uid,
                    "status"=>$status,
                    "type"=>"page_draft",
                    "parent"=>$id,
                ]);
            }
        }
    }
}