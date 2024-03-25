<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trn_branch_stock_transfer_detail extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'trn_branch_stock_transfer_details';
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
    
    public function stockInfo()
    {
        return $this->belongsTo(Trn_Medicine_Stock::class, 'stock_id','stock_id');
    }

    public function Medicine()
    {
        return $this->belongsTo(Mst_Medicine::class, 'medicine_id');
    }
}
