<?php

namespace App\Imports;

use App\Models\Mst_Medicine;
use App\Models\Mst_Unit;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use App\Models\Trn_Medicine_Purchase_Invoice_Detail; 
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelImport implements ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        return $rows;
    }
    
    // protected function getProductIdByCode($medicineCode)
    // {
       
    //     $medicine = Mst_Medicine::where('medicine_code', $medicineCode)->first();

    //     return $medicine ? $medicine->id : null;
    // }

    // protected function getUnitId($unitName)
    // {
       
    //     $unit = Mst_Unit::where('unit_name', $unitName)->first();

    //     return $unit ? $unit->id : null;
    // }
}
