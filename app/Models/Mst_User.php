<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Mst_User extends Authenticatable
{
    use HasFactory;
    protected $table = 'mst_users';

    protected $fillable = [
        'username',
        'password',
        'user_email',
        'user_type_id',
        'branch_id',
        'is_active',
        'last_login_time',
        
    ];

    public function userType()
    {
        return $this->belongsTo(Mst_User_Type::class, 'user_type_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Mst_Branch::class, 'branch_id', 'id');
    }
    public function profile()
    {
        return $this->hasOne(Trn_UserProfile::class,'user_id','id');
    }

}
