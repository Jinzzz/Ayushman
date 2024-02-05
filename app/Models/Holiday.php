<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use HasFactory,SoftDeletes;
    public $timestamps = true;
    protected $table = 'holidays';

    protected $fillable = [
        'holiday_name',
        'leave_type',
        'from_date',
        'to_date',
        'company',
        'year',
    ];

    public function leaveType()
    {
        return $this->belongsTo(Mst_Leave_Type::class, 'leave_type_id');
    }
}
