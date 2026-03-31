<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    public $guarded = [];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function branches()
    {
        return $this->belongsToMany(TblSoftBranch::class, 'tbl_soft_role_branch', 'role_id', 'branch_id');
    }

  /*  public static $rules = [
        'name' => 'sometimes|required|name|unique:roles',
    ];*/
}
