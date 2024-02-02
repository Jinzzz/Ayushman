<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_Leave_Config extends Model
{
    use HasFactory,SoftDeletes;
    public $timestamps = true;
    protected $table = 'mst_leave_config';

    protected $fillable = [
        'staff_id',
        'leave_type',
        'credit_period',
        'credit_limit',
    ];
}
