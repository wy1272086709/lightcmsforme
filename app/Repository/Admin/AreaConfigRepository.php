<?php
namespace App\Repository\Admin;
use App\Model\Admin\AreaConfig;

class AreaConfigRepository
{
    public static function saveAreaConfig($configData)
    {
        if (!empty($configData['config_id'])) {
            $id = $configData['config_id'];
            if (isset($configData['config_id']))
            {
                unset($configData['config_id']);
            }
            AreaConfig::query()->where('id', $id)
                ->update($configData);
            return $id;
        }
        if (isset($configData['config_id']))
        {
            unset($configData['config_id']);
        }
        // hao_pz 值是动态计算出来的.
        $configData['hao_pz'] = '0';
        return AreaConfig::query()->insertGetId($configData);
    }

    /*
     * 获取区域配置
     */
    public static function getOneAreaConfig($id)
    {
        $q = AreaConfig::query()->where('id', $id);
        $obj = $q->first();
        if ($obj) {
            return $obj->toArray();
        } else {
            return [];
        }
    }

}