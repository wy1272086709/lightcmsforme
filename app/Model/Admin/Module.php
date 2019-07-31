<?php
namespace App\Model\Admin;

class Module extends \App\Model\Admin\Model
{

    public $table = 'tb_area_module';
    public $timestamps = false;

    public $guarded = [];

    // 创建时间,更新时间字段
    // const CREATED_AT = 'create_time';
    // const UPDATED_AT = 'update_time';

    public $dateFormat = 'Y-m-d H:i:s';
}