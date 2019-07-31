<?php
namespace App\Model\Admin;
use App\Model\Admin\Model;

class CustomSource extends Model
{
    public $table = 'tb_custom_source';
    public $primaryKey = 'id';

    public $guarded = [];

    public $timestamps = false;
}