<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblNotificationMessage extends Model
{
    protected $table = 'notification_messages';
    protected $primaryKey = 'id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
