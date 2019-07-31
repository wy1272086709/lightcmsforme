<?php
namespace App\Repository\Admin;
use Illuminate\Support\Facades\DB;

class DayAreaNsmRepository
{
    /**
     * 获取对应的地区数据
     * @param $dates
     * @param $key
     * @return array
     */
    public static function getDayAreaNsmData($dates, $key)
    {
        $sqlTmp = $key ? "AND ((city like :key) OR (province like :key2))": '';
        $sql = "SELECT province_id, city_id, num_ip from day_area_nsm where dates =:dates ". $sqlTmp;
        $params = $key ? [ 'dates' => $dates, 'key' => '%'.$key. '%', 'key2' => '%'.$key.'%' ]: [ 'dates' => $dates ];
        $res = DB::select($sql, $params);
        return $res;
    }



}