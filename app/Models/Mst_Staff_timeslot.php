<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_Staff_Timeslot extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'mst__staff__timeslots';

    protected $primaryKey = 'id ';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'staff_id',
        'week_day',
        'timeslot',
        'avg_time_patient',
        'no_tokens',
        'is_available',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function staff()
    {
        return $this->belongsTo(Mst_Staff::class,'staff_id','staff_id');
    }

    public function timeSlot()
    {
        return $this->belongsTo(Mst_TimeSlot::class,'timeslot','id');
    }

    public function weekDay()
    {
        return $this->belongsTo(Mst_Master_Value::class,'week_day','id');
    }

}
