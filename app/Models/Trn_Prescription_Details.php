<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Prescription_Details extends Model
{
    use HasFactory;
    protected $table = 'trn__prescription__details';
    protected $primaryKey = 'prescription_details_id';

    protected $fillable = [
        'priscription_id',
        'medicine_id',
        'duration',
        'medicine_dosage',
        'remarks',
    ];

    public function prescription()
    {
        return $this->belongsTo(Trn_Prescription::class, 'priscription_id');
    }

    public function medicine()
    {
        return $this->belongsTo(Mst_Medicine::class, 'medicine_id');
    }

    public function medicineDosage()
    {
        return $this->belongsTo(Mst_Medicine_Dosage::class, 'medicine_dosage');
    }
}
