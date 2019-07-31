<?php
namespace App\Model\Admin;
class TaskTimeZoneSet extends Model
{
    public $table = 'tb_task_timezone_set';

    public $primaryKey = 'id';

    public $guarded = [];

    public $timestamps = false;
}