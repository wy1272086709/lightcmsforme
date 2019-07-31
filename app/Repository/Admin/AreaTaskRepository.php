<?php
namespace App\Repository\Admin;

use App\Model\Admin\AreaTask;
use App\Repository\Searchable;
use App\Utils\ConstantUtils;
use Illuminate\Support\Facades\DB;

class AreaTaskRepository
{
    use Searchable;
    /**
     * 根据$taskId 获取$taskName
     * @param $taskId
     * @return array
     */
    public static function getTaskName($taskId)
    {
        $res = AreaTask::query()->whereIn('id', $taskId)
        ->select([ 'task_name', 'id' ])
        ->get();
        $taskNameMap = [];
        foreach ($res as $k => $item) {
            $taskNameMap[$item['id']] = $item['task_name'];
        }
        return $taskNameMap;
    }

    /**
     * 将指定任务的ID停止掉
     * @param $taskIds array
     * @return boolean
     */
    public static function stopTask($taskIds)
    {
        return AreaTask::query()->whereIn('id', $taskIds)
            ->update(['enable' => 0, 'stop_time' => date('Y-m-d H:i:s')]);
    }

    public static function delTask($taskIds)
    {
        return AreaTask::query()->whereIn('id', $taskIds)
            ->delete();
    }



    public static function getEnableTaskIds()
    {
        $sql = "select id from tb_task_new where enable = 1";
        $res = DB::select($sql);
        $ids = $res ? array_column($res, 'id'): [];
        return $ids;
    }

    /**
     * 获取任务数组
     * @param $id
     * @param $status
     * @return array
     */
    public static function getTaskRow($id, $status = 0) {
        $q = AreaTask::query()->where('id', $id);
        $status && $q->where('enable', $status);
        $res = $q->first();
        return $res ? $res->toArray(): [];
    }

    /**
     * 获取任务列表
     * @param $data
     * @param $taskGroupMap | array
     * @param $issuedValueMap | array
     * @return array
     */
    public static function listTask($data, $taskGroupMap, $issuedValueMap) {
        // 获取分组名称
        $data->transform(function ($item) use($taskGroupMap, $issuedValueMap){
            $item->status_text = $item->enable == 1? '启用': '未启用';
            $item->task_group_name = isset($taskGroupMap[$item->task_group_id]) ? $taskGroupMap[$item->task_group_id]: '';
            // $item->create_time = $item->create_time == '0000-01-01 00:00:00' ? '': $item->create_time;
            // $item->update_time = $item->update_time == '0000-01-01 00:00:00' ? '': $item->update_time;
            // $item->stop_time = $item->stop_time == '0000-01-01 00:00:00' ? '': $item->stop_time;
            $item->issue_value = isset($issuedValueMap[$item->id]) ? $issuedValueMap[$item->id]: '';
            return $item;
        });
        return [
            'data'  => $data->items(),
            'count' => $data->total(),
            'msg'   => '',
            'code'  => 0
        ];
    }


    /**
     * @param $perPage
     * @param $where
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getTaskPageData($perPage, $where) {
        $q = AreaTask::query()->where(function($query) use($where){
            Searchable::buildQuery($query, $where);
        })->orderBy('id', 'desc');
        $data = $q->paginate($perPage);
        return $data;
    }


    public static function saveTask($data, $id)
    {
        if ($id) {
            $data['update_time'] = date('Y-m-d H:i:s');
            self::editTask($data, $id);
            return $id;
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            return self::addTask($data);
        }
    }

    public static function addTask($data)
    {

        return AreaTask::query()->insertGetId($data);
    }


    public static function editTask($data, $id)
    {
        return AreaTask::query()->find($id)
            ->update($data);
    }

    public static function getConfigId($taskId)
    {
        return AreaTask::query()->where('id', $taskId)
            ->value('config_id');
    }

    /**
     * 判断分组对应的任务是否存在
     * @param $groupIds
     * @return bool
     */
    public static function isExistsTask($groupIds)
    {
        return AreaTask::query()->whereIn('task_group_id', $groupIds)
            ->exists();
    }

    /**
     * 获取任务名称和任务ID的键值对
     * @param $taskIds
     * @param $fields
     * @return array
     */
    public static function getTaskRowMap($taskIds, $fields = ['task_name', 'time_zone', 'aq_type', 'time_edTime', 'level', 'id'])
    {
        $q = AreaTask::query()->whereIn('id', $taskIds)
            ->select($fields);
        $taskNameMap = $q->get()
            ->toArray();
        return $taskNameMap ? array_column($taskNameMap, null, 'id') : [];
    }

}