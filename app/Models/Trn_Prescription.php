<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Prescription extends Model
{
    use HasFactory;
    protected $table = 'trn__prescriptions';
    protected $primaryKey = 'prescription_id';

    protected $fillable = [
        'Booking_Id',
        'doctor_id',
        'duration',
        'diagnosis',
        'advice',
    ];

    public function Staff()
    {
        return $this->belongsTo(Mst_Staff::class, 'doctor_id');
    }

    public function BookingDetails()
    {
        return $this->belongsTo(Trn_Consultation_Booking::class, 'Booking_Id');
    }

    public function PrescriptionDetails()
    {
        return $this->hasMany(Trn_Prescription_Details::class, 'priscription_id');
    }
}
