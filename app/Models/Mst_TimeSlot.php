<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mst_TimeSlot extends Model
{
    use HasFactory;
    protected $table = 'mst_timeslots';


    public function Doctor()
    {
        return $this->belongsTo(Mst_Staff::class, 'staff_id','staff_id');
    }

    public function weekDay()
    {
        return $this->belongsTo(Mst_Master_Value::class,'week_day','id');
    }

    public function timeSlot()
    {
        return $this->belongsTo(Mst_Master_Value::class,'time_slot','id');
    }
}
