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
        'pharmacy_id',
        'batch_no',
        'mfd',
        'expd',
        'purchase_rate',
        'sale_rate',
        'purchase_unit_id',
        'opening_stock',
        'current_stock',
        'invoive_id',
        'remarks',
        'created_at',
        'updated_at',
        'deleted_at',
        
    ];
    
    
    public function pharmacy()
    {
        return $this->belongsTo(Mst_Pharmacy::class, 'pharmacy_id', 'id');
    }
    
    public function medicines()
    {
        return $this->belongsTo(Mst_Medicine::class, 'medicine_id');
    }

    public function stockTransfers()
    {
        return $this->hasMany(MstStockTransferTherapy::class, 'medicine_id', 'medicine_id');
    }
}
