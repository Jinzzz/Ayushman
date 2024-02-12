<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_branch_stock_transfer_detail extends Model
{
    use HasFactory;
    protected $table = 'trn_branch_stock_transfers';
    protected $fillable = [
        'stock_transfer_id',
        'medicine_id',
        'stock_id',
        'transfered_quantity',
    ];

    public function stockTransfer()
    {
        return $this->belongsTo(Trn_branch_stock_transfer::class, 'stock_transfer_id');
    }
}
