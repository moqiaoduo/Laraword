<?php

namespace App\Http\Controllers;

use App\Content;
use Illuminate\Http\Request;
use Storage;

class APIController extends Controller
{
    public function upload(Request $request){
        $files=$request->file('file');
        $arr_return=[];
        foreach ($files as $val){
            if ($val->isValid()) {
                array_push($arr_return,$this->addOrUpdateFile($val));
            }
        }
        return $arr_return;
    }

    public function upload_update(Request $request){
        $file=$request->file('file')[0];
        $id=$request->route('id');
        $arr_return=[];
        if ($file->isValid()) {
            $arr_return=$this->addOrUpdateFile($file,'update',$id);
        }
        return $arr_return;
    }

    protected function addOrUpdateFile($val,$method='add',$id=0){
        // 获取文件相关信息
        $originalName = $val->getClientOriginalName(); // 文件原名
        $ext = $val->getClientOriginalExtension();     // 扩展名
        $realPath = $val->getRealPath();   //临时文件的绝对路径
        $type = $val->getClientMimeType();     // image/jpeg

        if(!in_array($ext,explode(',',getSetting('attachmentTypes')))) return ["state"=>"FAIL","reason"=>"文件格式不支持","title"=>$originalName];

        // 上传文件
        $filename = date('YmdHis') . '_' . uniqid() . '.' . $ext;
        // 使用我们新建的uploads本地存储空间（目录）
        //这里的uploads是配置文件的名称
        $bool = $val->move(public_path('uploads'),$filename);
        $arr_return=["state"=>"FAIL","id"=>$id,"url"=>$filename,"origin"=>$originalName,"title"=>$originalName];
        if($bool){
            if($method=='add') $id=$this->insertUploadRecord($originalName,$filename);
            else $id=$this->updateUploadRecord($id,$filename);
            $arr_return['id']=$id;$arr_return['state']="SUCCESS";
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
        return ["media"=>$this->setMediaContent($d->path($filename),$d->url($filename)),"url"=>$d->url($filename),"size"=>tosize($d->size($filename))];
    }
}
