<?php


namespace App\Repository\Admin;


use App\Model\Admin\BatchSetUa;
use App\Model\Admin\CustomUa;
use App\Model\Admin\TaskFixIssued;
use App\Model\Admin\TaskUpload;
use App\Utils\CommonUtils;
use App\Utils\ConstantUtils;
use App\Model\Admin\TaskPlanIssued;

class TaskUaRepository
{
    /**
     * 保存ua 数据到数据库中
     * @param $taskId
     * @param $uaStyle
     * @param $uaData
     * @return integer
     */
    public static function insertUaDataToDb($taskId, $uaStyle, $uaData)
    {
        // 自定义UA
        if ($uaStyle == ConstantUtils::CUSTOM_UA)
        {
            $uaData  = \json_decode($uaData, true);
            $newUaData = [];
            foreach ($uaData as $k => $row) {
                if (isset($row['no']))
                {
                    unset($row['no']);
                }
                $row['task_id']    = $taskId;
                $newUaData[$k] = $row;
            }
            return CustomUa::query()->insert($newUaData);
        }
        else if ($uaStyle == ConstantUtils::BATCH_SET_UA)
        {
            $uaData = CommonUtils::urlAddCommaTransform($uaData);
            // 批量导入ua
            return BatchSetUa::query()->insert([
                'task_id' => $taskId,
                'ua_content' => $uaData
            ]);
        } else if ($uaStyle == ConstantUtils::UPLOAD_UA) {
            // 保存ua 上传数据到表中.
            TaskUploadRepository::insertUploadData($taskId, $uaData, ConstantUtils::UA_UPLOAD);
        }
    }

    /**
     * @param $taskId
     * @param $uaStyle
     * @return boolean
     */
    public static function isTaskUaExists($taskId, $uaStyle)
    {
        if ($uaStyle == ConstantUtils::CUSTOM_UA)
        {
            return CustomUa::query()->where('task_id', $taskId)
                ->exists();
        }
        else if ($uaStyle == ConstantUtils::BATCH_SET_UA)
        {
            return BatchSetUa::query()->where('task_id', $taskId)
                ->exists();
        }
        else if ($uaStyle == ConstantUtils::UPLOAD_UA)
        {
            return TaskUploadRepository::isExistUploadData($taskId, ConstantUtils::UA_UPLOAD);
        }
    }


    /**
     * 保存UA 数据到DB 中
     * @param $taskId
     * @param $uaStyle
     * @param $uaData
     * @return int
     */
    public static function saveUaDataToDb($taskId, $uaStyle, $uaData)
    {
        if ($uaStyle == ConstantUtils::AUTO_UA)
        {
            return true;
        }
        if (self::isTaskUaExists($taskId, $uaStyle)) {
            return self::updateUaDataToDb($taskId, $uaStyle, $uaData);
        } else {
            return self::insertUaDataToDb($taskId, $uaStyle, $uaData);
        }
    }

    /**
     * 保存ua 数据到数据库中
     * @param $taskId
     * @param $uaStyle
     * @param $uaData
     * @return integer
     */
    public static function updateUaDataToDb($taskId, $uaStyle, $uaData)
    {
        // 自定义UA
        if ($uaStyle == ConstantUtils::CUSTOM_UA)
        {
            $uaData  = \json_decode($uaData, true);
            // 先删除对应的记录
            CustomUa::query()->where('task_id', $taskId)
                ->delete();
            $newUaData = [];
            foreach ($uaData as $k => $row) {
                if (isset($row['no']))
                {
                    unset($row['no']);
                }
                $row['task_id']    = $taskId;
                $newUaData[$k] = $row;
            }
            return CustomUa::query()->insert($newUaData);
        }
        else if ($uaStyle == ConstantUtils::BATCH_SET_UA)
        {
            $uaData = CommonUtils::urlAddCommaTransform($uaData);
            // 批量导入ua 的，执行更新操作
            return BatchSetUa::query()->where('task_id', $taskId)->update([
                'ua_content' => $uaData
            ]);
        } else if ($uaStyle == ConstantUtils::UPLOAD_UA) {
            // 保存ua 数据到数据表中
            return TaskUploadRepository::updateUploadData($taskId, $uaData, ConstantUtils::UA_UPLOAD);
        }
    }


    /**
     * 获取任务的ua数据
     * @param $taskId
     * @param $uaStyle
     * @return array
     */
    public static function getTaskUaData($taskId, $uaStyle)
    {
        if ($uaStyle == ConstantUtils::CUSTOM_UA)
        {
            $uaArr = CustomUa::query()->where('task_id', $taskId)
            ->get()
            ->toArray();
            return $uaArr ? json_encode($uaArr, true): [];
        }
        else if ($uaStyle == ConstantUtils::BATCH_SET_UA)
        {
            $uaStr = BatchSetUa::query()->where('task_id', $taskId)
                ->value('ua_content');
            $uaStr = CommonUtils::urlAddEolTransform($uaStr);
            return $uaStr;
        }
        else if ($uaStyle == ConstantUtils::UPLOAD_UA)
        {
            // 取表中路径字段
            $uploadPath = TaskUpload::query()->where('task_id', $taskId)
                ->where('upload_type', ConstantUtils::UA_UPLOAD)
            ->value('upload_path');
            return $uploadPath;
        }
    }
}