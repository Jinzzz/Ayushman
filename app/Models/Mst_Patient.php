<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mst_Patient extends Model
{
    use HasFactory;
    protected $table = 'mst_patients';

    protected $fillable = [
        'patient_code',
        'patient_name',
        'patient_email',
        'patient_mobile',
        'patient_address',
        'patient_gender',
        'patient_dob',
        'username',
        'password',
        'is_active',

    ];

    public function gender()
    {
      return $this->belongsTo(Sys_Gender::class,'patient_gender','id');
    }
}
