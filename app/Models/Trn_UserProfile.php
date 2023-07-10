<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_UserProfile extends Model
{
    use HasFactory;
    protected $table ='trn_user_profiles';
    
    public function gender()
    {
      return $this->belongsTo(Sys_Gender::class,'gender_id','id');
    }
}
