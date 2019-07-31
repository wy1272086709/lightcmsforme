<?php
namespace App\Repository\Admin;
use App\Model\Admin\TaskGroup;
use App\Repository\Searchable;
use Illuminate\Database\QueryException;

class TaskGroupRepository
{
    use Searchable;
    static $serviceNameMap = [
        1 => '广告业务',
        2 => '搜索业务',
        3 => '流量业务',
        4 => 'hao123业务'
    ];

    static $platFormMap = [
        0 => 'PC端',
        1 => '移动端'
    ];

    static $eqFields = [
        'service_type',
        'platform',
        'enable'
    ];
    static $likeFields = [ 'group_name', 'description' ];
    public static function list($perPage, $where)
    {
        $data = TaskGroup::query()->where(function($query) use($where){
            Searchable::buildQuery($query, $where);
        })->orderBy('id', 'desc')
        ->paginate($perPage);
        $moduleIds = [];
        $data->transform(function ($item) use(&$moduleIds){
            $moduleIds[$item->module_id] = 1;
            $item->enable_text = $item->enable == 1? '<span class="layui-badge layui-bg-green">启用</span>':
                '<span class="layui-badge layui-bg-green">未启用</span>';
            $item->platform = isset(self::$platFormMap[$item->platform]) ? self::$platFormMap[$item->platform]: '';
            $item->service_name = isset(self::$serviceNameMap[$item->service_type]) ? self::$serviceNameMap[$item->service_type]: '';
            return $item;
        });
        $moduleNameMap = ModuleRepository::getModuleNameMap(array_keys($moduleIds));
        $data->transform(function($item) use($moduleNameMap){
            $item->module_name = isset($moduleNameMap[$item->module_id])? $moduleNameMap[$item->module_id]: '';
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
     * 获取任务分组列表
     * @param $map
     * @return array
     */
    public static function getTaskGroupList($map)
    {
        $query = TaskGroup::query();
        foreach ($map as $k => $val) {
            if (in_array($k, self::$eqFields))
            {
                $query->where($k, '=', $val);
            }
            else if (in_array($k, self::$likeFields))
            {
                $query->where($k, 'like', $val);
            }
        }
        return $query
            ->get()->toArray();
    }


    public static function find($id)
    {
        return TaskGroup::query()->find($id);
    }

    public static function getTaskGroupByModuleIds($moduleIds)
    {
        return TaskGroup::query()->whereIn('module_id', $moduleIds)
            ->get()
            ->toArray();
    }

    public static function addTaskGroup($postData)
    {
        try {
            TaskGroup::query()->create($postData);
            return [
                'code' => 0,
                'msg'  => '新增任务分组成功'
            ];
        } catch (QueryException $ex) {
            return [
                'code' => 1,
                'msg'  => '新增任务分组失败,失败原因:'. $ex->getMessage(),
            ];
        }
    }

    public static function updateTaskGroup($id, $postData)
    {
        try {
            TaskGroup::query()->find($id)
                ->update($postData);
            return [
                'code' => 0,
                'msg'  => '更新任务分组成功'
            ];
        } catch (QueryException $ex) {
            return [
                'code' => 1,
                'msg'  => '更新任务分组失败,失败原因:'. $ex->getMessage(),
            ];
        }
    }

    public static function delTaskGroup($ids)
    {
        try {
            TaskGroup::query()->whereIn('id', $ids)
                ->delete();
            return [
                'code' => 0,
                'msg'  => '删除任务分组成功'
            ];
        } catch (\Exception $ex) {
            return [
                'code' => 1,
                'msg'  => '删除任务分组失败,失败原因:'. $ex->getMessage(),
            ];
        }
    }
}