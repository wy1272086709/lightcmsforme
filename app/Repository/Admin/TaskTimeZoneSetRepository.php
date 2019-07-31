<?php
namespace App\Repository\Admin;
use App\Utils\ConstantUtils;
use App\Model\Admin\TaskTimeZoneSet;
class TaskTimeZoneSetRepository
{
    /**
     * 插入时区数据到数据表中
     * @param $taskId      integer
     * @param $timeZoneVal string
     * @param $timeRangeStyle  integer
     * @return boolean
     */
    public static function insertTimeZoneToDb($taskId, $timeRangeStyle, $timeZoneVal)
    {
        return TaskTimeZoneSet::query()->insertGetId([
           'task_id'         => $taskId,
           'choice_type'=> $timeRangeStyle,
           'time_zone_value' => $timeZoneVal
        ]);
    }


    public static function updateTimeZoneToDb($taskId, $timeRangeStyle, $timeZoneVal)
    {
        $res = TaskTimeZoneSet::query()->where('task_id', $taskId)
            ->update([
               'choice_type' => $timeRangeStyle,
               'time_zone_value'  => $timeZoneVal
            ]);
        return $res;
    }

    /**
     * @param $taskId
     * @param $timeRangeStyle
     * @return boolean
     */
    public static function isTimeZoneExist($taskId, $timeRangeStyle)
    {
        return TaskTimeZoneSet::query()->where('task_id', $taskId)
            ->where('choice_type', $timeRangeStyle)
            ->exists();
    }

    /**
     * 保存时区数据到表中
     * @param $taskId
     * @param $timeRangeStyle
     * @param $timeZoneVal
     * @return bool|int
     */
    public static function saveTimeZoneData($taskId, $timeRangeStyle, $timeZoneVal)
    {
        if (self::isTimeZoneExist($taskId, $timeRangeStyle)) {
            return self::updateTimeZoneToDb($taskId, $timeRangeStyle, $timeZoneVal);
        } else {
            return self::insertTimeZoneToDb($taskId, $timeRangeStyle, $timeZoneVal);
        }
    }

    /**
     * 获取时区数据
     * @param $taskId
     * @param $timeRangeStyle
     * @return array
     */
    public static function getTimeZoneRowData($taskId, $timeRangeStyle)
    {
        $zoneValue = TaskTimeZoneSet::query()->where('task_id', $taskId)
            ->where('choice_type', $timeRangeStyle)
            ->value('time_zone_value');
        return $zoneValue;
    }
}