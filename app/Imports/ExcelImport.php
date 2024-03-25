<?php

namespace App\Imports;

use App\Models\Mst_Medicine;
use App\Models\Mst_Unit;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use App\Models\Trn_Medicine_Purchase_Invoice_Detail; 
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class ExcelImport implements ToCollection, WithHeadingRow
{
    use Importable, SkipsErrors;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    // public function collection(Collection $rows)
    // {
    //     //dd("string");
    //     foreach ($rows as $row) {
    //         // Check if the date columns exist in the row
    //       if (isset($row['Mdd']) && isset($row['Expd']) && !empty($row['Mdd']) && !empty($row['Expd'])) {
    //             // Convert Excel date value to Carbon date object
    //             $date1 = Carbon::createFromFormat('Y-m-d', '1970-01-01')->addDays($row['Mdd'] - 2)->toDateString();
    //             $date2 = Carbon::createFromFormat('Y-m-d', '1970-01-01')->addDays($row['Expd'] - 2)->toDateString();
    //             //dd($date1,$date2);
    //             // Update the row with the formatted dates
    //             $row['Mdd'] = $date1;
    //             $row['Expd'] = $date2;
    //         }else{
    //             dd("not found");
    //         }

    //         // Process the rest of the data as needed
    //         // ...
    //     }
    //     return $rows;
    // }
    
      public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Check if the date columns exist and are not empty
            if ($row->has('Mfd') && $row->has('Expd') && !empty($row['Mfd']) && !empty($row['Expd'])) {
                // Convert Excel date value to Carbon date object
                $date1 = Carbon::createFromFormat('d-m-Y', $row['Mfd'])->toDateString();
                $date2 = Carbon::createFromFormat('d-m-Y', $row['Expd'])->toDateString();

                // Update the row with the formatted dates
                $row['Mfd'] = $date1;
                $row['Expd'] = $date2;
                $row['test'] = $row['Expd']; // Just for testing
            } else {
                // Handle missing or empty date columns
                // You can log a warning or take appropriate action here
                // For now, let's skip these rows
                continue;
            }

            // Process the rest of the data as needed
        }
        return $rows;
    }
    
}
