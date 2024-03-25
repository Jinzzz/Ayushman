<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class MstDoctor extends Model
{
    use HasFactory,SoftDeletes;
    public $timestamps = true;
    protected $table = 'mst__doctors';

    protected $fillable = [
        'user_id',
        'branch_id',
        'designation_id',
        'qualification',
        'consultation_fee',
    ];
}
