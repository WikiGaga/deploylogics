<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftMenu extends Model
{
    protected $table = 'tbl_soft_menu';
    protected $primaryKey = 'menu_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function children()
    {
        return $this->hasMany(TblSoftMenuDtl::class, 'menu_id')->with('permissions')->orderBy('menu_dtl_name');
    }

    public function submenu(){
        return $this->hasMany(TblSoftMenuDtl::class,'menu_id')
            ->with('children')
            ->where('business_id', auth()->user()->business_id)
            ->where('parent_menu_id', null)
            ->orderby('menu_dtl_sorting','asc');
    }
}
