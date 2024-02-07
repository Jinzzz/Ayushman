<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Trn_income_expense extends Model
{
    use HasFactory,SoftDeletes;
    protected $table ='trn_income_expenses';

    protected $fillable = [
        'income_expense_type_id',
        'income_expense_date',
        'income_expense_ledger_id',
        'income_expense_amount',
        'transaction_mode_id',
        'transaction_account_id',
        'reference',
        'notes',
        'reference_file',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_at',
        'deleted_by',
    ];
    
    public function ledger()
    {
        return $this->belongsTo(Mst_Account_Ledger::class, 'income_expense_ledger_id','id');
    }
}
