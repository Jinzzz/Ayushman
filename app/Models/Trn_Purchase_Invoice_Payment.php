<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Purchase_Invoice_Payment extends Model
{
    use HasFactory;    
    protected $table = 'trn__purchase__invoice__payments';

    protected $fillable = [
        'purchase_invoice_id',
        'paid_amount',
        'payment_mode',
        'deposit_to',
        'created_at',
        'updated_at',
    ];
}
