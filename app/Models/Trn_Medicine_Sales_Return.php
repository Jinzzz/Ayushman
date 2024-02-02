<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trn_Medicine_Sales_Return extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'trn__medicine__sales__returns';
    protected $primaryKey = 'sales_return_id';

    protected $fillable = [
        'sales_return_no',
        'sales_invoice_id',
        'patient_id',
        'sales_person_id',
        'return_date',
        'branch_id',
        'sub_total',
        'total_discount',
        'total_tax',
        'total_amount',
        'notes',
        'is_deleted',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];
    public function Staff()
    {
        return $this->belongsTo(Mst_Staff::class, 'sales_person_id');
    }
    
    public function Branch()
    {
        return $this->belongsTo(Mst_Branch::class, 'branch_id');
    }
}
