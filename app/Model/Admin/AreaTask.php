<?php
namespace App\Model\Admin;
class AreaTask extends Model
{
    public $table = 'tb_task_new';
    public $primaryKey = 'id';
    public $guarded = [];
    const UPDATED_AT = 'update_time';
    const CREATED_AT = 'create_time';
}