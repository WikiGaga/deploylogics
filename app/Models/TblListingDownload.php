<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblListingDownload extends Model
{
    protected $table = 'tbl_listing_downloads';

    public function user()
{
    return $this->belongsTo(User::class, 'id', 'id');
}

}
