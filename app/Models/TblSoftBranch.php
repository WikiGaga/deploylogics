<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftBranch extends Model
{
    protected $table = 'tbl_soft_branch';
    protected $primaryKey = 'branch_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function branch(){
        return $this->hasMany(TblPurcDemand::class,'branch_id');
    }

    public function branch_coa()
    {
        return $this->belongsTo(TblAccCoa::class, 'branch_account_code','chart_account_id');
    }

}
