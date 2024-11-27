<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $table = 'users';
    protected $primaryKey = 'id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    use LaratrustUserTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function business(){
        return $this->belongsTo(TblSoftBusiness::class,'business_id');
    }
    public function branch(){
        return $this->belongsTo(TblSoftBranch::class,'branch_id');
    }
    public function company(){
        return $this->belongsToMany(TblSoftCompany::class);
    }
    public function users_type_acco(){
        return $this->belongsTo(UsersTypeAcco::class,'id','user_id')
            ->with('customer');
    }

    public function userbranch(){
        return $this->belongsToMany(TblSoftBranch::class,'tbl_soft_user_branch','user_id','branch_id')
                 ->withPivot('default_branch');
    }

    public function defaultbranch(){
        return $this->belongsToMany(TblSoftBranch::class,'tbl_soft_user_branch','user_id','branch_id')
                ->withPivot('default_branch');
    }



    //---api code----
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
