<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Booking_Therapy_Refund extends Model
{
    use HasFactory;
    
    public function booking()
    {
        return $this->belongsTo(Trn_Consultation_Booking::class, 'booking_id', 'id');
    }
     public function patient()
    {
        return $this->belongsTo(Mst_Patient::class, 'patient_id', 'id');
    }
}
