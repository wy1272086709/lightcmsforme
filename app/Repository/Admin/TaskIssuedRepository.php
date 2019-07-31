<?php
namespace App\Repository\Admin;

use App\Model\Admin\AreaTask;
use App\Model\Admin\TaskPlan;
use App\Utils\ConstantUtils;
use Illuminate\Support\Facades\DB;

class TaskIssuedRepository
{
    /**
     * 获取发布量级
     * @param $taskIdArr     array  任务ID
     * @param $issuedStyle integer  发布数值的具体方式.0固定, 1计划模式
     * @return array
     */
    public static function getIssuedValue($taskIdArr, $issuedStyle = ConstantUtils::FIXED_ISSUE_MODE)
    {
        $issuedValueMap = [];
        // 固定模式, 查询tb_task_fix_issued 表
        if ($issuedStyle == ConstantUtils::FIXED_ISSUE_MODE)
        {
            $issuedValueArr = TaskPlan::query()->whereIn('task_id', $taskIdArr)
                ->select([ 'max_issued', 'task_id' ])
            ->get()
            ->toArray();
            foreach ($issuedValueArr as $row)
            {
                $issuedValueMap[$row['task_id']] = $row['max_issued'];
            }
        }
        else if ($issuedStyle == ConstantUtils::PLAN_ISSUE_MODE)
        {
            $today = date('Y-m-d');
            $issuedValueArr = TaskPlan::query()->whereIn('task_id', $taskIdArr)
            ->where('issued_date', $today)
                ->select([ 'max_issued', 'task_id' ])
            ->get()
            ->toArray();
            foreach ($issuedValueArr as $row)
            {
                $issuedValueMap[$row['task_id']] = $row['max_issued'];
            }
        }
        return $issuedValueMap;
    }


    /**
     * 保存对应的发布数值到数据表中
     * @param $taskId      integer 任务ID
     * @param $issueStyle  integer 发布数值方式
     * @param $issueData   array|string 发布数值的数据
     * @return boolean
     */
    public static function insertIssuedValue($taskId, $issueStyle, $issueData)
    {
        if ($issueStyle == ConstantUtils::FIXED_ISSUE_MODE) {
            $timeStr = '["00:00:00=>24:00:00"]';
            return TaskPlan::query()->insertGetId([
                'task_id'      => $taskId,
                'start_issued' => $issueData,
                'max_issued'   => $issueData,
                'issued_date'  => date('Y-m-d'),
                'issued_time'  => $timeStr,
                'change_type'  => 0
            ]);
        } else {
            $newIssuedData = [];
            foreach ($issueData as $k => $row) {
                $startTime = $row['start_issued_date'];
                $endTime   = $row['end_issued_date'];
                $startDate = $startTime;
                $endDate   = $endTime;
                for ($m = strtotime($startDate);$m<=strtotime($endDate);$m+=24*3600)
                {
                    $timeStr = '["00:00:00=>24:00:00"]';
                    $newRow = ['task_id' => $taskId];
                    $newRow['start_issued'] = $row['schedule_number'];
                    $newRow['max_issued'] = $row['schedule_number'];
                    $newRow['issued_date'] = date('Y-m-d', $m);
                    $newRow['issued_time'] = $timeStr;
                    $newRow['change_type'] = 1;
                    $newIssuedData[] = $newRow;
                }
            }
            return TaskPlan::query()->insert($newIssuedData);
        }
    }

    /**
     * 保存固定的数值到表中
     * @param $taskId
     * @param $issueValue
     * @return int
     */
    public static function saveFixedIssueValue($taskId, $issueValue)
    {
        return TaskPlan::query()->where('task_id', $taskId)
            ->update([
                'max_issued' => $issueValue
            ]);
    }


    public static function getTaskIssuedValueRes($taskId, $issueStyle)
    {
        // 固定模式
        if ($issueStyle == ConstantUtils::FIXED_ISSUE_MODE) {
            return TaskPlan::query()->where('task_id', $taskId)
                ->value('max_issued');
        } else {
            // 计划模式 [{"no":"0","start_issued_date":"2019-07-16 00:00:00","end_issued_date":"2019-07-25 00:00:00",
            // "totalDays":"10","schedule_number":"300"}]
            // 如何将格式转换为这种
            $columns = [
                'max(issued_date) as max_issued_date',
                'min(issued_date) as min_issued_date',
                'max_issued'
            ];
            $res = TaskPlan::query()->where('task_id', $taskId)
                ->select(
                    DB::raw('max(id) as max_id'),
                    DB::raw('min(id) as min_id'),
                    'max_issued')
                ->groupBy(['max_issued'])
                ->get()
                ->toArray();
            $i = 0;
            $newRes = [];
            foreach ($res as $k => $row)
            {
                $maxId = $row['max_id'];
                $minId = $row['min_id'];
                $rs = TaskPlan::query()
                    ->select(['issued_date', 'id'])
                    ->whereIn('id', [ $minId, $maxId ])
                    ->get()
                    ->toArray();
                $issueRes = array_column($rs, null, 'id');
                $startIssuedDate = $issueRes[$minId]['issued_date'];
                $endIssuedDate   = $issueRes[$maxId]['issued_date'];
                $start = $startIssuedDate;
                $end   = $endIssuedDate;
                $totalDays = (strtotime($endIssuedDate) - strtotime($startIssuedDate))/(24*3600)+1;
                $newRow = [
                  'start_issued_date' => $start,
                  'end_issued_date'   => $end,
                    'totalDays'       => $totalDays,
                    'schedule_number' => $row['max_issued'],
                    'no' => $i++
                ];
                $newRes[] = $newRow;
             }
            return $newRes?\json_encode($newRes, true): '';
        }
    }


    /**
     * @param $taskId
     * @param $issueStyle
     * @return boolean
     */
    public static function isTaskIssuedExists($taskId, $issueStyle)
    {
        return TaskPlan::query()->where('task_id', $taskId)
            ->exists();
    }

    /**
     * @param $taskId
     *
     * @return boolean
     */
    public static function delPlanTaskIssued($taskId)
    {
        return TaskPlan::query()->where('task_id', $taskId)
            ->delete();
    }

    /**
     * @param $taskId
     * @param $issueStyle
     * @param $issueData
     * @return boolean
     */
    public static function saveIssuedValueToDb($taskId, $issueStyle, $issueData)
    {
        // 如果存在
        if (self::isTaskIssuedExists($taskId, $issueStyle)) {
            // 先删除，后进行插入操作
            self::delPlanTaskIssued($taskId);
            return self::insertIssuedValue($taskId, $issueStyle, $issueData);
        } else {
            return self::insertIssuedValue($taskId, $issueStyle, $issueData);
        }
    }

    public static function updateTaskIssueFields($taskId){
        $sql = "select * from tb_task_plan where task_id=? and status=1 and length(issued_time)>10";
        $data = DB::select($sql, [ $taskId ]);
        if( $data ){
            $data = (array)current($data);
            $issued_time = \json_decode($data['issued_time'], true);
            $timeZone = [];
            foreach( $issued_time as $Zone ){
                $timeZone[] = $data['issued_date'].' '.explode('=>',$Zone)[0] .'=>'. $data['issued_date'].' '.explode('=>',$Zone)[1];
            }
            $updateData = [
                'time_issued' => \json_encode($timeZone),
                'max_issued'  => $data['max_issued']
            ];
            AreaTask::query()->where('id', $taskId)
            ->update($updateData);
        }else{
            AreaTask::query()->where('id', $taskId)
                ->update([
                   'max_issued' => 0,
                   'time_issued'=> ''
                ]);
        }
    }

}
