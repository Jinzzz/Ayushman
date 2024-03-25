<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trn_staff_salary_processing extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'trn_staff_salary_processings';
    protected $fillable = [
        'salary_month',
        'staff_id',
        'branch_id',
        'processed_date',
        'account_ledger_id',
        'bonus',
        'overtime_allowance',
        'other_earnings',
        'other_deductions',
        'lop',
        'total_earnings',
        'total_deductions',
        'net_earning',
        'payment_mode',
        'reference_number',
        'remarks',
        'processing_status', // 1 = pending // 2 = paid
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function staff()
    {
        return $this->belongsTo(Mst_Staff::class, 'staff_id');
    }

    public function branch()
    {
        return $this->belongsTo(Mst_Branch::class, 'branch_id','branch_id');
    }

    public function ledger()
    {
        return $this->belongsTo(Mst_Account_Ledger::class, 'account_ledger_id','id');
    }
     public function details()
    {
        return $this->hasMany(Trn_staff_salary_processing_detail::class, 'salary_processing_id','id');
    }
    public function paymentmode()
    {
        return $this->belongsTo(Mst_Master_Value::class, 'payment_mode','id');
    }
    
}
