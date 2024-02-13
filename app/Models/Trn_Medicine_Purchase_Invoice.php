<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Trn_Medicine_Purchase_Invoice extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'trn_medicine_purchase_invoices';
    protected $primaryKey = 'purchase_invoice_id';

    protected $fillable = [
        'purchase_invoice_id',
        'purchase_invoice_no',
        'supplier_id',
        'invoice_date',
        'due_date',
        'pharmacy_id',
        'credit_limit',
        'current_credit',
        'sub_total',
        'item_wise_discount',
        'bill_discount',
        'total_tax',
        'round_off',
        'total_amount',
        'is_paid',
        'paid_amount',
        'payment_mode',
        'deposit_to',
        'reference_code',
        'created_by',
    ];

    public function Supplier()
    {
        return $this->belongsTo(Mst_Supplier::class, 'supplier_id');
    }

    public function Pharmacy()
    {
        return $this->belongsTo(Mst_Pharmacy::class, 'pharmacy_id','id');
    }
    
    public function Branch()
    {
        return $this->belongsTo(Mst_Branch::class, 'branch_id');
    }

    public function purchaseInvoiceDetails()
    {
        return $this->hasMany(Trn_Medicine_Purchase_Invoice_Detail::class, 'invoice_id','purchase_invoice_id');
    }
}
