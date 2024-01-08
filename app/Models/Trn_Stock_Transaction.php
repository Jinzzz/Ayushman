<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Stock_Transaction extends Model
{
    use HasFactory;
    protected $table = 'trn_stock_transaction';

    protected $fillable = [
        'medicine_id',
        'invoice_id',
        'old_stock',
        'new_stock',
        'remark',
        'updated_by',
        'updated_on',
    ];
}
