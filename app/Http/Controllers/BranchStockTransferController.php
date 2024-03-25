<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Trn_branch_stock_transfer;
use App\Models\Mst_Pharmacy;
use App\Models\Mst_Medicine;
use App\Models\Trn_branch_stock_transfer_detail;
use App\Models\Trn_Medicine_Stock;
use App\Models\Mst_Staff;

class BranchStockTransferController extends Controller
{
    public function index(Request $request)
    {
        $pdatas = Trn_branch_stock_transfer::orderBy('created_at', 'DESC')
        ->with('pharmacy')
        ->with('pharmacys');
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $pdatas->whereIn('from_pharmacy_id', $mappedPharmacies);
        }
        
        return view('branch-stock-transfer.index', [
            'processDatas' =>  $pdatas->get(),
            'pageTitle' => 'Stock Transfer to Pharmacies'
        ]);
    }

    public function create(Request $request)
    {
        return view('branch-stock-transfer.create', [
            'pharmacies' => Mst_Pharmacy::where('status',1)->orderBy('created_at','DESC')->get(),
            'medicines' => Mst_Medicine::where('is_active',1)->orderBy('created_at','DESC')->get(),
            'pageTitle' => 'Add Stock Transfer to Pharmacy'
        ]);
    }

    public function getBatchDetails(Request $request)
    {
        $medicineId = $request->input('medicine_id');
        $pharmacyId = $request->input('pharmacy_id');
        $stockDetails = Trn_Medicine_Stock::where('medicine_id', $medicineId)
            ->where('pharmacy_id', $pharmacyId)
            ->get(['stock_id', 'batch_no', 'mfd', 'expd', 'current_stock','purchase_unit_id','purchase_rate','sale_rate']);
        return response()->json($stockDetails);
    }
    public function stockTransfer(Request $request)
    {

        $validatedData = $request->validate([
            'pharmacy_from' => 'required',
            'pharmacy_to' => 'required',
            'transfer_date' => 'required|date',
            'medicine_id' => 'required',
            'quantity'  => 'required',
            'batch_no'  => 'required',
            'reference_file' => 'nullable|mimes:pdf,doc|max:2048' //2mb
        ]);

        $branch_from_id = Mst_Pharmacy::where('id' , $request->pharmacy_from)->value('branch');
        $branch_to_id = Mst_Pharmacy::where('id' , $request->pharmacy_to)->value('branch');

        $stockTransfer = new Trn_branch_stock_transfer();
        $stockTransfer->from_pharmacy_id = $request->pharmacy_from;
        $stockTransfer->to_pharmacy_id = $request->pharmacy_to;
        $stockTransfer->transfer_date = $request->transfer_date;
        $stockTransfer->from_branch_id = $branch_from_id;
        $stockTransfer->to_branch_id = $branch_to_id;
        $stockTransfer->notes = $request->notes;
        $stockTransfer->created_by = Auth::id();
        $stockTransfer->updated_by = Auth::id();

        $refFile = $request->file('reference_file');
            
        if (!empty($refFile)) {
            if (isset($refFile)) {
                $filename = uniqid('branch_stock_transfer') . '.' . $refFile->getClientOriginalExtension();
                if(isset($refFile) && $refFile->isValid() ) 
                {
                   $path2 = $refFile->move(public_path('assets/uploads/branchstocktransfer/documents'), $filename);
                }
        }
        } else {
            $filename =null;
        }
        $stockTransfer->reference_file = $filename;
        $stockTransfer->save();
        $stockTransfer->transfer_code = 'P-TRN' . $stockTransfer->id;
        $stockTransfer->save();
        $pharmacy_to = $request->pharmacy_to;
        $pharmacy_from = $request->pharmacy_from;
        $batch_no = $request->batch_no;
        $medicine_id = $request->medicine_id; 
        $quantity = $request->quantity;
        $purchase_unit_id = $request->purchase_unit_id;

        array_shift($medicine_id);
        array_shift($batch_no);
        array_shift($quantity);
        $selected_batch_no = $request->selected_batch_no;

        foreach ($medicine_id as $key => $medicineId) {
            $detail = new Trn_branch_stock_transfer_detail();
            $detail->stock_transfer_id = $stockTransfer->id;
            $detail->medicine_id = $medicineId;
            $detail->stock_id = $batch_no[$key];
            $detail->transfered_quantity = $quantity[$key];
            $detail->save();

            
        $medicineStock = Trn_Medicine_Stock::where('pharmacy_id', $pharmacy_to)
                        ->where('medicine_id',$medicineId)
                        ->where('batch_no', $selected_batch_no[$key])
                        ->first();
        
        if ($medicineStock) {
            $medicineStock->current_stock += $quantity[$key];
            $medicineStock->save();
        } else {
        $medicineStockexist = Trn_Medicine_Stock::where('pharmacy_id', $pharmacy_from)
                    ->where('medicine_id', $medicineId)
                    ->where('batch_no', $selected_batch_no[$key])
                    ->first();
    
            // If the record does not exist, insert a new row
            $medicineStocks = new Trn_Medicine_Stock();
            $medicineStocks->pharmacy_id = $pharmacy_to;
            $medicineStocks->medicine_id = $medicineId;
            $medicineStocks->batch_no = $medicineStockexist->batch_no;
            $medicineStocks->mfd = $medicineStockexist->mfd;
            $medicineStocks->expd = $medicineStockexist->expd;
            $medicineStocks->purchase_rate = $medicineStockexist->purchase_rate;
            $medicineStocks->sale_rate = $medicineStockexist->sale_rate;
            $medicineStocks->purchase_unit_id = $medicineStockexist->purchase_unit_id;
            $medicineStocks->current_stock = $quantity[$key];
            $medicineStocks->save();
            $medicineStocks->stock_code = 'STK' . $medicineStocks->stock_id;
            $medicineStocks->save();

        }
        $reduceStock = Trn_Medicine_Stock::where('pharmacy_id', $pharmacy_from)
                ->where('medicine_id', $medicineId)
                ->where('stock_id', $batch_no[$key])
                ->where('batch_no', $selected_batch_no[$key])
                ->first();
        if($reduceStock)
        {
            $reduceStock->current_stock -= $quantity[$key];
            $reduceStock->save();
        }

        }         
        
        return redirect()->route('branch-transfer.index')->with('success', 'Stock transfer added successfully.');
        

    }
    public function stockTransferNew(Request $request)
    {

        $validatedData = $request->validate([
            'pharmacy_from' => 'required',
            'pharmacy_to' => 'required',
            'transfer_date' => 'required|date',
            'medicine_id' => 'required',
            'quantity'  => 'required',
            'batch_no'  => 'required',
            'reference_file' => 'nullable|mimes:pdf,doc|max:2048' //2mb
        ]);

        $branch_from_id = Mst_Pharmacy::where('id' , $request->pharmacy_from)->value('branch');
        $branch_to_id = Mst_Pharmacy::where('id' , $request->pharmacy_to)->value('branch');

        $stockTransfer = new Trn_branch_stock_transfer();
        $stockTransfer->from_pharmacy_id = $request->pharmacy_from;
        $stockTransfer->to_pharmacy_id = $request->pharmacy_to;
        $stockTransfer->transfer_date = $request->transfer_date;
        $stockTransfer->from_branch_id = $branch_from_id;
        $stockTransfer->to_branch_id = $branch_to_id;
        $stockTransfer->notes = $request->notes;
        $stockTransfer->created_by = Auth::id();
        $stockTransfer->updated_by = Auth::id();

        $refFile = $request->file('reference_file');
            
        if (!empty($refFile)) {
            if (isset($refFile)) {
                $filename = uniqid('branch_stock_transfer') . '.' . $refFile->getClientOriginalExtension();
                if(isset($refFile) && $refFile->isValid() ) 
                {
                   $path2 = $refFile->move(public_path('assets/uploads/branchstocktransfer/documents'), $filename);
                }
        }
        } else {
            $filename =null;
        }
        $stockTransfer->reference_file = $filename;
        $stockTransfer->save();
        $stockTransfer->transfer_code = 'P-TRN' . $stockTransfer->id;
        $stockTransfer->save();
        $pharmacy_to = $request->pharmacy_to;
        $pharmacy_from = $request->pharmacy_from;
        $batch_no = $request->batch_no;
        $medicine_id = $request->medicine_id; 
        $quantity = $request->quantity;
        $purchase_unit_id = $request->purchase_unit_id;

        array_shift($medicine_id);
        array_shift($batch_no);
        array_shift($quantity);
        $selected_batch_no = $request->selected_batch_no;
        $validRowsProcessed = false; // Flag to track if any valid rows are processed

        foreach ($medicine_id as $key => $medicineId) {
            if (isset($medicineId, $batch_no[$key], $quantity[$key])) {
            $validRowsProcessed = true; // Set the flag to true as at l
            $detail = new Trn_branch_stock_transfer_detail();
            $detail->stock_transfer_id = $stockTransfer->id;
            $detail->medicine_id = $medicineId;
            $detail->stock_id = $batch_no[$key];
            $detail->transfered_quantity = $quantity[$key];
            $detail->save();

            
        $medicineStockTo = Trn_Medicine_Stock::where('pharmacy_id', $pharmacy_to)
                        ->where('medicine_id',$medicineId)
                        ->where('batch_no', $selected_batch_no[$key])
                        ->first();
        
        if ($medicineStockTo) {
            $medicineStockTo->current_stock += $quantity[$key];
            $medicineStockTo->save();
        } else {
        $medicineStockexist = Trn_Medicine_Stock::where('pharmacy_id', $pharmacy_from)
                    ->where('medicine_id', $medicineId)
                    ->where('batch_no', $selected_batch_no[$key])
                    ->first();
    
            // If the record does not exist, insert a new row
            $medicineStocksToNew = new Trn_Medicine_Stock();
            $medicineStocksToNew->pharmacy_id = $pharmacy_to;
            $medicineStocksToNew->medicine_id = $medicineId;
            $medicineStocksToNew->batch_no = $medicineStockexist->batch_no;
            $medicineStocksToNew->mfd = $medicineStockexist->mfd;
            $medicineStocksToNew->expd = $medicineStockexist->expd;
            $medicineStocksToNew->purchase_rate = $medicineStockexist->purchase_rate;
            $medicineStocksToNew->sale_rate = $medicineStockexist->sale_rate;
            $medicineStocksToNew->purchase_unit_id = $medicineStockexist->purchase_unit_id;
            $medicineStocksToNew->current_stock = $quantity[$key];
            $medicineStocksToNew->stock_code = 'STK' . $batch_no[$key];
            $medicineStocksToNew->save();

        }
         $medicineStockFrom =  Trn_Medicine_Stock::where('pharmacy_id', $pharmacy_from)
                ->where('medicine_id', $medicineId)
                ->where('stock_id', $batch_no[$key])
                ->where('batch_no', $selected_batch_no[$key])
                ->first();
        if($medicineStockFrom)
        {
            $medicineStockFrom->current_stock -= $quantity[$key];
            $medicineStockFrom->save();
            
        }
        else
        {
             // If the record does not exist, insert a new row
            $medicineStocksFromNew = new Trn_Medicine_Stock();
            $medicineStocksFromNew->pharmacy_id = $pharmacy_from;
            $medicineStocksFromNew->medicine_id = $medicineId;
            $medicineStocksFromNew->batch_no = $selected_batch_no[$key];
            $medicineStocksFromNew->mfd = $medicineStockexist->mfd;
            $medicineStocksFromNew->expd = $medicineStockexist->expd;
            $medicineStocksFromNew->purchase_rate = $medicineStockexist->purchase_rate;
            $medicineStocksFromNew->sale_rate = $medicineStockexist->sale_rate;
            $medicineStocksFromNew->purchase_unit_id = $medicineStockexist->purchase_unit_id;
            $medicineStocksFromNew->current_stock = $quantity[$key];
            $medicineStocksFromNew->stock_code = 'STK' . $batch_no[$key];
            $medicineStocksFromNew->save();
            
        }
       
            }

        } 
        if (!$validRowsProcessed) {
        return redirect()->back()->with('error', 'No valid rows found. Please fill in the required fields.');
    }
        
        return redirect()->route('branch-transfer.index')->with('success', 'Stock transfer added successfully.');
        

    }

    public function show($id)
    {
        $stockDetails = Trn_branch_stock_transfer_detail::join('trn_branch_stock_transfers', 'trn_branch_stock_transfers.id', '=', 'trn_branch_stock_transfer_details.stock_transfer_id')
                      ->join('mst_medicines', 'mst_medicines.id', '=', 'trn_branch_stock_transfer_details.medicine_id')
                      ->join('trn_medicine_stocks', 'trn_medicine_stocks.stock_id', '=', 'trn_branch_stock_transfer_details.stock_id')
                      ->where('trn_branch_stock_transfers.id', $id)
                      ->get();
               
        return view('branch-stock-transfer.show',compact('stockDetails'));
    
    }

     public function destroy($id)
    {
     
        $stockTransfer = Trn_branch_stock_transfer::findOrFail($id);
        $stockTransferDetails = Trn_branch_stock_transfer_detail::where('stock_transfer_id', $id)->get();
        
        foreach ($stockTransferDetails as $detail) {
            $trnMedicineStock = Trn_Medicine_Stock::where('stock_id', $detail->stock_id)->first();

            $medicineStock = Trn_Medicine_Stock::where('stock_id', $trnMedicineStock->stock_id)
                            ->first();
            if ($medicineStock) {
                $medicineStock->current_stock += $detail->transfered_quantity;
                $medicineStock->save();
                $medicineStockToReleaseStock = Trn_Medicine_Stock::where('pharmacy_id',$stockTransfer->to_pharmacy_id)
                        ->where('medicine_id',$detail->medicine_id)
                        ->where('batch_no', $medicineStock->batch_no)
                        ->where('mfd', $medicineStock->mfd)
                        ->where('expd', $medicineStock->expd)
                        ->first();
                if ($medicineStockToReleaseStock)
                {
                      $medicineStockToReleaseStock->current_stock-=$detail->transfered_quantity;
                    
                    if($medicineStockToReleaseStock->current_stock>0)
                    {
                      
                        $medicineStockToReleaseStock->update();
                        
                    }
                   
                    
                }
            }
             
        }
        $stockTransfer->delete();
        $stockTransferDetails->each->delete();
        return 1;
    
    }
}
