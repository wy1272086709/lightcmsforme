<?php
namespace App\Repository\Admin;

use App\Model\Admin\AreaConfig;
use App\Model\Admin\BatchSetSource;
use App\Model\Admin\CustomSource;
use App\Model\Admin\TaskUpload;
use App\Utils\CommonUtils;
use App\Utils\ConstantUtils;
use SebastianBergmann\Diff\LongestCommonSubsequenceTest;

class TaskSourceRepository
{
    /**
     * 保存来源数据到数据库中
     * @param $taskId
     * @param $sourceStyle
     * @param $sourceData
     * @return integer
     */
    public static function insertSourceDataToDb($taskId, $sourceStyle, $sourceData)
    {
        // 自定义来源
        if ($sourceStyle == ConstantUtils::CUSTOM_SOURCE)
        {
            if (is_string($sourceData))
            {
                $sourceData = \json_decode($sourceData, true);
            }
            $newSourceData = [];
            foreach ($sourceData as $k => $row) {
                if (isset($row['no']))
                {
                    unset($row['no']);
                }
                $row['task_id']    = $taskId;
                $newSourceData[$k] = $row;
            }
            return CustomSource::query()->insert($newSourceData);
        }
        else if ($sourceStyle == ConstantUtils::BATCH_SET_SOURCE)
        {
            $sourceArr  = explode(PHP_EOL, $sourceData);
            $sourceData = join(',', $sourceArr);
            // 批量导入来源
            return BatchSetSource::query()->insertGetId([
                'task_id'        => $taskId,
                'source_content' => $sourceData
            ]);
        }
        else if ($sourceStyle == ConstantUtils::UPLOAD_SOURCE|| $sourceStyle == ConstantUtils::UPLOAD_RATIO_SOURCE)
        {
            $uploadType = $sourceStyle == ConstantUtils::UPLOAD_SOURCE ? ConstantUtils::SOURCE_UPLOAD: ConstantUtils::SOURCE_RATIO_UPLOAD;
            // 上传source 文件导入数据
            TaskUploadRepository::insertUploadData($taskId, $sourceData, $uploadType);
            // 同时将source文件的内容，假定是excel,写入到具体的数据表中.
        }
    }

    /**
     * @param $taskId
     * @param $sourceStyle
     * @param $sourceData
     * @return boolean
     */
    public static function saveSourceData($taskId, $sourceStyle, $sourceData)
    {
        if (self::isSourceDataExists($taskId, $sourceStyle)) {
            return self::updateSourceDataToDb($taskId, $sourceStyle, $sourceData);
        } else {
            return self::insertSourceDataToDb($taskId, $sourceStyle, $sourceData);
        }
    }

    /**
     * 更新来源数据到数据库中
     * @param $taskId
     * @param $sourceStyle
     * @param $sourceData
     * @return integer
     */
    public static function updateSourceDataToDb($taskId, $sourceStyle, $sourceData)
    {
        // 自定义来源
        if ($sourceStyle == ConstantUtils::CUSTOM_SOURCE)
        {
            $sourceData  = \json_decode($sourceData, true);
            CustomSource::query()->where('task_id', $taskId)->delete();
            $newSourceData = [];
            foreach ($sourceData as $k => $row) {
                if (isset($row['no']))
                {
                    unset($row['no']);
                }
                $row['task_id']    = $taskId;
                $newSourceData[$k] = $row;
            }
            return CustomSource::query()->insert($newSourceData);
        }
        else if ($sourceStyle == ConstantUtils::BATCH_SET_SOURCE)
        {
            // 批量导入来源
            return BatchSetSource::query()->where('task_id', $taskId)->update([
                'source_content' => $sourceData
            ]);
        }
        else if ($sourceStyle == ConstantUtils::UPLOAD_SOURCE || $sourceStyle == ConstantUtils::UPLOAD_RATIO_SOURCE)
        {
            $uploadType = $sourceStyle == ConstantUtils::UPLOAD_SOURCE ? ConstantUtils::SOURCE_UPLOAD: ConstantUtils::SOURCE_RATIO_UPLOAD;
            // 上传source 文件导入数据
            return TaskUploadRepository::updateUploadData($taskId, $sourceData, $uploadType);
        }
    }

