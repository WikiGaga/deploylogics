<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'modules';

    public function permissions(){
        return $this->hasMany(Permission::class,'module_id');
    }
    public function children_with_permission()
    {
        return $this->hasMany($this, 'parent_id')->with('permissions')->orderBy('sort_order');
    }
}
