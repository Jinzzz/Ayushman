<?php

namespace App\Http\Controllers;
use App\Models\Mst_Medicine;
use App\Models\Mst_Tax;
use Illuminate\Support\Facades\Auth;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Branch;
use App\Models\Mst_Unit;
use App\Models\Trn_Medicine_Stock;
use App\Models\Mst_Manufacturer;
use App\Models\Mst_Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Trn_Ledger_Posting;
use App\Models\Mst_Tax_Group;
use Carbon\Carbon;

class MstMedicineController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = "Medicines";
        $medicineType =  Mst_Master_Value::where('master_id',14)->pluck('master_value','id');
        $query = Mst_Medicine::query();

        if($request->has('medicine_name')){
            $query->where('medicine_name','LIKE',"%{$request->medicine_name}%");
        }
       
        if($request->has('generic_name')){
            $query->where('generic_name','LIKE',"%{$request->generic_name}%");
        }
        if($request->has('medicine_type')){
            $query->where('medicine_type','LIKE',"%{$request->medicine_type}%");
        }
        
        // if ($request->filled('branch')) {
        //     $query->whereHas('branch', function ($q) use ($request) {
        //         $q->where('branch_name', 'like', '%' . $request->input('branch') . '%');
        //     });
        // }
        if($request->has('contact_number')){
            $query->where('staff_contact_number','LIKE',"%{$request->contact_number}%");
        }
        // if ($request->filled('manufacturer')) {
        //     $query->whereHas('Manufacturer', function ($q) use ($request) {
        //         $q->where('master_value', 'like', '%' . $request->input('manufacturer') . '%');
        //     });
        // }
        $medicines = $query->orderBy('created_at', 'desc')->get();
        return view('medicine.index', compact('pageTitle', 'medicines','medicineType'));
    }

    public function create()
    {
        $pageTitle = "Create Medicine";
        $itemType = Mst_Master_Value::where('master_id',13)->pluck('master_value','id');
        $medicineType =  Mst_Master_Value::where('master_id',14)->pluck('master_value','id');
        // $dosageForm =  Mst_Master_Value::where('master_id',15)->pluck('master_value','id');
        $Manufacturer = Mst_Manufacturer::where('is_active',  1)
        ->whereNull('deleted_at') 
        ->get();
    // $branches = Mst_Branch::pluck('branch_name','branch_id'); 
    $taxes = Mst_Tax_Group::pluck('tax_group_name','id');

        $units = Mst_Unit::pluck('unit_name','id');
        $randomMedicineCode = 'MED_' . Str::random(8);
        return view('medicine.create', compact('pageTitle','taxes','itemType','medicineType','Manufacturer','units','randomMedicineCode'));
    }

    public function store(Request $request)
    {
  
        $request->validate([
            'medicine_name' => 'required',
            'generic_name' => 'required',
            'item_type' => 'required',
            'medicine_type' => 'required',
            'tax_id' => 'required|exists:mst__tax__groups,id',
            'unit_price' => 'required',
            'unit_id' => 'required|exists:mst_units,id',
            'is_active' => 'required',
            'medicine_code' => 'unique:mst_medicines,medicine_code|required',
        ]);
    
        $medicines = new Mst_Medicine();
        $is_active = $request->input('is_active') ? 1 : 0;
        $medicines->medicine_code = $request->input('medicine_code');
        $medicines->medicine_name = $request->input('medicine_name');
        $medicines->generic_name = $request->input('generic_name');
        $medicines->item_type = $request->input('item_type');
        $medicines->medicine_type = $request->input('medicine_type');
        $medicines->Hsn_code = $request->input('Hsn_code');
        $medicines->tax_id = $request->input('tax_id');
        $medicines->manufacturer = $request->input('manufacturer');
        $medicines->unit_price = $request->input('unit_price');
        $medicines->description = $request->input('description');
        $medicines->unit_id = $request->input('unit_id');
        $medicines->is_active =  $is_active ;
        $medicines->reorder_limit = $request->input('reorder_limit');
        $medicines->created_by = Auth::id();
        $medicines->save();


        return redirect()->route('medicine.index')->with('success','Medicine added successfully');
    }
    

    public function edit($id)
    {
        $pageTitle = "Edit Medicine";
        $medicine = Mst_Medicine::findOrFail($id);
        $itemType = Mst_Master_Value::where('master_id',13)->pluck('master_value','id');
        $medicineType =  Mst_Master_Value::where('master_id',14)->pluck('master_value','id');
        $Manufacturer = Mst_Manufacturer::where('is_active',  1)
                        ->whereNull('deleted_at') 
                        ->get();
        $taxes = Mst_Tax_Group::pluck('tax_group_name','id');
        $units = Mst_Unit::pluck('unit_name','id');

        return view('medicine.edit', compact('pageTitle','medicine','taxes','itemType','medicineType','Manufacturer','units'));
    }

    public function update(Request $request,$id)
    {
        $medicine = Mst_Medicine::findOrFail($id);
        $request->validate([
        
            'medicine_name' => 'required',
            'generic_name' => 'required',
            'item_type' => 'required',
            'medicine_type' => 'required',
            'tax_id' => 'required|exists:mst__tax__groups,id',      
            'unit_price' => 'required',
            'unit_id' => 'required|exists:mst_units,id',
            'medicine_code' => 'required|unique:mst_medicines,medicine_code,' . $medicine->id,
            //'Hsn_code' =>  'required|exists:mst_branches,branch_id',
           
           
            ]);
        $is_active = $request->input('is_active') ? 1 : 0;
    
       
        
        $medicine->medicine_name = $request->input('medicine_name');
        $medicine->generic_name = $request->input('generic_name');
        $medicine->item_type = $request->input('item_type');
        $medicine->medicine_type = $request->input('medicine_type');
        $medicine->Hsn_code = $request->input('Hsn_code');
        $medicine->tax_id = $request->input('tax_id');
        $medicine->manufacturer = $request->input('manufacturer');
        $medicine->unit_price = $request->input('unit_price');
        $medicine->description = $request->input('description');
        $medicine->unit_id = $request->input('unit_id');
        $medicine->is_active =  $is_active ;
        $medicine->reorder_limit = $request->input('reorder_limit');
        $medicine->save();
    
        return redirect()->route('medicine.index')->with('success','Medicine updated successfully'); 
    }

    public function show($id)
    {
        $pageTitle = "View medicine details";
        $show = Mst_Medicine::where('id', $id)
        ->join('mst__manufacturers', 'mst__manufacturers.manufacturer_id', '=', 'mst_medicines.manufacturer')
            ->select('mst_medicines.*', 'mst__manufacturers.name')
            ->first();
 
        $medicineStock = Trn_Medicine_Stock::where('medicine_id', $id)->get();
        return view('medicine.show',compact('pageTitle','show','medicineStock'));

    }
     
    public function destroy($id)
    {
        $medicine = Mst_Medicine::findOrFail($id);
        $medicine->delete();
        return 1;

        return redirect()->route('medicine.index')->with('success','Medicine deleted successfully');
    }

