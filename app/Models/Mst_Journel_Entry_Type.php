<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mst_Journel_Entry_Type extends Model
{
    use HasFactory;
    protected $table = 'mst__journel__entry__types';
    protected $primaryKey = 'journal_entry_type_id';

    protected $fillable = [
        'journal_entry_type_name',
    ];
}
