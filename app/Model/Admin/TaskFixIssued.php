<?php
namespace App\Model\Admin;
class TaskFixIssued extends Model
{
    public $table = 'tb_task_fix_issued';
    public $primaryKey = 'id';
    public $guarded = [];
    public $timestamps = false;
}