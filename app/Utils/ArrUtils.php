<?php
namespace App\Utils;
class ArrUtils
{
    /**
     * 将对应的数组元素里面为空值的元素过滤掉
     * @param $data
     * @return array
     */
    public static function filterEmpty($data)
    {
        $newData = [];
        foreach ($data as $key => $val) {
            if ($val === '' || $val === '-1' || $val === [] || $val === false) {
                continue;
            }
            $newData[$key] = $val;
        }
        return $newData;
    }

}