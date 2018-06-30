<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;

class ThemeController extends Controller
{
    public function index(Request $request){
        $themes=Storage::disk('assets')->directories();
        $data=[];
        foreach ($themes as $val){
            if(Storage::disk('views')->exists($val)){
                $json=json_decode(Storage::disk('views')->read($val."/theme.json"),true);
                if(Storage::disk('assets')->exists($val."/img/preview.png"))
                $json['preview']=url('theme/'.$val.'/img/preview.png');
                else $json['preview']=url('img/preview.png');
                array_push($data,$json);
            }
        }
        return view('admin.theme.list',['info'=>$request->get('info'),'alert'=>$request->get('alert')])->with('data',$data)->with('current',env('APP_THEME','default'));
    }

    public function edit(Request $request,$theme){
        $type=$request->get('type','views');
        $file=$request->get('file','articles.blade.php');
        $json=json_decode(Storage::disk('views')->read($theme."/theme.json"),true);
        $dir['assets']=[];$dir['views']=[];
        foreach (Storage::disk('assets')->allFiles($theme) as $val) array_push($dir['assets'],mb_substr($val,mb_strlen($theme,'utf-8')+1,null,'utf-8'));
        foreach (Storage::disk('views')->allFiles($theme) as $val) array_push($dir['views'],mb_substr($val,mb_strlen($theme,'utf-8')+1,null,'utf-8'));
        $content=Storage::disk($type)->read($theme."/".$file);
        return view('admin.theme.edit',['info'=>$request->get('info'),'alert'=>$request->get('alert')])->with('data',$json)->with('file',$file)->with('dir',$dir)->with('type',$type)->with('content',$content)->with('theme',$theme);
    }

    public function update(Request $request,$theme){
        $type=$request->get('type','views');
        $file=$request->get('file','articles.blade.php');
        $bool=Storage::disk($type)->put($theme."/".$file,$request->post('content'));
        if($bool){$info='更新模板成功！';$alert='success';}
        else{$info='更新模板失败！';$alert='danger';}
        return redirect()->route('admin::theme.edit',[$theme,"info"=>$info,"alert"=>$alert,"type"=>$type,"file"=>$file]);
    }

    public function show($theme){
        modifyEnv(['APP_THEME'=>$theme]);
        return redirect()->route('admin::theme.index',["info"=>"更换主题成功","alert"=>"success"]);
    }

    public function destroy($theme){
        Storage::disk('assets')->deleteDirectory($theme);
        Storage::disk('views')->deleteDirectory($theme);
        return redirect()->route('admin::theme.index',["info"=>"删除主题成功","alert"=>"success"]);
    }

    public function create(){
        return view('admin.theme.create');
    }

    public function store(Request $request){
        $file=$request->file('file')[0];
        // 获取文件相关信息
        $originalName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension();     // 扩展名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        $type = $file->getClientMimeType();     // image/jpeg

        if($ext!='zip') return response(["status"=>403,"result"=>"格式不正确，请重新上传"],403);

        // 上传文件
        $filename = date('YmdHis') . '_' . uniqid() . '.' . $ext;
        // 使用我们新建的uploads本地存储空间（目录）
        //这里的uploads是配置文件的名称
        $bool = $file->move(storage_path('app/theme'),$filename);
        $rs=installTheme($filename);
        @unlink(storage_path('app/theme/'.$filename));
        return response(["status"=>$rs=='安装成功。'? 200: 403,"result"=>$rs],$rs=='安装成功。'? 200: 403);
    }
}
