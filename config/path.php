<?php
//原文件保存目录
$yuan_file_path = str_replace('entry','data', dirname(__DIR__, 2)).'/y_nsm_xml/';
$file_path = str_replace('entry','data', dirname(__DIR__, 2)).'/nsm_xml/';        //加密文件保存目录
$_root_dir = str_replace('entry', '',     dirname(__DIR__, 2));
$cache_path = str_replace('entry','cache', dirname(__DIR__, 2)).'/_nsm/';   //Cache文件保存目录
$_data_dir = str_replace('entry', 'data',     dirname(__DIR__, 2));
$excel_file_path = str_replace('entry','data', dirname(__DIR__, 2)).'/xlsx_config/';

//    接口配置
$cfg_api = [
    'channel' => 'http://simulation.faafox.com/api/cuid_pcnum.php', // 渠道接口
    'aes_key' => 'HR3V3T3nTsyR8A19',
];

$path = [
    'yuan_file_path' => $yuan_file_path,
    'file_path'      => $file_path,
    'root_path'      => $_root_dir,
    'cache_path'     => $cache_path,
    'data_dir'       => $_data_dir,
    'excel_dir'      => $excel_file_path
];

return array_merge($path, [
    'cfg_api' => $cfg_api
]);