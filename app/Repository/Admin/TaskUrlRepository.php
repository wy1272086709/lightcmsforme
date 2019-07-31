<?php


namespace App\Repository\Admin;


use App\Model\Admin\AreaConfig;
use App\Model\Admin\TaskUpload;
use App\Model\Admin\TaskUrl;
use App\Utils\CommonUtils;
use App\Utils\ConstantUtils;
use SebastianBergmann\CodeCoverage\Report\PHP;

class TaskUrlRepository
{
    public static function addTaskUrl($taskId, $taskUrlStyle, $taskUrlContent)
    {
        if ($taskUrlStyle == ConstantUtils::TASK_URL_BATCH_EXPORT_STYLE) {
            return TaskUrl::query()->insertGetId([
                'task_id' => $taskId,
                't_s'     => CommonUtils::urlAddCommaTransform($taskUrlContent)
            ]);
        } else {
            // 文件上传,对应的文件信息存入数据表中
            return TaskUploadRepository::insertUploadData($taskId, $taskUrlContent, ConstantUtils::TASK_URL_UPLOAD);
        }
    }

    public static function getTaskUrlData($taskId, $taskUrlStyle)
    {
        if ($taskUrlStyle == ConstantUtils::TASK_URL_BATCH_EXPORT_STYLE)
        {
            $tsVal = TaskUrl::query()->where('task_id', $taskId)
                ->value('t_s');
            $tsArr = explode(',', $tsVal);
            return implode(PHP_EOL, $tsArr);
        }
        else
        {
            return TaskUpload::query()->where('task_id', $taskId)
                ->where('upload_type', ConstantUtils::TASK_URL_UPLOAD)
                ->value('upload_path');
        }
    }

    public static function getRealTaskUrlData($taskId, $taskUrlStyle, $configId)
    {
        if ($taskUrlStyle == ConstantUtils::TASK_URL_BATCH_EXPORT_STYLE)
        {
            $sStr = TaskUrl::query()->where('task_id', $taskId)
                ->value('t_s');
            return explode(',', $sStr);
        }
        else
        {
            $res = AreaConfig::query()->find($configId)
                ->value('s_list');
            if ($res) {
                $arr = \json_decode($res, true);
                $urlArr = [];
                foreach ($arr as $row) {
                    $urlArr[] = $row['url'];
                }
            } else {
                return [];
            }
            return $urlArr;
        }
    }

    public static function editTaskUrl($taskId, $taskUrlStyle, $taskUrlContent)
    {
        if ($taskUrlStyle == ConstantUtils::TASK_URL_BATCH_EXPORT_STYLE) {
            return TaskUrl::query()->where('task_id', $taskId)
                ->update(['t_s' => CommonUtils::urlAddCommaTransform($taskUrlContent)]);
        } else {
            return TaskUploadRepository::saveUploadData($taskId, ConstantUtils::TASK_URL_UPLOAD, $taskUrlContent);
        }
    }

    public static function isExistTaskUrl($taskId, $taskUrlStyle)
    {
        if ($taskUrlStyle == ConstantUtils::TASK_URL_BATCH_EXPORT_STYLE) {
            return TaskUrl::query()->where(
                'task_id', $taskId)
                ->exists();
        } else {
            return TaskUploadRepository::isExistUploadData($taskId, ConstantUtils::TASK_URL_UPLOAD);
        }

    }

    public static function saveTaskUrl($taskId, $taskUrlStyle, $taskUrlContent)
    {
        if (self::isExistTaskUrl($taskId, $taskUrlStyle))
        {
            return self::editTaskUrl($taskId, $taskUrlStyle, $taskUrlContent);
        }
        else
        {
            return  self::addTaskUrl($taskId, $taskUrlStyle, $taskUrlContent);
        }
    }

}