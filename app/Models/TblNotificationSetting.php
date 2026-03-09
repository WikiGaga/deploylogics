<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblNotificationSetting extends Model
{
    protected $table = 'notification_settings';
    protected $primaryKey = 'id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
