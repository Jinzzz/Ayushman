<?php

namespace App\Http\Controllers;

use App\Models\Mst_Medicine;
use App\Models\Mst_Branch;
use App\Models\Mst_Pharmacy;
use App\Models\Trn_Medicine_Stock;
use Illuminate\Http\Request;

class TrnMedicineStockUpdationController extends Controller
{
    public function index()
    {
        $pageTitle = "Medicine Stock Correction";
        $medicines = Mst_Medicine::pluck('medicine_name', 'id');
        $pharmacies = Mst_Pharmacy::get();
        return view('medicine_stock_updation.index', compact('pageTitle', 'medicines', 'pharmacies'));
    }

    public function getGenericName($id)
    {
        $medicine = Mst_Medicine::find($id);
        return response()->json(['generic_name' => $medicine->generic_name]);
    }

    public function getBatchNumbers($id)
    {

        $batchNumbers = Trn_Medicine_Stock::where('medicine_id', $id)->pluck('batch_no');
        return response()->json(['batch_numbers' => $batchNumbers]);
    }
    public function getUnitId($id)
    {
       
        $medicine = Mst_Medicine::find($id);

        if (!$medicine) {
            // Handle the case where the medicine record is not found
            return response()->json(['error' => 'Medicine not found'], 404);
        }
    
        return response()->json(['unit_id' => $medicine->unit_id]);
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
            'medicine' => 'required',
            'batch_no' => 'required',
            'new_stock' => 'required|numeric|min:0',
            'pharmacy_id' => 'required',

        ]);

        $pharmacyId = $request->input('pharmacy_id');
        $medicineId = $request->input('medicine');
        $batchNo = $request->input('batch_no');
        $newStock = $request->input('new_stock');
        $remarks = $request->input('remarks');
        $mfd = $request->input('mfd');
        $expd = $request->input('expd');
        $purchase_rate = $request->input('purchase_rate');
        $purchase_unit_id = $request->input('unit_id');
        $total_stock = $request->current_stock + $request->new_stock;

        $medicineStock = Trn_Medicine_Stock::where('medicine_id', $medicineId)
            ->where('batch_no', $batchNo)
            ->where('mfd', $mfd)
            ->where('expd', $expd)
            ->first();

        if (!$medicineStock) {
            // If the record doesn't exist, create a new one
            Trn_Medicine_Stock::create([
                'medicine_id' => $medicineId,
                'pharmacy_id' => $pharmacyId,
                'batch_no' => $batchNo,
                'mfd' => $mfd,
                'expd' => $expd,
                'purchase_rate' => $purchase_rate,
                'purchase_unit_id' => $purchase_unit_id,
                'opening_stock' => 0,
                'old_stock' => $newStock,
                'current_stock' => $newStock,
                'remarks' => $remarks,
            ]);
        } else {
            // If the record already exists, update it
            $medicineStock->current_stock = $total_stock;
            $medicineStock->save();
        }

        return redirect()->back()->with('success', 'Medicine stock updated successfully.');
    }
}
