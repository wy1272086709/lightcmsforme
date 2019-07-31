<?php
/**
 * @author  Eddy <cumtsjh@163.com>
 */

namespace App\Repository\Admin;

use App\Model\Admin\Category;
use App\Repository\Searchable;

class CategoryRepository
{
    use Searchable;

    public static function list($perPage, $condition = [])
    {
        $data = Category::query()
            ->where(function ($query) use ($condition) {
                Searchable::buildQuery($query, $condition);
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);
        $data->transform(function ($item) {
            xssFilter($item);
            $item->editUrl = route('admin::category.edit', ['id' => $item->id]);
            $item->parentName = $item->pid == 0 ? '顶级菜单' : $item->parent->name;
            return $item;
        });

        return [
            'code' => 0,
            'msg' => '',
            'count' => $data->total(),
            'data' => $data->items(),
        ];
    }

    public static function add($data)
    {
        return Category::query()->create($data);
    }

    public static function update($id, $data)
    {
        return Category::query()->where('id', $id)->update($data);
    }

    public static function find($id)
    {
        return Category::query()->find($id);
    }

    public static function tree($entity_id = null, $pid = 0, $all = null, $level = 0, $path = [])
    {
        if (is_null($all)) {
            if (is_null($entity_id)) {
                $all = Category::select('id', 'pid', 'name', 'order')->get();
            } else {
                $all = Category::select('id', 'pid', 'name', 'order')->where('model_id', $entity_id)->get();
            }
        }
        return $all->where('pid', $pid)
            ->map(function (Category $model) use ($all, $level, $path, $entity_id) {
                $data = [
                    'id' => $model->id,
                    'name' => $model->name,
                    'level' => $level,
                    'pid' => $model->pid,
                    'path' => $path,
                    'order' => $model->order,
                ];

                $child = $all->where('pid', $model->id);
                if ($child->isEmpty()) {
                    return $data;
                }

                array_push($path, $model->id);
                $data['children'] = self::tree($entity_id, $model->id, $all, $level + 1, $path);
                return $data;
            })->sortBy('order');
    }
}
