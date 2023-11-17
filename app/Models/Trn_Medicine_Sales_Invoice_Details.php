<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Medicine_Sales_Invoice_Details extends Model
{
    use HasFactory;
    protected $table = 'trn__medicine__sales__invoice__details';
    protected $primaryKey = 'sales_invoice_details_id';

    protected $fillable = [
        'sales_invoice_id',
        'medicine_id',
        'medicine_unit_id',
        'batch_id',
        'quantity',
        'rate',
        'amount',
        'expiry_date',
        'manufactured_date',
        'med_quantity_tax_amount',
    ];

    public function Unit()
    {
        return $this->belongsTo(Mst_Unit::class, 'medicine_unit_id');
    }

    public function Medicine()
    {
        return $this->belongsTo(Mst_Medicine::class, 'medicine_id');
    }
}

