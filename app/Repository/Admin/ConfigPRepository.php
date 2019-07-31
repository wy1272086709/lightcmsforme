<?php
namespace App\Repository\Admin;
use App\Model\Admin\ConfigP;

class ConfigPRepository {

    /**
     * @param $taskId
     * @param $data
     * @return int
     */
    public static function createTaskClickSetConfig($taskId, $data)
    {
        foreach ($data as $k => $row) {
            $data[$k]['task_id'] = $taskId;
        }
        return ConfigP::query()->insert($data);
    }


    /**
     * @param $taskId
     * @param $data
     * @return int
     */
    public static function updateTaskClickSetConfig($taskId, $data)
    {
        // 先删除对应的数据，再插入对应的数据.
        self::delTaskClickSetConfig($taskId);
        foreach ($data as $k => $row) {
            $data[$k]['task_id'] = $taskId;
        }
        return ConfigP::query()->insert($data);
    }

    /**
     * 保存点击设置
     * @param $taskId
     * @param $data
     * @return int
     */
    public static function saveTaskClickSetConfig($taskId, $data)
    {
        if (self::IsTaskClickSetConfigExists($taskId))
        {
            return self::updateTaskClickSetConfig($taskId, $data);
        }
        else
        {
            return self::createTaskClickSetConfig($taskId, $data);
        }
    }

    /**
     * @param $taskId
     * @return boolean
     */
    public static function IsTaskClickSetConfigExists($taskId)
    {
        return ConfigP::query()->where('task_id', $taskId)
            ->exists();
    }

    /**
     * @param $taskId
     * @return mixed
     */
    public static function delTaskClickSetConfig($taskId)
    {
        //
        return ConfigP::query()->where('task_id', $taskId)
            ->delete();
    }

    /**
     * @param $taskId
     * @return array
     */
    public static function getTaskClickSetData($taskId)
    {
        return ConfigP::query()->where('task_id', $taskId)
            ->get()
            ->toArray();
    }
}