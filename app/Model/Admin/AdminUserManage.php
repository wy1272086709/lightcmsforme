<?php
namespace App\Model\Admin;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class AdminUserManage extends Authenticatable
{
    use HasRoles;
    protected $table = 'tb_admin';
    public $timestamps = false;
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    protected $guarded = [];
    protected $primaryKey = 'aid';
    protected $guard_name = 'admin';

}