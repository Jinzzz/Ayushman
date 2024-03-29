<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mst_Branch extends Model
{
    use HasFactory;
    protected $table ='mst_branches';


    protected $primaryKey = 'branch_id';

    protected $fillable = [
        'branch_name',
        'branch_address',
        'is_active',
        'branch_contact_number',
        'branch_email',
        'branch_admin_name',
        'branch_admin_contact_number',
        'created_by',
    ];

    protected $attributes = [
       
        'deleted_by' => false,
    ];
    
}
