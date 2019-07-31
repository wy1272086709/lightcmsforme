<?php
namespace App\Repository\Admin;

use App\Model\Admin\AreaTaskMap;

class AreaTaskMapRepository
{
    /**
     * 获取area_task_map 对应的task 数据
     * @param $taskId
     * @return array
     */
    public static function getTaskMapData($taskId)
    {
        $res = AreaTaskMap::query()->where('task_id', $taskId)
            ->get()
        ->toArray();
        return $res;
    }


    /**
     * @param $taskId
     * @param $data
     * @return int
     */
    public static function saveAreaTaskMapData($taskId, $data)
    {
        $isExists = self::isAreaTaskMapDataExist($taskId);
        if ($isExists)
        {
            return self::updateAreaTaskMapData($taskId, $data);
        }
        else
        {
            return self::insertAreaTaskMapData($taskId, $data);
        }
    }

    /**
     * @param $taskId
     * @return bool
     */
    public static function isAreaTaskMapDataExist($taskId)
    {
        return AreaTaskMap::query()->where('task_id', $taskId)
            ->exists();
    }

    /**
     *
     * @param $taskId
     * @param $data
     * @return int
     */
    public static function insertAreaTaskMapData($taskId, $data)
    {
        foreach ($data as $k => $row)
        {
            $data[$k]['task_id'] = $taskId;
        }
        return AreaTaskMap::query()->insert($data);
    }

    /**
     * 获取地区数据
     * @param $taskId
     * @return array
     */
    public static function getAreaTasMapRow($taskId)
    {
        $areaData = AreaTaskMap::query()->where('task_id', $taskId)
            ->select([ 'province_id', 'city_id', 'num_ip', 'dates' ])
            ->get()
            ->toArray();
        return $areaData;
    }

    /**
     * @param $taskId
     * @param $data
     * @return int
     */
    public static function updateAreaTaskMapData($taskId, $data)
    {
        self::delAreaTaskMapData($taskId);
        return self::insertAreaTaskMapData($taskId, $data);
    }

    public static function delAreaTaskMapData($taskId)
    {
        return AreaTaskMap::query()->where('task_id', $taskId)
            ->delete();
    }



}