<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mst_Staff extends Model
{
    use HasFactory;
    protected $table = 'mst_staffs';

    protected $primaryKey = 'staff_id';



    protected $fillable = [
        'staff_type',
        'employment_type',
        'staff_username',
        'password',
        'staff_name',
        'gender',
        'is_active',
        'branch_id',
        'date_of_birth',
        'staff_email',
        'staff_contact_number',
        'staff_address',
        'staff_qualification',
        'staff_work_experience',
        'staff_logon_type',
        'staff_commission_type',
        'staff_commission',
        'staff_booking_fee',
    ];


    public function staffType()
    {
        return $this->belongsTo(Mst_Master_Value::class,'staff_type','id');
    }

    public function employmemntType()
    {
        return $this->belongsTo(Mst_Master_Value::class,'employment_type','id');
    }

    public function Gender()
    {
        return $this->belongsTo(Mst_Master_Value::class,'gender','id');
    }

    public function branch()
    {
        return $this->belongsTo(Mst_Branch::class,'branch_id','branch_id');
    }

    public function stafflogonType()
    {
        return $this->belongsTo(Mst_Master_Value::class,'staff_logon_type','id');
    }

    public function qualification()
    {
        return $this->belongsTo(Mst_Master_Value::class,'staff_qualification','id');
    }

    public function commissionType()
    {
        return $this->belongsTo(Mst_Master_Value::class,'staff_commission_type','id');
    }
}
