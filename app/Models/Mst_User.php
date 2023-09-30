<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mst_User extends Authenticatable
{
    use HasFactory;
    protected $table = 'mst_users';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'password',
        'email',
        'user_type_id',
        'is_active',
        'last_login_time',
    ];

}
