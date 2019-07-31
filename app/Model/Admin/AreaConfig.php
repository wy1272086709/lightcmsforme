<?php


namespace App\Model\Admin;


class AreaConfig extends Model
{
    public $table = 'tb_area_config';

    public $primaryKey = 'id';

    public $guarded = [];

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
}