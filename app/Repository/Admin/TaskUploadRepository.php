<?php
namespace App\Repository\Admin;
use App\Http\Services\UploadService;
use App\Model\Admin\TaskUpload;
use App\Utils\CommonUtils;
use App\Utils\ConstantUtils;

class TaskUploadRepository {
    /**
     *
     * @param $taskId
     * @param $upload_path
     * @param $uploadType
     * @return boolean
     */
    public static function insertUploadData($taskId, $upload_path, $uploadType) {
        $builder  = TaskUpload::query();
        return $builder->insertGetId([
            'task_id'     => $taskId,
            'upload_path' => $upload_path,
            'upload_type' => $uploadType
        ]);
    }


    public static function updateUploadData($taskId, $upload_path, $uploadType) {
        $re = TaskUpload::query()->where('task_id', $taskId)
            ->where('upload_type', $uploadType)
            ->update([
                'upload_path' => $upload_path,
            ]);
        return $re;
    }


    public static function getUploadedData($taskId)
    {
        $uploadInfoRes = TaskUpload::query()->where('task_id', $taskId)
            ->pluck('upload_path','upload_type')
            ->toArray();
        return $uploadInfoRes ? $uploadInfoRes: [];
    }

    /**
     * 是否存在上传文件数据
     * @param $taskId
     * @param $uploadType
     * @return boolean 是否存在对应的记录
     */
    public static function isExistUploadData($taskId, $uploadType) {
        $isExists = TaskUpload::query()->where('task_id', $taskId)
            ->where('upload_type', $uploadType)
            ->exists();
        return $isExists;
    }

    /**
     * 更新tb_area_config 表中的rf_list(来源), s_list(任务页面), gjc_list(关键词)字段。
     * 如果不更新，上传的文件内容，在接口的地方，就无法使用到，
     * 后续的逻辑，没法进行，就需要对这个逻辑，进行重新定义
     * @param $configId
     * @param $uploadPath
     * @param $uploadType
     */
    public static function updateConfigData($configId, $uploadPath, $uploadType = ConstantUtils::TASK_URL_UPLOAD)
    {
        // 上传文件的同时,将文件对应的Excel内容，写入到tb_area_config 表中对应的字段里面.
        $field = self::getConfigFields($uploadType);
        $common = new CommonUtils();
        $common->setExcelDataToTable($uploadPath, $configId, $field, $uploadType);
    }

    public static function getConfigFields($uploadType)
    {
        if ($uploadType == ConstantUtils::SOURCE_UPLOAD)
        {
            return 'rf_list';
        }
        else if ($uploadType == ConstantUtils::TASK_URL_UPLOAD)
        {
            return 's_list';
        }
        else if ($uploadType == ConstantUtils::WORD_UPLOAD)
        {
            return 'gjc_list';
        }
        else if ($uploadType == ConstantUtils::SOURCE_RATIO_UPLOAD)
        {
            return 'rf_list';
        }
    }

    /**
     * 保存上传文件的数据到DB 中
     * @param $taskId
     * @param $uploadType
     * @param $upload_path
     * @return bool|int
     */
    public static function saveUploadData($taskId, $uploadType, $upload_path)
    {
        if (self::isExistUploadData($taskId, $uploadType))
        {
            return self::updateUploadData($taskId, $upload_path, $uploadType);
        }
        else
        {
            return self::insertUploadData($taskId, $upload_path, $uploadType);
        }
    }
}