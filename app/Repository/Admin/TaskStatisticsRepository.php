<?php
namespace App\Repository\Admin;


use Illuminate\Support\Facades\DB;

class TaskStatisticsRepository
{
    /**
     * @param $date
     * @param $perPage
     * @param int $page
     * @return mixed
     */
    public static function ippvRes($date, $perPage, $page = 1)
    {
        $offset = ((int) $page -1) * $perPage;
        $sql = "SELECT t.*, d.IP as succ_ip, d.pv as succ_pv FROM tb_task_ippv t join day_return_log d on t.taskID=d.taskID 
WHERE t.date = d.dates and t.date='$date' GROUP BY t.taskID order by t.taskID asc LIMIT $offset, $perPage";
        // echo $sql;
        $res =  DB::select($sql);
        return $res;
    }

    /**
     * @param $date
     * @return mixed
     */
    public static function ippvCount($date)
    {
        $sql = "SELECT count(t.id) AS cnt FROM tb_task_ippv t join day_return_log d on t.taskID=d.taskID 
WHERE t.date = d.dates and t.date='$date'";
        $res = DB::select($sql);
        return $res;
    }

    /**
     * @param $taskId
     * @return integer
     */
    public static function countHourIppv($taskId)
    {
        $where = "taskID = $taskId";
        $sql = "SELECT count(left(`date`,8))  as cnt FROM tb_task_hour_ippv WHERE $where ";
        $res = DB::select($sql);
        $obj = current($res);
        return $obj->cnt;
    }

    public static function getHourIppvRes($taskId, $page, $perPage)
    {
        $where = "taskID = $taskId";
        $offset = ((int)$page - 1) * $perPage;
        $sql = "SELECT left(`date`, 8) as dates FROM tb_task_hour_ippv WHERE $where group by left(`date`,8) order by left(`date`,8) desc 
limit $offset, $perPage";
        $datesRes = DB::select($sql);
        $dates = [];
        foreach ($datesRes as $row)
        {
            $newRow = (array)$row;
            isset($newRow['dates']) && $dates[] = $newRow['dates'];
        }
        if (!$dates)
        {
            return [];
        }
        $dateSql = "SELECT * FROM tb_task_hour_ippv WHERE left(`date`,8) in('".implode("','", $dates)."')";
        // echo $dateSql;
        $res = DB::select($dateSql);
        return $res;
    }


    public static function CountAds($searchTime)
    {
        $where = "p.plan_time BETWEEN '%s' AND '%s' AND t.service_type = 1";
        $where = sprintf($where, $searchTime, date('Y-m-d', strtotime($searchTime.' +1 days')));
        $sql = "SELECT t.id, count(t.id) as cnt FROM tb_area_task t join tb_plan p on t.id=p.task_id 
        WHERE $where group by t.id";
        $obj = DB::select($sql);
        if (!$obj)
        {
            return 0;
        }
        $total = 0;
        foreach ($obj as $row) {
            $row = (array)$row;
            $total+= $row['cnt'];
        }
        return $total;
    }

    /**
     *
     * @param $searchTime
     * @param $page
     * @param $perPage
     * @return array
     */
    public static function getAdsPageList($searchTime, $page, $perPage)
    {
        $offset = ((int)$page -1) * (int)$perPage;
        $where = "p.plan_time BETWEEN '%s' AND '%s' AND t.service_type =1";
        $where = sprintf($where, $searchTime, date('Y-m-d', strtotime($searchTime.' +1 days')));
        $sql = "SELECT t.id, t.task_name, sum(p.plan_count) as plan_count, 
sum(p.exec_count) as exec_count, sum(p.succ_count) as succ_count, sum(p.search_scnt) as search_scnt, 
sum(p.bs_scnt) as bs_scnt, sum(p.bs_scnt2) as bs_scnt2, sum(p.bs_ccnt) as bs_ccnt FROM tb_area_task t join tb_plan p on 
t.id=p.task_id WHERE $where group by t.id LIMIT $offset, $perPage";
        $objRes = DB::select($sql);
        if (!$objRes)
        {
            return [];
        }
        foreach ($objRes as $k =>$row)
        {
            $objRes[$k] = (array) $row;
        }
        return $objRes;
    }



    public static function countSearch($date)
    {
        $where = "p.plan_time BETWEEN '%s' AND '%s' AND t.service_type = 2";
        $where = sprintf($where, $date, date('Y-m-d', strtotime($date.' +1 days')));
        $sql = "SELECT t.id as id, count(t.id) as cnt FROM tb_area_task t join tb_plan p on t.id=p.task_id WHERE $where group by t.id";
        $objRes = DB::select($sql);
        if (!$objRes)
        {
            return [];
        }
        $total = 0;
        foreach ($objRes as $k =>$row)
        {
            $row = (array) $row;
            $total+=(int)$row['cnt'];
        }
        return $total;
    }


    public static function getSearchPageList($date, $page, $perPage)
    {
        $offset = ((int)$page -1) * (int)$perPage;
        $where = "p.plan_time BETWEEN '%s' AND '%s' AND t.service_type = 2";
        $where = sprintf($where, $date, date('Y-m-d', strtotime($date.' +1 days')));
        $sql = "SELECT t.id,  t.task_name,  sum(p.plan_count) as plan_count, sum(p.exec_count) as exec_count, sum(p.succ_count) as succ_count, sum(p.search_scnt) as search_scnt, 
sum(p.bs_scnt) as bs_scnt, sum(p.bs_scnt2) as bs_scnt2, sum(p.bs_ccnt) as bs_ccnt FROM tb_area_task t join tb_plan p on t.id=p.task_id WHERE $where 
group by t.id limit $offset, $perPage";
        $objRes = DB::select($sql);
        if (!$objRes)
        {
            return [];
        }
        $res = [];
        foreach ($objRes as $k =>$row)
        {
            $row = (array) $row;
            $res[] = $row;
        }
        return $res;
    }
}