    public static function isSourceDataExists($taskId, $sourceStyle)
    {
        if ($sourceStyle == ConstantUtils::CUSTOM_SOURCE)
        {
            return CustomSource::query()->where('task_id', $taskId)
                ->exists();
        }
        else if ($sourceStyle == ConstantUtils::BATCH_SET_SOURCE)
        {
            return BatchSetSource::query()->where('task_id', $taskId)
                ->exists();
        }
        else if ($sourceStyle == ConstantUtils::UPLOAD_SOURCE || $sourceStyle == ConstantUtils::UPLOAD_RATIO_SOURCE)
        {
            $uploadType = $sourceStyle == ConstantUtils::UPLOAD_SOURCE ? ConstantUtils::SOURCE_UPLOAD: ConstantUtils::SOURCE_RATIO_UPLOAD;
            return TaskUploadRepository::isExistUploadData($taskId, $uploadType);
        }
    }

    /**
     * 获取任务来源信息
     * @param $taskId
     * @param $sourceStyle
     * @return array
     */
    public static function getTaskSourceData($taskId, $sourceStyle)
    {
        // 自定义来源
        if ($sourceStyle == ConstantUtils::CUSTOM_SOURCE)
        {
            $res = CustomSource::query()->where('task_id', $taskId)
                ->get()
                ->toArray();
            return $res ? json_encode($res): '';
        }
        else if ($sourceStyle == ConstantUtils::BATCH_SET_SOURCE)
        {
            $res = BatchSetSource::query()->where('task_id', $taskId)
                ->value('source_content');
            $arr = explode(',', $res);
            return join(PHP_EOL, $arr);
        }
        else if ($sourceStyle == ConstantUtils::UPLOAD_SOURCE || $sourceStyle == ConstantUtils::UPLOAD_RATIO_SOURCE)
        {
            $uploadType = $sourceStyle == ConstantUtils::UPLOAD_SOURCE ? ConstantUtils::SOURCE_UPLOAD: ConstantUtils::SOURCE_RATIO_UPLOAD;
            return TaskUpload::query()->where('task_id', $taskId)
                ->where('upload_type', $uploadType)
                ->value('upload_path');
        }
    }


    /**
     * 获取任务来源信息
     * @param $taskId
     * @param $sourceStyle
     * @param $configId
     * @return string|array
     */
    public static function getTaskRealSourceData($taskId, $sourceStyle, $configId)
    {
        // 自定义来源
        if ($sourceStyle == ConstantUtils::CUSTOM_SOURCE)
        {
            $res = CustomSource::query()->where('task_id', $taskId)
                ->get()
                ->toArray();
            if ($res)
            {
                // url 这列
                $urls = array_column($res, 'url');
                // ratio 这列
                $ratios = array_column($res, 'ratio');
                // 首先计算点号后面的位数多少
                return CommonUtils::getValueByRatio($urls, $ratios);
            }
            return '';
        }
        else if ($sourceStyle == ConstantUtils::BATCH_SET_SOURCE)
        {
            $res = BatchSetSource::query()->where('task_id', $taskId)
                ->value('source_content');
            $arr = explode(',', $res);
            $k = array_rand($arr, 1);
            return $arr[$k];
        }
        else if ($sourceStyle == ConstantUtils::UPLOAD_SOURCE|| $sourceStyle == ConstantUtils::UPLOAD_RATIO_SOURCE)
        {
            $res = AreaConfig::query()->where('id', $configId)
                ->value('rf_list');
            if (strpos($res, ']')!== false) {
                $arr = \json_decode($res, true);
                $urlArr = array_column($arr, 'url');
                $ratioArr = array_column($arr, 'ratio');
                $ratioArr = self::transformRatioToNumber($ratioArr);
                return CommonUtils::getValueByRatio($urlArr, $ratioArr);
            } else {
                return '';
            }
        }
    }

    /**
     * @param $ratios
     * @return array
     */
    public static function transformRatioToNumber($ratios)
    {
        $lenArr = [];
        foreach ($ratios as $val)
        {
            $tmpStr = strstr($val, '.');
            if ($tmpStr) {
                $lenArr[] = strlen($tmpStr) - 2;// 点号和百分号去掉
            } else {
                $lenArr[] = 0;
            }
        }
        $lenMax = max($lenArr);
        $ratios = array_map(function($ratio) use($lenMax){
            $len = strlen($ratio);
            $val = substr($ratio, 0);
            return ((int)$val) * pow(10, $lenMax);
        }, $ratios);
        return $ratios;
    }

}