<?php
namespace App\Http\Services;
use App\Repository\Admin\AreaTaskRepository;
use App\Repository\Admin\StatisticsRepository;
use App\Repository\Admin\TaskStatisticsRepository;

class StatisticsService
{
    /**
     * 获取流量业务的结果列表
     * @param $date
     * @param $perPage
     * @param $page
     * @return array
     */
    public static function ippvStatisticsRes($date, $perPage, $page)
    {
        if ($date == date('Y-m-d'))
        {
            $res = get_today_return_log($date);
        }
        else
        {
            $res = TaskStatisticsRepository::ippvRes($date, $perPage, $page);
        }
        $res = self::formatPageRes($date, $res);
        return $res;
    }


    /**
     * 获取流量业务的总数
     * @param $date
     * @return array
     */
    public static function ippvStatisticsCount($date)
    {
        $cntRes = TaskStatisticsRepository::ippvCount($date);
        $cntObj = current($cntRes);
        return $cntObj->cnt;
    }


    public static function formatPageRes($date, $res)
    {
        foreach ($res as $k => $row) {
            $res[$k] = (array) $row;
        }
        $taskIds = array_column($res, 'taskID');
        $results = AreaTaskRepository::getTaskRowMap($taskIds);
        foreach ($res as $k => $row)
        {
            $per = bili($row['succ_pv'], $row['PV'], 90, 70);
            $res[$k]['per'] = $per;
            $res[$k]['task_name'] = isset($results[$row['taskID']]['task_name']) ? $results[$row['taskID']]['task_name']: '';
            $taskInfo = isset($results[$row['taskID']]) ? $results[$row['taskID']]: [];
            if (isset($taskInfo['time_zone'])) {
                $timeZone = !empty($taskInfo['time_zone']) ? \json_decode($taskInfo['time_zone'], true): [];
                if (!empty($timeZone)) {
                    $res[$k]['t_today'] = array_sum($timeZone);
                } else {
                    $res[$k]['t_today'] = 0;
                }
                if (isset($taskInfo['time_edTime'])) {
                    $res[$k]['t_now'] = t_now($timeZone, $taskInfo['time_edTime']);
                } else {
                    $res[$k]['t_now'] = 0;
                }
                $res[$k]['ippvViewUrl'] = route('admin::statistics.ipPvHourView', ['id' => $row['taskID']]). '?date='. $date;
            }
        }
        return $res;
    }

    /**
     * @param $taskId
     * @return int
     */
    public static function countHourIppv($taskId)
    {
        $cnt = TaskStatisticsRepository::countHourIppv($taskId);
        return $cnt;
    }

    /**
     * @param $taskId
     * @param $page
     * @param $perPage
     * @return array
     */
    public static function getHourIppvRes($taskId, $page, $perPage)
    {
        $res = TaskStatisticsRepository::getHourIppvRes($taskId, $page, $perPage);
        $res = self::formatHourIppvRes($res, $taskId);
        return $res;
    }

    public static function getTaskName($taskId)
    {
        $taskNameArr = AreaTaskRepository::getTaskName([ $taskId ]);
        $taskName = isset($taskNameArr[$taskId]) ? $taskNameArr[$taskId]: '';
        return $taskName;
    }

    /**
     * @param $res
     * @param $taskId
     * @return array
     */
    public static function formatHourIppvRes($res, $taskId)
    {
        $data = [];
        foreach ($res as $k => $row)
        {
            $row  = (array) $row;
            $hour = substr($row['date'], 8);
            $date = date('Y-m-d', strtotime(substr($row['date'], 0, 8)));
            if ($row['type'] == 1)
            {
                $data[ $date ][$hour]['xf_pv'] = $row['PV'];
            }
            else
            {
                $data[ $date ][$hour]['_pv'] = $row['PV'];
            }
        }
        krsort($data);
        // 获取taskNameArr
        $dataRes = [];
        foreach ($data as $date => $row)
        {
            $newRow = [
                'date' => $date
            ];
            if(array_sum(array_column($row, 'xf_pv'))<1) continue;
            for ($i=0;$i<24;$i++)
            {
                if ($i<10) $i = '0'.$i;
                if (!isset($row[$i])) {
                    $_str = "";
                    $newRow['stat_val_'.$i] = $_str;
                    continue;
                } else {
                    $_str = $row[$i]['_pv']."<br/>".$row[$i]['xf_pv'];
                }
                if(count($row[$i])<2 || $row[$i]['xf_pv']==0) {
                    $_str = "";
                }
                $newRow['stat_val_'.$i] = $_str;
            }
            $dataRes[] = $newRow;
        }
        return $dataRes;
    }


    /**
     * @param $date
     * @param $perPage
     * @param $page
     * @return array
     */
    public static function getAdsStatisticsRes($date, $perPage, $page)
    {
        $res = TaskStatisticsRepository::getAdsPageList($date, $page, $perPage);
        return $res;
    }

    public static function getAdsStatisticsTotal($date)
    {
        $total = TaskStatisticsRepository::CountAds($date);
        return $total;
    }


    public static function getSearchCount($date)
    {
        return TaskStatisticsRepository::countSearch($date);
    }

    public static function getSearchPageList($date, $perPage, $page)
    {
        return TaskStatisticsRepository::getSearchPageList($date, $page, $perPage);
    }

}