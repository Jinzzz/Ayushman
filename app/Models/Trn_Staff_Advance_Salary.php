<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Staff_Advance_Salary extends Model
{
    use HasFactory;
    protected $table = 'trn__staff__advance__salaries';
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
        return $this->belongsTo(Mst_Branch::class, 'branch_id','branch_id');
    }

    public function ledger()
    {
        return $this->belongsTo(Mst_Account_Ledger::class, 'payed_through_ledger_id','id');
    }
     public function paymentmode()
    {
        return $this->belongsTo(Mst_Master_Value::class, 'payment_mode','id');
    }
     public function payedthroughmode()
    {
        return $this->belongsTo(Mst_Master_Value::class, 'payed_through_mode','id');
    }

}
