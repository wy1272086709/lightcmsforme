<?php
namespace App\Model\Admin;
class ConfigP extends Model {
    public $table = 'tb_config_p';
    public $primaryKey = 'id';

    public $guarded = [];

    public $timestamps = false;
}