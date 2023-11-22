<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mst_Wellness_Therapyrooms extends Model
{
    use HasFactory;
    protected $table = 'mst__wellness__therapyrooms';

    protected $fillable = [
        'wellness_id',
        'branch_id',
        'therapy_room_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Mst_Branch::class, 'branch_id');
    }

    public function wellness()
    {
        return $this->belongsTo(Mst_Wellness::class, 'wellness_id');
    }

    public function therapyRoom()
    {
        return $this->belongsTo(Mst_Therapy_Room::class, 'therapy_room_id');
    }
}
