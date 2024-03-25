<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trn_branch_stock_transfer extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'trn_branch_stock_transfers';
    protected $fillable = [
        'transfer_code',
        'from_pharmacy_id',
        'to_pharmacy_id',
        'transfer_date',
        'from_branch_id',
        'to_branch_id',
        'notes',
        'reference_file',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function stockTransferDetails()
    {
        return $this->hasMany(Trn_branch_stock_transfer_detail::class, 'stock_transfer_id');
    }
    public function pharmacy()
    {
        return $this->belongsTo(Mst_Pharmacy::class, 'from_pharmacy_id');
    }

    public function pharmacys()
    {
        return $this->belongsTo(Mst_Pharmacy::class, 'to_pharmacy_id');
    }
    
}
