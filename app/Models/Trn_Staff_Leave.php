<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Staff_Leave extends Model
{
    use HasFactory;
    protected $table = 'trn_staff_leaves';

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'leave_date',
        'leave_duration',
        'leave_reason',
        'branch_id',
        'leave_status',
    ];
}
