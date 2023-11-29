<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAvailableLeave extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'employee_available_leaves';

    protected $fillable = [
        'staff_id',
        'remark',
        'total_leaves',
        'created_at',
        'updated_at',
    ];
}
