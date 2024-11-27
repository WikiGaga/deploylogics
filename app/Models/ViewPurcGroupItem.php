<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcGroupItem extends Model
{
    protected $table = 'vw_purc_group_item';
    protected $primaryKey = 'group_item_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function children(){
        return $this->hasMany(ViewPurcGroupItem::class, 'group_parent_item_id', 'id')->with('children')
            ->select(['group_item_id as id','group_item_name_code_string as group_item_code','group_item_name','group_parent_item_id','group_item_level'])->orderBy('group_item_name_code_string');
    }
}
