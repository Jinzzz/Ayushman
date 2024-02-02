<?php

namespace App\Helpers;

use App\Models\Mst_Medicine;
use Illuminate\Http\Request;


class AdminHelper
{
    public static function getProductId($medicineCode) 
    {
        $medicine = Mst_Medicine::where('medicine_code',$medicineCode)->first();

        return $medicine->id??null;
    }


    public static function getUnitId($medicineCode)
    {
        $medicine = Mst_Medicine::where('medicine_code', $medicineCode)->first();

        return $medicine->unit_id ?? null;
    }
}