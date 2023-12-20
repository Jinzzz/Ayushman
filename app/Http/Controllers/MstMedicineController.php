<?php

namespace App\Http\Controllers;
use App\Models\Mst_Medicine;
use App\Models\Mst_Tax_Group;
use Illuminate\Support\Facades\Auth;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Unit;
use App\Models\Mst_Branch;
use App\Models\Trn_Medicine_Stock;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Mst_Manufacturer;
use Illuminate\Http\Request;

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
        $medicines = $query->orderBy('updated_at', 'desc')->get();
        return view('medicine.index', compact('pageTitle', 'medicines','medicineType'));
    }

    public function create()
    {
        $pageTitle = "Create Medicine";
        $itemType = Mst_Master_Value::where('master_id',13)->pluck('master_value','id');
        $medicineType =  Mst_Master_Value::where('master_id',14)->pluck('master_value','id');
        // $dosageForm =  Mst_Master_Value::where('master_id',15)->pluck('master_value','id');
        $Manufacturer =  Mst_Master_Value::where('master_id',16)->pluck('master_value','id');
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
            'description' => 'required',
            'unit_id' => 'required|exists:mst_units,id',
            'is_active' => 'required',
            'Hsn_code'  => 'unique:mst_medicines,Hsn_code|required',
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
        $request->validate([
        
            'medicine_name' => 'required',
            'generic_name' => 'required',
            'item_type' => 'required',
            'medicine_type' => 'required',
            'tax_id' => 'required|exists:mst__tax__groups,id',      
            'unit_price' => 'required',
            'description' => 'required',
            'unit_id' => 'required|exists:mst_units,id',
            'is_active' => 'required',         
            //'Hsn_code' =>  'required|exists:mst_branches,branch_id',
           
           
            ]);
        $is_active = $request->input('is_active') ? 1 : 0;
    
       
        $medicine = Mst_Medicine::findOrFail($id);
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
        $show = Mst_Medicine::findOrFail($id);
        return view('medicine.show',compact('pageTitle','show'));
    }
     
    public function destroy($id)
    {
        $medicine = Mst_Medicine::findOrFail($id);
        $medicine->delete();
        return 1;

        return redirect()->route('medicine.index')->with('success','Medicine deleted successfully');
    }

    public function changeStatus(Request $request, $id)
    {
        $medicine = Mst_Medicine::findOrFail($id);
    
        $medicine->is_active = !$medicine->is_active;
        $medicine->save();
        return 1;
        return redirect()->back()->with('success','Status changed successfully');
    }
    public function viewStockUpdation($id)
    {
     
        $pageTitle = "Medicine Stock Updation";
        $medicines = Mst_Medicine::findOrFail($id);
        $branchs = Mst_Branch::get();
        $meds = Mst_Medicine::get();
        return view('medicine.stockupdation',compact('pageTitle','medicines','branchs','meds'));
    }
    public function getBatchNumbers(Request $request)
{
    // Retrieve batch numbers based on the selected medicine ID
    $medicineId = $request->input('medicine_id');
    $batchNumbers = Trn_Medicine_Stock::where('medicine_id', $medicineId)->pluck('batch_no');

    return response()->json(['batchNumbers' => $batchNumbers]);
}
    public function getCurrentStock($medicineId, $batchNo)
    {
    
        // Fetch current stock based on the provided parameters
        $currentStock = Trn_Medicine_Stock::where('medicine_id', $medicineId)
            ->where('batch_no', $batchNo)
            ->value('current_stock');

        // Return the current stock as JSON
        return response()->json(['current_stock' => $currentStock]);
    }

    public function updateStockMedicine(Request $request)
    {

        $request->validate([
            'medicine' => 'required',
            'generic_name' => 'required',
            'batch_no' => 'required',
            'current_stock' => 'required|numeric|min:0',
            'new_stock' => 'required|numeric|min:0',
            'remarks' => 'required',
            'branch_id' => 'required',
        ]);

        $branchId = $request->input('branch_id');
        $medicineId = $request->input('medicine');
        $batchNo = $request->input('batch_no'); 
        $newStock = $request->input('new_stock');
        $remarks = $request->input('remark');

        $stock = Trn_Medicine_Stock::where('medicine_id', $medicineId)
            ->where('batch_no', $batchNo)
            ->first();

            if ($stock) {
                $stock->current_stock += $newStock; // Update the current stock
                $stock->save();
            } else {
                    // If the stock entry does not exist, create a new stock entry
                    Trn_Medicine_Stock::create([
                'branch_id' => $branchId,
                'medicine_id' => $medicineId,
                'batch_no' => $batchNo,
                'generic_name' => $genericName,
                'current_stock' => $newStock,
                'remarks' => $remarks,
            ]);
        }

        // Optionally, you can return a response indicating success
        return redirect()->route('medicine.index')->with('success', 'Stock updated/created successfully');

    }


}
