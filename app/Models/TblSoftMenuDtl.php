<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftMenuDtl extends Model
{
    protected $table = 'tbl_soft_menu_dtl';
    protected $primaryKey = 'menu_dtl_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function menu(){
        return $this->belongsTo(TblSoftMenu::class,'menu_id');
    }

    public function children()
    {
        return $this->hasMany(TblSoftMenuDtl::class, 'parent_menu_id')->orderBy('menu_dtl_name');
    }

    public function permissions(){
        return $this->hasMany(Permission::class,'menu_dtl_id');
    }
}
