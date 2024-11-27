<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcGroupItem extends Model
{
    protected $table = 'tbl_purc_group_item';
    protected $primaryKey = 'group_item_id';

    protected $fillable = [
        'group_item_id',
        'group_item_name',
        'group_item_mother_language_name',
        'group_item_code',
        'parent_group_id',
        'group_item_ref_no',
        'product_type_group_id',
        'group_item_level',
        'group_item_sales_status',
        'group_item_brand_validation',
        'group_item_expiry',
        'group_item_stock_type',
        'group_item_number',
        'parent_group_item_number',
        'group_item_entry_date_time',
        'group_item_entry_status',
        'group_item_user_id',
        'business_id',
        'company_id',
        'branch_id',
        'updated_at',
        'created_at',
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function parent(){
        return $this->hasOne(ViewPurcGroupItem::class,'group_item_id');
    }

    public function purcProduct() {
        return $this->hasMany(TblPurcProduct::class, 'group_item_id');
    }
    public function childs() {
        return $this->hasMany(static::class,'parent_group_id','group_item_id') ;
    }

    public function last_level() {
        return $this->hasMany(static::class,'parent_group_id','group_item_id')
            ->select('group_item_id','group_item_name','parent_group_id');
    }

    public function children(){
        return $this->hasMany(self::class, 'parent_group_id', 'group_item_id')->with('children')
            ->select(['group_item_id as id','group_item_code','group_item_name','parent_group_id'])->orderBy('group_item_code');
    }
}
