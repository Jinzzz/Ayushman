<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_Salary extends Model
{
    use HasFactory,SoftDeletes;
    public $timestamps = true;
    protected $table = 'mst_salary';

    protected $fillable = [
        'staff_id',
        'salary_head',
        'salary_head_type',
        'amount',
    ];
}
