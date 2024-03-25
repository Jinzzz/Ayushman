<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Ledger_Posting extends Model
{
    use HasFactory;
    protected $table = 'trn_ledger_postings';

    protected $fillable = [
        'posting_date',
        'voucher_type_id',
        'master_id',
        'account_ledger_id',
        'debit',
        'credit',
        'branch_id',
        'narration',
        'reference_number',
        'entity_id',
        'transaction_id'
    ];
    
     public function ledger()
    {
        return $this->belongsTo(Mst_Account_Ledger::class, 'account_ledger_id','id');
    }
     public function branch()
    {
        return $this->belongsTo(Mst_Branchr::class, 'branch_id','id');
    }
}
