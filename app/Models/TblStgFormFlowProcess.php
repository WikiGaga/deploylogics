<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblStgFormFlowProcess extends Model
{
    protected $table = 'tbl_stg_form_flow_process';

    public function actions_btn_btl() {
        return $this->hasOne(TblStgActions::class,'stg_actions_id','process_id');
    }
    public function users_btl() {
        return $this->hasOne(User::class,'id','process_id');
    }
}
