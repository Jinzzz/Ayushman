<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_staff_salary_processing_detail extends Model
{
    use HasFactory;

    protected $table = 'trn_staff_salary_processing_details';
    protected $fillable = [
        'salary_processing_id',
        'salary_head_id',
        'amount',
    ];

    public function staff()
    {
        return $this->belongsTo(Trn_staff_salary_processing::class, 'salary_processing_id','id');
    }
    public function salary_head()
    {
        return $this->belongsTo(Salary_Head_Master::class, 'salary_head_id','id');
    }
    
}
