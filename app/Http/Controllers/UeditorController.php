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
    protected function insertRecord()
    {

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
        //dd($file['file_native_path']);
        return true;
    }

    /**
     * 获取附件数据库中的信息.
     * @param int|string $pk 主键
     * @return array|object
     */
    protected function getRecordInfo($pk)
    {
        $info = [
            // 文件相对路径, 比如http://www.ninja911.com/attachments/xxxxxxx.jpg
            // attachments/xxxxxxx.jpg 就是相对路径
            'file_relative_path' => 'uploads',
        ];
        return $info;
    }

    /**
     * 编辑器获取图库清单
     * @param int $type
     * @return \Illuminate\Http\JsonResponse
     */
    protected function actionListImage($type = 1)
    {
        $new_start = 0;
        $page = 1;
        $arr_list = ['safasdf']; // ['url', 'mtime']
        $arr_return = array(
            'state' => 'SUCCESS',
            'list' => array(),
            'start' => 0,
            'total' => 0,
        );

        $cfg = $this->getJsonConfig();
        $page_size = $cfg['imageManagerListSize'] ? $cfg['imageManagerListSize'] : 20;
        $size = (int)request('size', $page_size);
        if ($size < 0) {
            $size = $page_size;
        }
        $start = (int)request('start', 0);
        if ($start < 0) {
            $start = 0;
        }

        $page = ceil($start / $size) + 1;
        if ($page < 1) {
            $page = 1;
        }
        $arr_return['list'] = $arr_list;
        $arr_return['page'] = $page;
        $arr_return['page_size'] = $page_size;

        $arr_return['total'] = 0;
        $arr_return['start'] = $start;
        return response()->json($arr_return);
    }
}

?>