public function updateStatus($medicineId)
{
    $medicine = Mst_Medicine::find($medicineId);
    if (!$medicine) {
        return response()->json(['success' => false]);
    }

    // Toggle the is_active value
    $medicine->is_active = !$medicine->is_active;
    $medicine->save();

    return response()->json(['success' => true, 'status' => $medicine->is_active]);
}

    public function viewStockUpdation()
    {
     
        $pageTitle = "Medicine Initial Stock Updation";
        $stock = Trn_Medicine_Stock::join('mst_medicines', 'trn_medicine_stocks.medicine_id', '=', 'mst_medicines.id')
                    ->select('trn_medicine_stocks.*')->first();
        $pharmacies = Mst_Pharmacy::get();
        $branchs = Mst_Branch::get();
        $meds = Mst_Medicine::get();
        return view('medicine.stockupdation',compact('pageTitle','branchs','meds','pharmacies','stock'));
    }
    public function getBatchNumbers(Request $request)
{
    // Retrieve batch numbers based on the selected medicine ID
    $medicineId = $request->input('medicine_id');
    $batchNumbers = Trn_Medicine_Stock::where('medicine_id', $medicineId)->pluck('batch_no');

    return response()->json(['batchNumbers' => $batchNumbers]);
}
    public function getCurrentStockOld($medicineId, $batchNo)
    {
    
        // Fetch current stock based on the provided parameters
        $currentStock = Trn_Medicine_Stock::where('medicine_id', $medicineId)
            ->where('batch_no', $batchNo)
            ->value('current_stock');

        // Return the current stock as JSON
        return response()->json(['current_stock' => $currentStock]);
    }
    public function getCurrentStock($medicineId, $batchNo)
{
    // Fetch required data based on the provided parameters
    $data = Trn_Medicine_Stock::where('medicine_id', $medicineId)
        ->where('batch_no', $batchNo)
        ->select('current_stock', 'purchase_rate', 'sale_rate')
        ->first();

    // Return the data as JSON
    return response()->json([
        'current_stock' => $data->current_stock,
        'purchase_rate' => $data->purchase_rate,
        'sale_rate' => $data->sale_rate
    ]);
}

