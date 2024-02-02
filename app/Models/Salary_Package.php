<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary_Package extends Model
{
    use HasFactory,SoftDeletes;
    public $timestamps = true;
    protected $table = 'salary_packages';

    protected $fillable = [
        'id',
        'package_name',
        'company_name',
        'status',
        'remark',
        'salary_head_id',
        'salary_head_type_id',
        'package_amount_type',
        'package_amount_value',
        'created_at',
        'updated_at',
    ];
}
