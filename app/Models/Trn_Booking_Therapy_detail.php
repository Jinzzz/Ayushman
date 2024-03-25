<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Booking_Therapy_detail extends Model
{
    use HasFactory;
    protected $table = 'trn__booking__therapy_details';
    protected $fillable = [
        'booking_id',
        'therapy_id',
        'therapy_fee',
        'booking_timeslot',
    ];

    public function therapy()
    {
        return $this->belongsTo(Mst_Therapy::class, 'therapy_id', 'id');
    }

    public function booking()
    {
        return $this->belongsTo(Mst_Therapy::class, 'booking_id', 'id');
    }
}