//old initial stock update code - modified on 29/02/2024 due to empty row php error issue
// public function updateStockMedicine(Request $request)
//     {

//             $validatedData = $request->validate([
//                 'pharmacy_id' => 'required',
//                 'medicine_id' => 'required',
//                 'batch_no' => 'required',
//                 'mfd' => 'required',
//                 'expd' => 'required',
//                 'new_stock' => 'required',
//                 'purchase_rate' => 'required',
//                 'sale_rate' => 'required',
//             ]);
    
//             $pharmacyId = $request->input('pharmacy_id');
//             $medicineIds = $request->input('medicine_id');
//             $batchNos = $request->input('batch_no');
//             $mfdDates = $request->input('mfd');
//             $expdDates = $request->input('expd');
//             $newStocks = $request->input('new_stock');
//             $purchaseRates = $request->input('purchase_rate');
//             $saleRates = $request->input('sale_rate');

//             // Remove the first element from  array
//             array_shift($medicineIds);
//             array_shift($batchNos);
//             array_shift($mfdDates);
//             array_shift($expdDates);
//             array_shift($newStocks);
//             array_shift($purchaseRates);
//             array_shift($saleRates);
    
//             $existingRecordsMsg = [];

// foreach ($medicineIds as $key => $medicineId) {
//     $existingRecord = Trn_Medicine_Stock::leftjoin('mst_medicines','mst_medicines.id','=','trn_medicine_stocks.medicine_id')->where([
//         'pharmacy_id' => $pharmacyId,
//         'medicine_id' => $medicineId,
//         'batch_no' => $batchNos[$key],
//         'mfd' => $mfdDates[$key],
//         'expd' => $expdDates[$key],
//         'opening_stock' => 0,
//         'current_stock' => $newStocks[$key],
//         'purchase_rate' => $purchaseRates[$key],
//         'sale_rate' => $saleRates[$key],
//     ])->select('trn_medicine_stocks.*','mst_medicines.medicine_name')->first();

//     if (!$existingRecord) {
//         $newStockRecord = Trn_Medicine_Stock::create([
//             'pharmacy_id' => $pharmacyId,
//             'medicine_id' => $medicineId,
//             'batch_no' => $batchNos[$key],
//             'mfd' => $mfdDates[$key],
//             'expd' => $expdDates[$key],
//             'opening_stock' => 0,
//             'current_stock' => $newStocks[$key],
//             'purchase_rate' => $purchaseRates[$key],
//             'sale_rate' => $saleRates[$key],
//         ]);

//         // Update the stock_code
//         $stockCode = 'STK' . $newStockRecord->stock_id;
//         $newStockRecord->update(['stock_code' => $stockCode]);
//         $branchId = Mst_Pharmacy::where('id', $request->pharmacy_id)->value('branch');
        
//         //Accounts Receivable
//         Trn_Ledger_Posting::create([
//             'posting_date' => Carbon::now(),
//             'master_id' => 'ISU' . $newStockRecord->stock_id,
//             'account_ledger_id' => 3,
//             'entity_id' => 0,
//             'debit' => array_sum($purchaseRates),
//             'credit' => 0,
//             'branch_id' => $branchId,
//             'transaction_id' => $newStockRecord->stock_id,
//             'narration' => 'Initial Stock Updation Payment'
//         ]);

//         //Accounts Payable
//         Trn_Ledger_Posting::create([
//             'posting_date' => Carbon::now(),
//             'master_id' => 'ISU' . $newStockRecord->stock_id,
//             'account_ledger_id' => 4,
//             'entity_id' => 0,
//             'debit' => 0,
//             'credit' => array_sum($purchaseRates),
//             'branch_id' => $branchId,
//             'transaction_id' => $newStockRecord->stock_id,
//             'narration' => 'Initial Stock Updation Payment'
//         ]);
//     } else {
//         $existingRecordsMsg[] = $existingRecord->medicine_name;
//     }
// }

