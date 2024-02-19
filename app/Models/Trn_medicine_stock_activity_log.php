<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_medicine_stock_activity_log extends Model
{
    use HasFactory;
    protected $fillable = [
        'stock_id',
        'batch_no',
        'remarks',       
        'created_at',
        'updated_at',
    ];
}
