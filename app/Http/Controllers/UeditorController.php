<?php
/**
 * 自定义的编辑器控制器.
 * 可以观看 Zhangmazi\Ueditor\UeditorUploaderAbstract 复用类的方法,根据自身业务选择性重写覆盖
 *
 * @author ninja911<ninja911@qq.com>
 * @date   2016-08-20 22:22
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Zhangmazi\Ueditor\UeditorUploaderAbstract;
use File;

class UeditorController extends Controller
{
    use UeditorUploaderAbstract;
    /**
     * 记录上传日志(这些方法都可以重写覆盖)
     * @return mixed
     */
    protected function insertRecord($setarr, $request = null)
    {
        $title=$setarr['file_origin_name'];
        $filename=basename($setarr['file_path']);
        $this->insertUploadRecord($title,$filename);
    }

    /**
     * 验证是否合法(这些方法都可以重写覆盖)
     * @return bool|mixed
     */
    protected function checkGuard()
    {
        //如果是后端
        //return Auth::check();
        return true;
    }

    /**
     * 获取相对于public_path()根目录的相对目录
     * @return bool|mixed
     */
    protected function getRelativeDir()
    {
        return 'uploads';
    }

    /**
     * 获取保存根目录路径
     * @paraam string $driver_name 驱动名
     * @return string
     */
    protected function getSaveRootPath($driver_name = 'local')
    {
        return public_path('/');
    }

    /**
     * 删除原始文件
     * @param $file
     * @return bool
     */
    protected function deleteOriginFile($file)
    {
        File::delete($file['file_native_path']);
        File::delete($file['origin_pic_native_path']);
        return true;
    }

    protected function getJsonConfig()
    {
        $page_size = config('zhangmazi.ueditor.imageManagerListSize', 20);
        $upload_max_size = config('zhangmazi.ueditor.upload_max_size_by_k', 20480) * 1024;
        $upload_field_name = config('zhangmazi.ueditor.upload_field_name', 'upload_files');
        $attachment=explode(",",getSetting('attachmentTypes'));
        $arr_config = array(
            //上传图片配置项
            'imageActionName' => 'UploadImage', //执行上传图片的action名称
            'imageFieldName'  => $upload_field_name,  //提交的图片表单名称
            'imageMaxSize' => $upload_max_size,    //上传大小限制，单位B
            'imageAllowFiles' => config("settings.ueditor_attachmentTypes.image"), //上传图片格式显示
            'imageCompressEnable' => true, //是否压缩图片,默认是true
            'imageCompressBorder' => 3600, //图片压缩最长边限制
            'imageInsertAlign' => 'none',    //插入的图片浮动方式
            'imageUrlPrefix' => '',  //图片访问路径前缀
            'imagePathFormat' => '', //上传保存路径,可以自定义保存路径和文件名格式

            //涂鸦图片上传配置项
            'scrawlActionName' => 'UploadScrawl',    //执行上传涂鸦的action名称
            'scrawlFieldName'  => $upload_field_name, //提交的图片表单名称
            'scrawlPathFormat' => '',    //上传保存路径,可以自定义保存路径和文件名格式
            'scrawlMaxSize' => $upload_max_size,   //上传大小限制，单位B
            'scrawlUrlPrefix' => '',    //图片访问路径前缀
            'scrawlInsertAlign' => 'none',   //插入的图片浮动方式

            //截图工具上传
            'snapscreenActionName' => 'UploadSnapScreen',    //执行上传截图的action名称
            'snapscreenPathFormat' => '',    //上传保存路径,可以自定义保存路径和文件名格式
            'snapscreenUrlPrefix' => '', //图片访问路径前缀
            'snapscreenInsertAlign' => 'none',   //插入的图片浮动方式

            //抓取远程图片配置
            'catcherLocalDomain' => array(
                '127.0.0.1',
                'localhost',
                'static.oschina.net',
                '127.net',
                'cms-bucket.nosdn.127.net',
                'blog.ninja911.com'
            ),
            'catcherActionName' => 'CatchImage',    //执行抓取远程图片的action名称
            'catcherFieldName'  => $upload_field_name,    //提交的图片列表表单名称
            'catcherPathFormat' => '',   //上传保存路径,可以自定义保存路径和文件名格式
            'catcherUrlPrefix' => '',    //图片访问路径前缀
            'catcherMaxSize' => $upload_max_size,  //上传大小限制，单位B
            'catcherAllowFiles' => config("settings.ueditor_attachmentTypes.image"), //上传图片格式显示

            //上传视频配置
            'videoActionName' => 'UploadVideo', //执行上传视频的action名称
            'videoFieldName'  => $upload_field_name,  //提交的视频表单名称
            'videoPathFormat' => '', //上传保存路径,可以自定义保存路径和文件名格式
            'videoUrlPrefix' => '',  //视频访问路径前缀
            'videoMaxSize' => $upload_max_size,    //上传大小限制，单位B，默认100MB
            'videoAllowFiles' => config("settings.ueditor_attachmentTypes.video"), //上传视频格式显示

            //上传文件配置
            'fileActionName' => 'UploadFile',  //controller里,执行上传视频的action名称
            'fileFieldName'  => $upload_field_name,  //提交的文件表单名称
            'filePathFormat' => '',  //上传保存路径,可以自定义保存路径和文件名格式
            'fileUrlPrefix' => '',   //文件访问路径前缀
            'fileMaxSize' => $upload_max_size, //上传大小限制，单位B，默认50MB
            //上传文件格式显示
            'fileAllowFiles' => $attachment,

            //列出指定目录下的图片
            'imageManagerActionName' => 'ListImage',  //执行图片管理的action名称
            'imageManagerListPath' => '',    //指定要列出图片的目录
            'imageManagerListSize' => $page_size,    //每次列出文件数量
            'imageManagerUrlPrefix' => '',   //图片访问路径前缀
            'imageManagerInsertAlign' => 'none', //插入的图片浮动方式
            'imageManagerAllowFiles' => config("settings.ueditor_attachmentTypes.image"), //列出的文件类型

            //列出指定目录下的文件
            'fileManagerActionName' => 'ListFile',   //执行文件管理的action名称
            'fileManagerListPath' => '', //指定要列出文件的目录
            'fileManagerUrlPrefix' => '',    //文件访问路径前缀
            'fileManagerListSize' => $page_size, //每次列出文件数量
            'fileManagerAllowFiles' => $attachment,
        );
        return $arr_config;
    }

    protected function uploadFile($upload_field_name = 'upload_files')
    {
        $request = request();
        if (!$request->hasFile($upload_field_name)) {
            return ['state' => '请选择文件'];
        }
        $arr_return = [];
        $arr_files = $this->dealFiles($request, $upload_field_name);

        $uploader = app()->make('zhangmazi.ueditor.uploader');
        $params = $this->getUploaderParams();
        $arr_exts = explode(",",getSetting('attachmentTypes'));
        $uploader->maxSize = 0; //不限制
        $uploader->allowExts = $arr_exts;
        $uploader->isSaveOriginFile = false;
        $uploader->allowExts = $arr_exts;
        $uploader->thumb = true;
        $uploader->thumbMaxWidth = $params['arr_thumb_max_width'];
        $uploader->thumbMaxHeight = $params['arr_thumb_max_height'];
        $uploader->thumbAppointed = $params['arr_thumb_appointed'];
        $uploader->thumbWater = $params['arr_thumb_water'];
        $uploader->waterType = $params['water_picture_exists'] ? 'picture' : '';
        $uploader->waterPicture = $params['water_picture'];
        $uploader->waterPosition = 9;
        $uploader->isSaveOriginFile = $params['need_origin_pic'];
        if (app()->bound('image') && class_exists('\Intervention\Image\ImageManager')) {
            $uploader->imageHelper = app()->make('image');
        }
        if ($arr_files) {
            //$storage_driver = $this->getStorage()->getDriver();
            $storage_config = $this->getStorageConfig();
            $disk_name = $this->getStorageDiskName();
            $storage_driver = $storage_config['disks'][$disk_name]['driver'];
            $save_root_path = $this->getSaveRootPath($storage_driver);
            $relative_dir = $this->getRelativeDir();
            $visibility = !empty($storage_config['disks'][$disk_name]['visibility']) ?
                $storage_config['disks'][$disk_name]['visibility'] : null;
            $url_root = $storage_config['disks'][$disk_name]['url_root'];
            foreach ($arr_files as $file) {
                $res = $uploader->uploadFile($file, $save_root_path, $relative_dir);
                if (!empty($res[0]['file_size'])) {
                    $img_url = $relative_dir . '/'. $res[0]['file_name'];
                    if (File::exists($res[0]['file_native_path']) && $this->getStorage()->put(
                            $img_url,
                            File::get($res[0]['file_native_path']),
                            $visibility
                        )) {
                        $res[0]['link_url'] = $url_root . $img_url;
                        $arr_return[] = $res[0];
                        //删除原始文件
                        $this->deleteOriginFile($res[0]);
                        //写入DB记录
                        $this->insertRecord($res[0], $request);
                    } else {
                        $arr_return[] = ['err' => '上传失败'];
                    }
                } elseif (!empty($res['err'])) {
                    $arr_return[] = $res;
                }
            }
        }

        return $arr_return;
    }
}

?>