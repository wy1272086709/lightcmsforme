<?php
namespace App\Model\Admin;
class TaskGroup extends Model
{
    public $table = 'tb_task_group';
    public $primaryKey = 'id';

    public $guarded = [];
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
}