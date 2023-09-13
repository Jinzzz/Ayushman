<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mst_Therapy_Room_Assigning extends Model
{
    use HasFactory;
    protected $table = 'mst_therapy_room_assigning';


    public function therapyroomName()
    {
        return $this->belongsTo(Mst_Therapy_Room::class,'therapy_room_id','id');
    }

    public function branch()
    {
        return $this->belongsTo(Mst_Branch::class,'branch_id','branch_id');
    }

    public function staff()
    {
        return $this->belongsTo(Mst_Staff::class,'staff_id','staff_id');
    }
}
