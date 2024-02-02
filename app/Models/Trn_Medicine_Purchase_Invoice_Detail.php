<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trn_Medicine_Purchase_Invoice_Detail extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'trn_medicine_purchase_invoice_details';
    protected $primaryKey = 'purchase_invoice_details_id';
    protected $fillable = [
        'invoice_id',
        'product_id',
        'unit_id',
        'quantity',
        'free_quantity',
        'free_quantity_unit_id',
        'batch_no',
        'mfd',
        'expd',
        'rate',
        'tax_value',
        'tax_amount',
        'discount',
        'amount',
        'created_by',
        'updated_by',
        
    ];
}