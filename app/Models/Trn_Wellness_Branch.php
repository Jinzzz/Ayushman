<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Wellness_Branch extends Model
{
    use HasFactory;
    protected $table = 'trn_wellness_branches';

    protected $fillable = [
        'wellness_id',
        'branch_id',
    ];
}
