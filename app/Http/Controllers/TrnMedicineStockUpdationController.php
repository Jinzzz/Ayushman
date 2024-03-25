<?php

namespace App\Http\Controllers;

use App\Models\Mst_Medicine;
use App\Models\Mst_Branch;
use App\Models\Mst_Pharmacy;
use App\Models\Trn_Medicine_Stock;
use App\Models\Trn_medicine_stock_activity_log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Trn_Ledger_Posting; 


class TrnMedicineStockUpdationController extends Controller
{
    public function index()
    {
        $pageTitle = "Medicine Stock Updation";
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

        $batchNumbers = Trn_Medicine_Stock::where('medicine_id', $id)
            ->select('batch_no', 'mfd', 'expd', 'purchase_rate', 'sale_rate')
            ->get();

        return response()->json(['batch_numbers' => $batchNumbers]);
    }
    
    
    public function customegetBatchNumbers(Request $request)
    {

         $pharmacyId = $request->input('pharmacy_id');
        $medicineId = $request->input('medicine_id');
    
        $batchNumbers = Trn_Medicine_Stock::where('pharmacy_id', $pharmacyId)
            ->where('medicine_id', $medicineId)
            ->select('batch_no', 'mfd', 'expd', 'purchase_rate', 'sale_rate')
            ->get();
    
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
        $stock = Trn_Medicine_Stock::where('medicine_id', $medicineId)
            ->where('batch_no', $batchNo)
            ->select('current_stock', 'purchase_rate', 'sale_rate','mfd','expd')
            ->first();
    
        if (!$stock) {
            return response()->json(['error' => 'Stock not found'], 404);
        }
    
        $currentStock = $stock->current_stock;
        $purchaseRate = $stock->purchase_rate;
        $salesRate = $stock->sale_rate;
        $mfD= $stock->mfd;
        $expD= $stock->expd;
    
        return response()->json([
            'current_stock' => $currentStock,
            'purchase_rate' => $purchaseRate,
            'sale_rate' => $salesRate,
            'mfD' => $mfD,
            'expD' => $expD,
            
        ]);
    }
    

    public function updateMedicineStocks(Request $request)
    {
   
        $request->validate([
            'medicine' => 'required',
            'batch_no' => 'required',
            'new_stock' => 'required|numeric|min:0',
            'pharmacy_id' => 'required',
            'remarks' => 'required',
        ]);
    
        $pharmacyId = $request->input('pharmacy_id');
        $medicineId = $request->input('medicine');
        $batchNo = $request->input('batch_no');
        $newStock = $request->input('new_stock');
        $remarks = $request->input('remarks');
        $mfd = $request->input('mfd');
        $expd = $request->input('expd');
        $purchase_rate = $request->input('purchase_rate');
        $sale_rate = $request->input('sale_rate');
        $purchase_unit_id = $request->input('unit_id');
        $currentStock = $request->input('current_stock');
    
        $medicineStock = Trn_Medicine_Stock::where('medicine_id', $medicineId)
            ->where('batch_no', $batchNo)
            ->where('mfd', $mfd)
            ->where('expd', $expd)
            ->first();
    
        if ($medicineStock) {
            
          
          $medicineStock->current_stock = $newStock;
                      // Log the stock activity
            Trn_medicine_stock_activity_log::create([
                'stock_id' => $medicineStock->stock_id,
                'batch_no' => $batchNo,
                'remarks' => $remarks,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $medicineStock->save();
            $purchaseRate = $request->input('purchase_rate') * $request->input('adjustment_stock');
            $branchId = Mst_Pharmacy::where('id', $request->pharmacy_id)->value('branch');

        //Accounts Receivable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'MSC' . $medicineStock->stock_id,
            'account_ledger_id' => 3,
            'entity_id' => 0,
            'debit' => $purchaseRate,
            'credit' => 0,
            'branch_id' => $branchId,
            'transaction_id' => $medicineStock->stock_id,
            'narration' => 'Medicine Stock Correction Payment'
        ]);

        //Accounts Payable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'MSC' . $medicineStock->stock_id,
            'account_ledger_id' => 4,
            'entity_id' => 0,
            'debit' => 0,
            'credit' =>$purchaseRate,
            'branch_id' => $branchId,
            'transaction_id' => $medicineStock->stock_id,
            'narration' => 'Medicine Stock Correction Payment'
        ]);
    
            return redirect()->back()->with('success', 'Medicine stock updated successfully.');
        } else {
            return redirect()->back()->with('msg', 'Medicine stock not found. Please check the details and try again.');
        }
    }
    

}
