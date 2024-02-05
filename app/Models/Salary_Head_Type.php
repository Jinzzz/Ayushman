<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary_Head_Type extends Model
{
    use HasFactory;
    protected $table = 'salary_head_types';

    protected $fillable = [
        'salary_head_type',
        'created_at',
        'updated_at'
    ];
}
