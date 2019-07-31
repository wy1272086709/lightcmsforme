<?php
namespace App\Model\Admin;
class TaskUpload extends Model {
    public $table = 'tb_task_upload';
    public $primaryKey = 'id';
    public $guarded = [];

    // 关闭时间功能
    public $timestamps = false;
}