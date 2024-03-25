<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trn_Booking_Wellness_Detail extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'trn__booking__wellness__details';
    protected $fillable = [
        'booking_id',
        'wellness_id',
        'wellness_fee',
        'booking_timeslot',
        'created_at',
        'updated_at',
    ];
    
    public function booking()
    {
        return $this->belongsTo(Trn_Consultation_Booking::class, 'booking_id', 'id');
    }
     public function wellness()
    {
        return $this->belongsTo(Mst_Wellness::class, 'wellness_id', 'wellness_id');
    }
}
