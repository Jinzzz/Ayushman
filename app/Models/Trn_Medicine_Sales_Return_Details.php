<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Medicine_Sales_Return_Details extends Model
{
    use HasFactory;
    protected $table = 'trn__medicine__sales__return__details';
    protected $primaryKey = 'sales_return_details_id';

    protected $fillable = [
        'sales_return_id',
        'medicine_id',
        'stock_id',
        'quantity_unit_id',
        'quantity',
        'rate',
        'discount',
        'tax_value',
        'tax_amount',
        'amount',
        'batch_id'
    ];

    public function Unit()
    {
        return $this->belongsTo(Mst_Unit::class, 'quantity_unit_id');
    }
    public function Medicine()
    {
        return $this->belongsTo(Mst_Medicine::class, 'medicine_id');
    }
    
}
