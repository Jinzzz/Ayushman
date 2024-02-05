<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Feedback extends Model
{
    use HasFactory;
    protected $table ='trn__feedback';
    protected $fillable = [
        'patient_id',
        'consultancy_rating',
        'visit_rating',
        'service_rating',
        'pharmacy_rating',
        'appointment_rating',
        'average_rating',
        'feedback',
        'is_active',
        'created_at',
        'updated_at',
    ];


    public function patient()
    {
        return $this->belongsTo(Mst_Patient::class, 'patient_id','id');
    }

}
