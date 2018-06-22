<?php

namespace App\Http\Controllers;

use App\Page;
use Illuminate\Http\Request;
use Storage;
use App\Media;
use App\Post;
use App\Content;

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
                    $id=$this->insertUploadRecord($originalName,$filename);
                    array_push($arr_return,["state"=>"SUCCESS","url"=>$filename,"original"=>$originalName,"title"=>$originalName,"id"=>$id]);
                }
            }
        }
        return $arr_return;
    }

    public function upload_update(Request $request){
        $file=$request->file('file')[0];
        $id=$request->route('id');
        $arr_return=[];
        if ($file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $type = $file->getClientMimeType();     // image/jpeg

            // 上传文件
            $filename = date('YmdHis') . '_' . uniqid() . '.' . $ext;
            // 使用我们新建的uploads本地存储空间（目录）
            //这里的uploads是配置文件的名称
            $bool = $file->move(public_path('uploads'),$filename);
            if($bool){
                $id=$this->updateUploadRecord($id,$filename);
                array_push($arr_return,["state"=>"SUCCESS","url"=>$filename,"original"=>$originalName,"title"=>$originalName,"id"=>$id]);
            }
        }
        return $arr_return;
    }

    public function getAttachmentInfo(Request $request){
        $filename=basename($request->post('url'));
        $rs=Content::where('content','like','%"filename":"'.$filename.'"%')->first();
        if(empty($rs)) return [];
        return ["id"=>$rs['cid'],"filename"=>json_decode($rs['content'],true)['filename']];
    }

    public function delFile(Request $request){
        $id=$request->post('id');
        $file=Content::find($id);
        Storage::disk('uploads')->delete(json_decode($file['content'],true)['filename']);
        return ['result'=>$file->delete()];
    }

    public function getPAttachment($id){
        $files=Content::where('type','attachment')->where('parent',$id)->get()->toArray();
        $arr_return=[];
        foreach ($files as $data){
            array_push($arr_return,["state"=>"SUCCESS","url"=>json_decode($data['content'],true)['filename'],"original"=>$data['title'],"title"=>$data['title']]);
        }
        return $arr_return;
    }

    public function getAttachmentUrl(Request $request){
        return Storage::disk('uploads')->url($request->post('filename'));
    }

    public function getMediaPreview($id){
        $data=Content::where('type','attachment')->find($id);
        $filename=json_decode($data['content'],true)['filename'];
        $d=Storage::disk('uploads');
        return ["media"=>$this->setMediaContent($d->path($filename),$d->url($filename)),"url"=>$d->url($filename)];
    }
}
