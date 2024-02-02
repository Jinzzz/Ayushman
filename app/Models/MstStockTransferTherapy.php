<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MstStockTransferTherapy extends Model
{
    use HasFactory,SoftDeletes;
    public $timestamps = true;
    protected $table = 'mst_stock_transfer_therapy';

    protected $fillable = [
        'id',
        'medicine_id',
        'therapy_id',
        'batch_id',
        'transfer_quantity',
        'transfer_date',
        'created_by',
        'created_at',
        'updated_at'
    ];



    public function medicine()
    {
        return $this->belongsTo(Mst_Medicine::class, 'medicine_id');
    }

    public function therapy()
    {
        return $this->belongsTo(Mst_Therapy::class, 'therapy_id', 'id');
    }
}
