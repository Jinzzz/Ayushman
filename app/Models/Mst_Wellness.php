<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mst_Wellness extends Model
{
    use HasFactory;
    protected $table = 'mst_wellness';

    protected $primaryKey = 'wellness_id';

    protected $fillable = [
        'wellness_name',
        'wellness_cost',
        'wellness_image',
        'offer_price',
        'remarks',
        'is_active',
        'is_deleted',
        'deleted_by',
        'wellness_duration',
        'wellness_terms_conditions',
        'wellness_inclusions',
        'wellness_description',
    ];

    public function branch()
    {
        return $this->belongsTo(Mst_Branch::class, 'branch_id', 'branch_id');
    }
}
