<?php
namespace App\Model\Admin;
class TaskPlan extends Model
{
    public $table = 'tb_task_plan';
    public $primaryKey = 'id';
    public $guarded = [];
    public $timestamps = false;
}