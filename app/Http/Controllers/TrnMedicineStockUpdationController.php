<?php

namespace App\Http\Controllers;

use App\Models\Mst_Medicine;
use App\Models\Mst_Branch;
use App\Models\Trn_Medicine_Stock;
use Illuminate\Http\Request;

class TrnMedicineStockUpdationController extends Controller
{
    public function index()
    {
        $pageTitle = "Medicine Stock Updation";
        $medicines = Mst_Medicine::pluck('medicine_name','id');
        $branchs = Mst_Branch::get();
        return view('medicine_stock_updation.index',compact('pageTitle','medicines','branchs'));
    }

    public function getGenericName($id)
    {
        $medicine = Mst_Medicine::find($id);
        return response()->json(['generic_name' => $medicine->generic_name]);
    }

    public function getBatchNumbers($id)
    {
        $batchNumbers = Trn_Medicine_Stock::where('medicine_id',$id)->pluck('batch_no');
        return response()->json(['batch_numbers' => $batchNumbers]);
    }

    public function getCurrentStock($medicineId, $batchNo)
    {
        $currentStock = Trn_Medicine_Stock::where('medicine_id', $medicineId)
        ->where('batch_no', $batchNo)
        ->value('current_stock');

        return response()->json(['current_stock' => $currentStock]);
   
    }

    public function updateMedicineStocks(Request $request)
{

    $request->validate([
        'medicine' => 'required|exists:mst_medicines,id',
        'generic_name' => 'required',
        'batch_no' => 'required',
        'current_stock' => 'required|numeric|min:0',
        'new_stock' => 'required|numeric|min:0',
        'remarks' => 'required',
        'branch_id' => 'required',
        
    ]);

    // Update the medicine stocks in the database
    $medicineStock = Trn_Medicine_Stock::where('medicine_id', $request->input('medicine'))
                                        ->where('batch_no', $request->input('batch_no'))
                                        ->first();

    if (!$medicineStock) {
       
        return redirect()->back()->with('error', 'Medicine stock not found.');
    }

    $medicineStock->old_stock = $medicineStock->current_stock;
    $newCurrentStock = $request->input('new_stock');
    $medicineStock->current_stock = $newCurrentStock;
    $medicineStock->remarks = $request->input('remarks');
    $medicineStock->branch_id = $request->input('branch_id');
 
    $medicineStock->save();

  
    return redirect()->back()->with('success', 'Medicine stock updated successfully.');
}
}
