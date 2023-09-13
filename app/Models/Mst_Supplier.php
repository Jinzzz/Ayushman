<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mst_Supplier extends Model
{
    use HasFactory;
    protected $table = 'mst_suppliers';

    protected $fillable = [
        'supplier_code',
        'supplier_name',
        'supplier_contact',
        'supplier_email',
        'supplier_address',
        'gstno',
        'remarks',
        'is_active',
    ];
}
