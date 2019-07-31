<?php
namespace App\Http\Services;
use App\Repository\Admin\TaskUploadRepository;
use App\Utils\ConstantUtils;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * 上传模块代码
     * @param $file
     * @param $id
     * @return bool
     */
    public static function uploadModuleFile(UploadedFile $file, $id) {
        $originName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension(); // 扩展名
        $realPath = $file->getRealPath(); // 临时文件的绝对路径
        $dirName = date('Ym'). '/';
        $fileName = $dirName. $id. '_'. time().'.'. $ext;
        $dir = base_path().'upload1'. '/'. $dirName;
        if (is_dir($dir)) {
            chmod($dir, 0777);
            fclose(fopen($dir.'index.htm', 'w'));
        } else {
            mkdir($dir, 0777, true);
            fclose(fopen($dir.'index.htm', 'w'));
        }
        $isSuccess = Storage::disk('upload1')->put($fileName, file_get_contents($realPath));
        // $isSuccess && self::saveUploadDataToDb($id, $fileName, ConstantUtils::MODULE_UPLOAD);
        return $isSuccess;
    }


    public static function uploadWordFile(UploadedFile $file, $taskId)
    {
        $ext = $file->getClientOriginalExtension(); // 扩展名
        $realPath = $file->getRealPath(); // 临时文件的绝对路径
        $dirName = date('Y_m_d'). '/';
        $fileName = 'xlsx_config/'. $dirName. $taskId. '_word_set_'. time().'.'. $ext;
        $dir = base_path().'data'. '/'. $dirName;
        if (is_dir($dir)) {
            chmod($dir, 0777);
        } else {
            mkdir($dir, 0777, true);
        }
        $isSuccess = Storage::disk('data_path')->put($fileName, file_get_contents($realPath));
        // $isSuccess && self::saveUploadDataToDb($taskId, $fileName, ConstantUtils::TASK_URL_UPLOAD);
        return $isSuccess;
    }


    /**
     * 获取上传文件名
     * @param $ext
     * @param $id
     * @param $type
     * @return string
     */
    public static function getUploadFileName($ext, $id, $type)
    {
        $fileName = '';
        if ($type == 1) {
            $dirName = date('Ym') . '/';
            $fileName = $dirName . $id . '_' . time() . '.' . $ext;
        } else if ($type == 2) {
            $dirName = date('Y_m_d'). '/';
            $fileName = $dirName. $id. '_ua_'. time().'.'. $ext;
        } else if ($type == 3) {
            $dirName = date('Y_m_d'). '/';
            $fileName = $dirName. $id. '_source_'. time().'.'. $ext;
        } else if ($type == 4) {
            $dirName = date('Y_m_d'). '/';
            $fileName = $dirName. $id. '_task_url_'. time().'.'. $ext;
        } else if ($type == 5) {
            $dirName = date('Y_m_d'). '/';
            $fileName = $dirName. $id. '_word_set_'. time().'.'. $ext;
        }
        return $fileName;
    }


    public static function saveUploadDataToDb($id, $fileName, $type = ConstantUtils::MODULE_UPLOAD)
    {
        $exists = TaskUploadRepository::isExistUploadData($id, $type);
        if ($exists) {
            return TaskUploadRepository::updateUploadData($id, $fileName, $type);
        } else {
            return TaskUploadRepository::insertUploadData($id, $fileName, $type);
        }
    }




    /**
     * 上传ua代码
     * @param $file
     * @param $taskId
     * @return bool
     */
    public static function uploadUaFile(UploadedFile $file, $taskId) {
        $originName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension(); // 扩展名
        $realPath = $file->getRealPath(); // 临时文件的绝对路径
        $type = $file->getClientMimeType(); //image/jpeg
        $dirName = date('Y_m_d'). '/';
        $fileName = $dirName. $taskId. '_ua_'. time().'.'. $ext;
        $dir = base_path().'m_baidu'. '/'. $dirName;
        if (is_dir($dir)) {
            chmod($dir, 0777);
        } else {
            mkdir($dir, 0777, true);
        }
        $isSuccess = Storage::disk('data_path')->put($fileName, file_get_contents($realPath));
        // $isSuccess && self::saveUploadDataToDb($taskId, $fileName, ConstantUtils::UA_UPLOAD);
        return $isSuccess;
    }

    /**
     * 上传来源代码
     * @param $file
     * @param $taskId
     * @return bool
     */
    public static function uploadSourceFile(UploadedFile $file, $taskId) {
        $originName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension(); // 扩展名
        $realPath = $file->getRealPath(); // 临时文件的绝对路径
        $dirName = date('Y_m_d'). '/';
        $fileName = 'xlsx_config/'. $dirName. $taskId. '_source_'. time().'.'. $ext;
        $dir = base_path().'data'. '/'. $dirName;
        if (is_dir($dir)) {
            chmod($dir, 0777);
        } else {
            mkdir($dir, 0777, true);
        }
        $isSuccess = Storage::disk('data_path')->put($fileName, file_get_contents($realPath));
        // 这里需要将来源，对应的文件
        // $isSuccess && self::saveUploadDataToDb($taskId, $fileName, ConstantUtils::SOURCE_UPLOAD);
        return $isSuccess;
    }

    /**
     * 上传任务页面代码
     * @param $file
     * @param $taskId
     * @return bool
     */
    public static function uploadTaskPageUrlFile(UploadedFile $file, $taskId) {
        $ext = $file->getClientOriginalExtension(); // 扩展名
        $realPath = $file->getRealPath(); // 临时文件的绝对路径
        $dirName = date('Y_m_d'). '/';
        $fileName = 'xlsx_config/'.$dirName. $taskId. '_task_url_'. time().'.'. $ext;
        $dir = base_path().'data'. '/xlsx_config/'. $dirName;
        if (is_dir($dir)) {
            chmod($dir, 0777);
        } else {
            mkdir($dir, 0777, true);
        }
        $isSuccess = Storage::disk('data_path')->put($fileName, file_get_contents($realPath));
        // $isSuccess && self::saveUploadDataToDb($taskId, $fileName, ConstantUtils::TASK_URL_UPLOAD);
        return $isSuccess;
    }

}