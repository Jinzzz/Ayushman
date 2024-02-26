<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffPharmacyMapping extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'staff_pharmacy_mapping';
    protected $fillable = [
        'staff_id',
        'pharmacy',
    ];

    public function pharmacies()
    {
        return $this->hasMany(Mst_Pharmacy::class, 'pharmacy','pharmacy_id');
    }
}
