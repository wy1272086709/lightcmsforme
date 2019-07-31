<?php
namespace App\Model\Admin;

class CustomUa extends Model
{
    public $table = 'tb_custom_ua';
    public $primaryKey = 'id';
    public $guarded = [];
    public $timestamps = false;
}