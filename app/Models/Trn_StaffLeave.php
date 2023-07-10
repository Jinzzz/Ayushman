<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_StaffLeave extends Model
{
    use HasFactory;
    protected $table='trn_staff_leaves';
    public function leave_type()
    {
      return $this->belongsTo(Mst_LeaveType::class);
    }
}
