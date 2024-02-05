<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trn_Medicine_Stock_Detail extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'trn_medicine_stock_details';

    protected $fillable = [
        'batch_id',
        'unit_id',
        'sales_rate',
        'mrp',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
