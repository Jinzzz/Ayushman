<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Sales_Invoice_Payment extends Model
{
    use HasFactory;
    protected $table = 'trn__sales__invoice__payments';

    protected $fillable = [
        'sales_invoice_id',
        'payable_amount',
        'payment_mode',
        'deposit_to',
        'created_at',
        'updated_at',
    ];
    
    public function salesInvoice()
    {
        return $this->belongsTo(Trn_Medicine_Sales_Invoice::class, 'sales_invoice_id','sales_invoice_id');
    }
}
