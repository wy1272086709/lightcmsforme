<?php
/**
 * Date: 2019/3/16 Time: 13:42
 *
 * @author  Eddy <cumtsjh@163.com>
 * @version v1.0.0
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class NEditorController extends Controller
{
    /**
     * 基础功能-图片上传
     *
     * @param Request $request
     * @return array
     */
    public function serve(Request $request, $type)
    {
        if (!method_exists(\self::class, $type)) {
            return [
                'code' => 1,
                'msg' => '未知操作'
            ];
        }

        return call_user_func(\self::class . '::' . $type, $request);
    }

    protected function uploadImage(Request $request)
    {
        if (!$request->hasFile('file')) {
            return [
                'code' => 2,
                'msg' => '非法请求'
            ];
        }
        $file = $request->file('file');
        if (!$this->isValidImage($file)) {
            return [
                'code' => 3,
                'msg' => '文件不合要求'
            ];
        }

        $result = $file->store(date('Ym'), config('light.neditor.disk'));
        if (!$result) {
            return [
                'code' => 3,
                'msg' => '上传失败'
            ];
        }

        return [
            'code' => 200,
            'msg' => '',
            'url' => Storage::disk(config('light.neditor.disk'))->url($result),
        ];
    }

    protected function isValidImage(UploadedFile $file)
    {
        if (!$file->isValid() ||
            $file->getSize() > config('light.neditor.upload.imageMaxSize') ||
            !in_array(
                '.' . strtolower($file->getClientOriginalExtension()),
                config('light.neditor.upload.imageAllowFiles')
            )
        ) {
            return false;
        }

        return true;
    }
}
