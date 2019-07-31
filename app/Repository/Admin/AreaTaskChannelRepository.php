<?php
namespace App\Repository\Admin;
use App\Model\Admin\AreaTaskChannel;
use Illuminate\Support\Facades\DB;

class AreaTaskChannelRepository
{
    /**
     * @param $taskId
     * @return mixed
     */
    public static function getChannelData($taskId)
    {
        $sql = "select * from tb_area_task_channel where task_id=?";
        $res = DB::select($sql, [$taskId]);
        return $res;
    }


    /**
     * 获取渠道数据
     * @param $taskIds
     * @return array
     */
    public static function getAreaTaskChannelData($taskIds)
    {
        if (!$taskIds) {
            return [];
        }
        $sql = "select * from tb_area_task_channel where task_id IN(". implode(',', $taskIds). ")";
        $res = DB::select($sql);
        return $res ? $res: [ ];
    }

    /**
     * 获取渠道数据
     * @param $taskId
     * @return array
     */
    public static function getAreaTaskChannelRow($taskId)
    {
        $channelData = AreaTaskChannel::query()->where('task_id', $taskId)
            ->select([ 'g_id', 'u_id', 'ratio' ])
            ->get()
            ->toArray();
        return $channelData;
    }

    /**
     * @param $taskId
     * @param $data
     * @return int
     */
    public static function saveAreaTaskChannelData($taskId, $data)
    {
        $isExists = self::isAreaTaskChannelDataExist($taskId);
        if ($isExists)
        {
            return self::updateAreaTaskChannelData($taskId, $data);
        }
        else
        {
            return self::insertAreaTaskChannelData($taskId, $data);
        }
    }

    /**
     * @param $taskId
     * @return bool
     */
    public static function isAreaTaskChannelDataExist($taskId)
    {
        return AreaTaskChannel::query()->where('task_id', $taskId)
            ->exists();
    }

    /**
     *
     * @param $taskId
     * @param $data
     * @return int
     */
    public static function insertAreaTaskChannelData($taskId, $data)
    {
        foreach ($data as $k => $row)
        {
            $data[$k]['task_id'] = $taskId;
        }
        return AreaTaskChannel::query()->insert($data);
    }

    /**
     * @param $taskId
     * @param $data
     * @return int
     */
    public static function updateAreaTaskChannelData($taskId, $data)
    {
        self::delAreaTaskChannelData($taskId);
        return self::insertAreaTaskChannelData($taskId, $data);
    }

    /**
     * @param $taskId
     * @return mixed
     */
    public static function delAreaTaskChannelData($taskId)
    {
        return AreaTaskChannel::query()->where('task_id', $taskId)
            ->delete();
    }

}