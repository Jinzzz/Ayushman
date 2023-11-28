<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Add the correct namespace

class Salary_Head_Master extends Model
{
    use HasFactory,SoftDeletes;
    public $timestamps = true;
    protected $table = 'salary_head_masters';

    protected $fillable = [
        'id',
        'salary_head_name',
        'salary_head_type',
        'status',
        'remark',
        'company',
        'reason',
        'created_at',
        'updated_at'
    ];
}
