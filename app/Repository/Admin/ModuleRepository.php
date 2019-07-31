<?php
namespace App\Repository\Admin;

use App\Repository\Searchable;
use App\Model\Admin\Module;
use Illuminate\Database\QueryException;

class ModuleRepository
{
    use Searchable;


    public static function list($perPage, $condition)
    {
        $data = Module::query()->select('*')
        ->where(function ($query) use($condition) {
            Searchable::buildQuery($query, $condition);
        })->orderBy('id', 'desc')
            ->paginate($perPage);

        $data->transform(function ($item) {
            $item->update_time = in_array($item->update_time, [ '0000-01-01 00:00:00', '0000-00-00 00:00:00' ]) ?
                '': $item->update_time;
            $item->enableText = $item->enable? '启用': '未启用';
            return $item;
        });

        return [
            'code' => 0,
            'msg'  => '',
            'data' => $data->items(),
            'count'=> $data->total()
        ];
    }

    /**
     * 获取模块ID对应的map数据
     * @param $ids
     * @param $enable
     */
    public static function getModuleNameMap($ids = [], $enable = '')
    {
        $query = Module::query();
        $ids && $query->whereIn('id', $ids);
        $enable!=='' && $query->where('enable', $enable);
        return $query->pluck('module_name', 'id')
        ->toArray();
    }


    public static function find($id)
    {
        return Module::query()->find($id);
    }

    public static function getMaxId()
    {
        return Module::query()->orderBy('id', 'desc')
            ->select([ 'id' ] )
        ->limit(1)->first();
    }

    public static function enableStatus($id, $status)
    {
        $updateData = [
            'enable' => $status
        ];
        return Module::query()->find($id)
            ->update($updateData);
    }

    public static function delete($ids)
    {
        try {
            Module::query()->whereIn('id', $ids)
                ->delete();
            return [
                'code' => 0,
                'msg'  => '删除模块成功!'
            ];
        } catch (QueryException $ex) {
            return [
                'code' => 1,
                'msg'  => '删除模块失败, 失败原因:'. $ex->getMessage(),
            ];
        }
    }

    public static function addModule($postData)
    {
        try {
            $postData['update_time'] = $postData['create_time'] = date('Y-m-d H:i:s');
            Module::query()->create($postData);
            return [
                'code' => 0,
                'msg'  => '新增模块成功'
            ];
        } catch (QueryException $ex) {
            return [
                'code' => 1,
                'msg'  => '新增模块失败,失败原因:'. $ex->getMessage(),
            ];
        }
    }

    public static function updateModule($id, $postData)
    {
        try {
            $postData['update_time'] = date('Y-m-d H:i:s');
            Module::query()->find($id)->update($postData);
            return [
                'code' => 0,
                'msg'  => '更新模块成功'
            ];
        } catch (QueryException $ex) {
            return [
                'code' => 1,
                'msg'  => '更新模块失败,失败原因:'. $ex->getMessage(),
            ];
        }
    }

}