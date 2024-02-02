<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_Pharmacy extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'mst_pharmacies';
    protected $fillable = [
        'pharmacy_name',
        'branch',
        'status'
    ];

    public function branch()
    {
        return $this->belongsTo(Mst_Branch::class, 'branch');
    }
}
