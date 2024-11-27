<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersTypeAcco extends Model
{
    protected $table = 'users_type_acco';
    protected $primaryKey = 'id';

    protected $fillable = [
          'id',
          'user_id',
          'user_type',
          'document_id'
    ];

    protected $guarded = [
        'created_at',
        'updated_at'
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function customer(){
        return $this->belongsTo(TblSaleCustomer::class,'document_id','customer_id');
    }
}
