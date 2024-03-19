<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Staff_Advance_Salary extends Model
{
    use HasFactory;
    protected $table = 'trn_staff_salary_processings';
    protected $fillable = [
        'salary_month',
        'staff_id',
        'branch_id',
        'payed_date',
        'net_earning',
        'paid_amount',
        'payment_mode',
        'payed_through_mode',
        'payed_through_ledger_id',
        'reference_number',
        'remarks',
        'created_by',
    ];

    public function staff()
    {
        return $this->belongsTo(Mst_Staff::class, 'staff_id');
    }

    public function branch()
    {
        return $this->belongsTo(Mst_Branch::class, 'branch_id','id');
    }

    public function ledger()
    {
        return $this->belongsTo(Mst_Account_Ledger::class, 'payed_through_ledger_id','id');
    }

}
