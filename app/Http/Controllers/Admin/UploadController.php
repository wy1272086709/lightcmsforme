<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Services\UploadService;
use App\Repository\Admin\ModuleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{

    public static $methodMap = [
        1 => 'uploadModuleFile',
        2 => 'uploadUaFile',
        3 => 'uploadSourceFile',
        4 => 'uploadTaskPageUrlFile',
        5 => 'uploadWordFile',
    ];
    public function run(Request $request)
    {
        if ($request->isMethod('post'))
        {
            $file = $request->file('file1');
            // 文件上传的类型,
            $type = $request->get('type');
            $method = isset(self::$methodMap[$type]) ? self::$methodMap[$type]: '';
            if ($type != 1) {
                $idStr = $request->get('taskId');
            } else {
                $idStr = $this->getId();
            }
            $fileName = '';
            if ($file->isValid())
            {
                $ext = $file->getClientOriginalExtension();
                $isSuccess = UploadService::$method($file, $idStr);
                if ($isSuccess) {
                    return [
                        'code'     => 0,
                        'msg'      => '',
                        'filepath' => UploadService::getUploadFileName($ext, $idStr, $type)
                    ];
                } else {
                    return [
                        'code' => 1,
                        'msg'  => '',
                        'filepath' => ''
                    ];
                }
            }
        }
    }


    private function getId()
    {
        $id = request()->get('id');
        $maxId = ModuleRepository::getMaxId();
        return $id? $id: $maxId->id+1;
    }
}