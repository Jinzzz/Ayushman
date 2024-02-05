<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trn_Journel_Entry extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'trn__journel__entries';
    protected $primaryKey = 'journal_entry_id';

    protected $fillable = [
        'journel_entry_type_id',
        'journel_number',
        'journel_date',
        'financial_year_id',
        'branch_id',
        'notes',
        'total_debit',
        'total_credit',
        'is_deleted',
        'created_by',
        'deleted_by',
        'deleted_at',
    ];

    public function journel_entry_type()
    {
        return $this->belongsTo(Mst_Journel_Entry_Type::class, 'journel_entry_type_id');
    }

    public function branch()
    {
        return $this->belongsTo(Mst_Branch::class, 'branch_id');
    }

    public function journelDetails()
    {
        return $this->hasMany(Trn_Journel_Entry_Details::class, 'journal_entry_id');
    }
}
