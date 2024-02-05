<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Ledger_Posting extends Model
{
    use HasFactory;
    protected $table = 'trn_ledger_postings';

    protected $fillable = [
        'ledger_posting_id',
        'posting_date',
        'master_id',
        'account_ledger_id',
        'debit',
        'credit',
        'branch_id',
        'transaction_amount',
        'reference_no',
        'narration'

    ];
}
