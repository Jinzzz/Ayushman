<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trn_Medicine_Stock extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'trn_medicine_stocks';
    protected $primaryKey = 'stock_id';

    protected $fillable = [
        'stock_code',
        'medicine_id',
        'branch_id',
        'batch_no',
        'mfd',
        'expd',
        'purchase_rate',
        'purchase_unit_id',
        'opening_stock',
        'current_stock',
        'created_at',
        'updated_at',
        'deleted_at',
        
    ];

    public function stockTransfers()
    {
        return $this->hasMany(MstStockTransferTherapy::class, 'medicine_id', 'medicine_id');
    }
}
