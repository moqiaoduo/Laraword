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
use App\Media;
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
}

?>