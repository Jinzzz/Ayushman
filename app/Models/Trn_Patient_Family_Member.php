<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Patient_Family_Member extends Model
{
    use HasFactory;
    protected $table = 'trn_patient_family_member';

    protected $fillable = [
        'patient_id',
        'family_member_name',
        'mobile_number',
        'mobile_number_new',
        'email_address',
        'gender_id',
        'blood_group_id',
        'date_of_birth',
        'relationship_id',
        'address',
        'created_by',
        'is_active',
        'verified',
    ];
    
      public function gender()
    {
      return $this->belongsTo(Mst_Master_Value::class,'gender_id','id');
    }


    public function bloodGroup()
    {
      return $this->belongsTo(Mst_Master_Value::class,'blood_group_id','id');
    }
      public function relationship()
    {
      return $this->belongsTo(Mst_Master_Value::class,'relationship_id','id');
    }
}
