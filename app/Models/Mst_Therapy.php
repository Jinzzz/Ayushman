<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Mst_Therapy extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'mst_therapies';

    protected $fillable = [
        'therapy_name',
        'therapy_cost',
        'remarks',
        'is_active',
        'deleted_at',
    ];
    
    public function bookingTherapyDetails()
    {
        return $this->hasMany(Trn_Booking_Therapy_detail::class, 'therapy_id', 'id');
    }
}
