<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use App\Media;
use App\Post;

class APIController extends Controller
{
    public function upload(Request $request){
        $files=$request->file('file');
        $arr_return=[];
        foreach ($files as $val){
            if ($val->isValid()) {
                // 获取文件相关信息
                $originalName = $val->getClientOriginalName(); // 文件原名
                $ext = $val->getClientOriginalExtension();     // 扩展名
                $realPath = $val->getRealPath();   //临时文件的绝对路径
                $type = $val->getClientMimeType();     // image/jpeg

                // 上传文件
                $filename = date('YmdHis') . '_' . uniqid() . '.' . $ext;
                // 使用我们新建的uploads本地存储空间（目录）
                //这里的uploads是配置文件的名称
                $bool = $val->move(public_path('uploads'),$filename);
                if($bool){
                    array_push($arr_return,["state"=>"SUCCESS","url"=>$filename,"original"=>$originalName,"title"=>$originalName]);
                    $this->insertUploadRecord($originalName,$filename);
                }
            }
        }
        return $arr_return;
    }

    public function delFile(Request $request){
        $filename=$request->post('filename');
        //dd($filename);
        Storage::disk('uploads')->delete($filename);
        return Media::where('filename',$filename)->delete();
    }

    public function getPostAttachment($id){
        $post=Post::find($id);
        if(empty($post)) return [];
        $files=$post->files;
        $arr_return=[];
        foreach ($files as $val){
            $data=Media::where('filename',$val)->get()->toArray()[0];
            array_push($arr_return,["state"=>"SUCCESS","url"=>$data['filename'],"original"=>$data['title'],"title"=>$data['title']]);
        }
        return $arr_return;
    }
}
