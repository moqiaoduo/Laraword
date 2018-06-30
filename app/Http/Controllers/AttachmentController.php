<?php

namespace App\Http\Controllers;

use App\Content;
use Illuminate\Http\Request;
use Storage;

class AttachmentController extends Controller
{
    public function show($id){
        $data=Content::where('type','attachment')->find($id);
        if(!empty($data)){
            $t=json_decode($data['content'],true);
            $filename=$t['filename'];
            $description=$t['description'];
            $path=Storage::disk('uploads')->path($filename);
            $url=Storage::disk('uploads')->url($filename);
            $data['content']=$this->setContent($path,$url,$data['title'],$description);
            return view('content')->with('data',$data);
        }
        return view('errors.404');
    }

    protected function setContent($path,$url,$title,$description){
        return $this->setMediaContent($path,$url).'<br>'.$description.'<br>附件下载：<a target=_blank href="'.$url.'">'.$title.'</a>';
    }
}
