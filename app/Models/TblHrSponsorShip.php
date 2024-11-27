<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrSponsorShip extends Model
{
    protected $table = 'tbl_payr_sponsorship';
    protected $primaryKey = 'sponsorship_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
