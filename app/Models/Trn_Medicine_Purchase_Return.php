<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Trn_Medicine_Purchase_Return extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'trn_medicine_purchase_return';

    protected $primaryKey  = 'purchase_return_id';

    protected $fillable = [
        'purchase_return_id',
        'purchase_return_no',
        'purchase_invoice_id',
        'supplier_id',
        'return_date',
        'pharmacy_id',
        'reason',
        'sub_total',
        'created_by',
      
    ];

    public function supplier()
{
    return $this->belongsTo(Mst_Supplier::class, 'supplier_id');
}

public function Branch()
{
    return $this->belongsTo(Mst_Branch::class, 'branch_id');
}

}
