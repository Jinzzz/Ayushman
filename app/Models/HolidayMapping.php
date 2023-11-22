<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HolidayMapping extends Model
{
    use HasFactory,SoftDeletes;
    public $timestamps = true;
    protected $table = 'holiday_mappings';

    protected $fillable = [
        'holiday_id',
        'department',
    ];
}
