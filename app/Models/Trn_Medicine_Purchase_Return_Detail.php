<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Trn_Medicine_Purchase_Return_Detail extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'trn_medicine_purchase_return_details';

    protected $fillable = [
        'purchase_return_id',
        'product_id',
        'quantity_id',
        'unit_id',
        'rate',
        'return_quantity',
        'created_at',
        'pharmacy_id',
        'return_rate'
    ];
}
