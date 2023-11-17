<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Journel_Entry_Details extends Model
{
    use HasFactory;
    protected $table = 'trn__journel__entry__details';
    protected $primaryKey = 'journel_entry_details_id';

    protected $fillable = [
        'journal_entry_id',
        'account_ledger_id',
        'debit',
        'credit',
        'description',
    ];

    public function ledgerDetails()
    {
        return $this->belongsTo(Mst_Account_Ledger::class, 'id');
    }
}
