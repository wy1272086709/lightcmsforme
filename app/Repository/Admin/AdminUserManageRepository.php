<?php
namespace App\Repository\Admin;
use App\Model\Admin\AdminUser;
use App\Repository\Searchable;
use App\Model\Admin\AdminUserManage;
use App\Model\Admin\Menu;
class AdminUserManageRepository
{
    use Searchable;

    public static function list($perPage, $condition = [])
    {
        $data = AdminUserManage::query()
            ->select('*')
            ->where(function ($query) use ($condition) {
                Searchable::buildQuery($query, $condition);
            })
            ->where('typeid', '!=', 1)
            ->orderBy('aid', 'desc')
            ->paginate($perPage);
        $data->transform(function ($item) {
            xssFilter($item);
            $item->editUrl = route('admin::userManage.edit', ['id' => $item->aid]);
            $item->statusText = $item->enable == AdminUserManage::STATUS_ENABLE ?
                '<span class="layui-badge layui-bg-green">启用</span>' :
                '<span class="layui-badge">禁用</span>';
            $item->typename = $item->typeid == 1? '总管理员': '普通管理员';
            $item->loginip = trim($item->loginip) == '0.0.0.0' ? '': $item->loginip;
            return $item;
        });

        return [
            'code' => 0,
            'msg' => '',
            'count' => $data->total(),
            'data' => $data->items(),
        ];
    }

    public static function find($id)
    {
        return AdminUserManage::query()->find($id);
    }

    public static function add($data)
    {
        $data['pwd'] = bcrypt($data['pwd']);
        $data['pubdate'] = date('Y-m-d H:i:s');
        return AdminUserManage::query()->create($data);
    }

    public static function update($id, $data)
    {
        if (!empty($data['pwd'])) {
            $data['pwd'] = bcrypt($data['pwd']);
        }
        return AdminUserManage::query()->where('aid', $id)->update($data);
    }

    public static function setDefaultPermission(AdminUserManage $user)
    {
        $logoutPermission = Menu::query()->where('route', 'admin::logout')->first();
        if ($logoutPermission) {
            $user->givePermissionTo($logoutPermission->name);
        }
    }

    public static function delete($ids)
    {
        AdminUserManage::query()->whereIn('aid', $ids)->delete();
        return true;
    }

}