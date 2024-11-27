<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleCustomer extends Model
{
    protected $table = 'tbl_sale_customer';
    protected $primaryKey = 'customer_id';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function sub_customer()
    {
        return $this->hasMany(TblSaleSubCustomer::class, 'parent_customer_id' , "customer_id")
        ->with('customer');
    }
    
    public function contact_person()
    {
        return $this->hasMany(TblSaleCustomerDtl::class,"customer_id");
    }

    public function customer_branches()
    {
        return $this->hasMany(TblSaleCustomerBranch::class,"customer_id");
    }

    public function customer_city(){
        return $this->belongsTo(TblDefiCity::class , 'city_id');
    }
}
