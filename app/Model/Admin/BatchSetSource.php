<?php
namespace App\Model\Admin;
class BatchSetSource extends Model
{
    public $table = 'tb_batch_set_source';
    public $primaryKey = 'id';

    public $guarded = [];
    public $timestamps = false;
}