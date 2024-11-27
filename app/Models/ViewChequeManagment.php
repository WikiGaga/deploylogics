<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewChequeManagment extends Model
{
    protected $table = 'vw_cheque_managment';
    protected $primaryKey = 'cheque_managment_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