//     if (!empty($existingRecordsMsg)) {
//         $medicineNames=implode(",",$existingRecordsMsg);
//         $errorMessage = "Records for medicine $existingRecord->medicine_name already exists.";
//         return redirect()->back()->with('errors', $errorMessage);
//     } else {
//         return redirect()->back()->with('success', 'Stock updated/created successfully');
//     }
//     }  


    public function updateStockMedicine(Request $request)
    {
        $pharmacyId = $request->input('pharmacy_id');
        
        $existingRecordsMsg = [];

        foreach ($request->medicine_id as $key => $medicineId) {
            if (empty($medicineId) || empty($request->batch_no[$key]) || empty($request->mfd[$key]) ||
                empty($request->expd[$key]) || empty($request->new_stock[$key]) || 
                empty($request->purchase_rate[$key]) || empty($request->sale_rate[$key])) {
                continue;
            }
            
            $validatedData = $request->validate([
                'medicine_id.' . $key => 'required',
                'batch_no.' . $key => 'required',
                'mfd.' . $key => 'required',
                'expd.' . $key => 'required',
                'new_stock.' . $key => 'required',
                'purchase_rate.' . $key => 'required',
                'sale_rate.' . $key => 'required',
            ]);
            
            $existingRecord = Trn_Medicine_Stock::leftjoin('mst_medicines', 'mst_medicines.id', '=', 'trn_medicine_stocks.medicine_id')
                ->where([
                    'pharmacy_id' => $pharmacyId,
                    'medicine_id' => $medicineId,
                    'batch_no' => $request->batch_no[$key],
                    'mfd' => $request->mfd[$key],
                    'expd' => $request->expd[$key],
                    'opening_stock' => 0,
                    'current_stock' => $request->new_stock[$key],
                    'purchase_rate' => $request->purchase_rate[$key],
                    'sale_rate' => $request->sale_rate[$key],
                ])->select('trn_medicine_stocks.*', 'mst_medicines.medicine_name')->first();
            
            if (!$existingRecord) {
                $newStockRecord = Trn_Medicine_Stock::create([
                    'pharmacy_id' => $pharmacyId,
                    'medicine_id' => $medicineId,
                    'batch_no' => $request->batch_no[$key],
                    'mfd' => $request->mfd[$key],
                    'expd' => $request->expd[$key],
                    'opening_stock' => 0,
                    'current_stock' => $request->new_stock[$key],
                    'purchase_rate' => $request->purchase_rate[$key],
                    'sale_rate' => $request->sale_rate[$key],
                ]);
                $stockCode = 'STK' . $newStockRecord->stock_id;
                $newStockRecord->update(['stock_code' => $stockCode]);
                $branchId = Mst_Pharmacy::where('id', $request->pharmacy_id)->value('branch');
                
                $subAmount = $request->new_stock[$key] * $request->purchase_rate[$key];
                
                Trn_Ledger_Posting::create([
                    'posting_date' => Carbon::now(),
                    'master_id' => 'ISU' . $newStockRecord->stock_id,
                    'account_ledger_id' => 3,
                    'entity_id' => 0,
                    'debit' => $subAmount,
                    'credit' => 0,
                    'branch_id' => $branchId,
                    'transaction_id' => $newStockRecord->stock_id,
                    'narration' => 'Initial Stock Updation Payment'
                ]);
    
                Trn_Ledger_Posting::create([
                    'posting_date' => Carbon::now(),
                    'master_id' => 'ISU' . $newStockRecord->stock_id,
                    'account_ledger_id' => 4,
                    'entity_id' => 0,
                    'debit' => 0,
                    'credit' => $subAmount,
                    'branch_id' => $branchId,
                    'transaction_id' => $newStockRecord->stock_id,
                    'narration' => 'Initial Stock Updation Payment'
                ]);
            } else {
                $existingRecordsMsg[] = $existingRecord->medicine_name;
            }
        }
        
        if (!empty($existingRecordsMsg)) {
            $medicineNames = implode(",", $existingRecordsMsg);
            $errorMessage = "Records for medicines $medicineNames already exist.";
            return redirect()->back()->with('errors', $errorMessage);
        } else {
            return redirect()->back()->with('success', 'Stock updated/created successfully');
        }
    }
    
    
    public function getUnitPrice(Request $request, $medicineId)
    {
        $medicine = Mst_Medicine::find($medicineId);
    
        if (!$medicine) {
            return response()->json(['success' => false]);
        }
    
        $unitPrice = $medicine->unit_price;
    
        return response()->json(['success' => true, 'unitPrice' => $unitPrice]);
    }

}