<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcChangeRateBranches extends Model
{
    protected $table = 'tbl_purc_change_rate_branches';
    protected $primaryKey = 'change_rate_branch_id';
    protected $fillable = [
        'change_rate_branch_id',
        'change_rate_id',
        'branch_id',
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
