<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_staff_cash_deposit extends Model
{
    use HasFactory;
    protected $table = 'trn_staff_cash_deposits';
    protected $fillable = [
        'transfer_from_account',
        'transfer_to_account',
        'branch_id',
        'transfer_amount',
        'transfer_date',
        'reference_number',
        'remarks',
        'created_by',
    ];

    public function branch()
    {
        return $this->belongsTo(Mst_Branch::class, 'branch_id','id');
    }

    public function tranfserFromLedger()
    {
        return $this->belongsTo(Mst_Account_Ledger::class, 'account_ledger_id','transfer_from_account');
    }

    public function tranfserToLedger()
    {
        return $this->belongsTo(Mst_Account_Ledger::class, 'account_ledger_id','transfer_to_account');
    }

}
