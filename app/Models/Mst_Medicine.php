<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mst_Medicine extends Model
{
    use HasFactory;
    protected $table = 'mst_medicines';


    protected $fillable = [
        'medicine_name'
    ];

    

    public function branch()
    {
        return $this->belongsTo(Mst_Branch::class, 'branch_id', 'branch_id');
    }
    
    public function tax()
    {
        return $this->belongsTo(Mst_Tax::class, 'tax_id', 'id');
    }
    public function unit()
    {
        return $this->belongsTo(Mst_Unit::class, 'unit_id', 'id');
    }
    
    public function itemType()
    {
        return $this->belongsTo(Mst_Master_Value::class,'item_type','id');
    }

    public function medicineType()
    {
        return $this->belongsTo(Mst_Master_Value::class,'medicine_type','id');
    }

    public function dosageForm()
    {
        return $this->belongsTo(Mst_Master_Value::class,'dosage_form','id');
    }

    public function Manufacturer()
    {
        return $this->belongsTo(Mst_Master_Value::class,'manufacturer','id');
    }

}
