<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftCompany extends Model
{
    protected $table = 'tbl_soft_company';
    protected $primaryKey = 'company_id';

    public function user(){
        return $this->belongsToMany(User::class);
    }
}
