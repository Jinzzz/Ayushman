<?php

namespace App\Http\Controllers;

use App\Models\Mst_Branch;
use App\Models\Mst_Medicine;
use App\Models\Mst_Supplier;
use App\Models\Mst_Unit;
use App\Models\Mst_Pharmacy;
use App\Models\Trn_Medicine_Purchase_Invoice;
use App\Models\Trn_Medicine_Purchase_Invoice_Detail;
use App\Models\Trn_Medicine_Purchase_Return;
use App\Models\Trn_Medicine_Purchase_Return_Detail;
use App\Models\Trn_Medicine_Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class TrnMedicinePurchaseReturnController extends Controller
{

    public function index(Request $request)
    {

            $pageTitle = "Medicine Purchase Return";
            $pharmacies = Mst_Pharmacy::get();
                
            $query = Trn_Medicine_Purchase_Return::query();
            $query->join('mst_pharmacies', 'trn_medicine_purchase_return.pharmacy_id', '=', 'mst_pharmacies.id')
                ->join('mst_suppliers', 'trn_medicine_purchase_return.supplier_id', '=', 'mst_suppliers.supplier_id')
                ->select('trn_medicine_purchase_return.*', 'mst_pharmacies.*', 'mst_suppliers.*');
      
                
               if ($request->has('purchase_return_no') && $request->purchase_return_no != "") {
                    $query->where('trn_medicine_purchase_return.purchase_return_no', $request->purchase_return_no);
                }

                if ($request->filled('return_date')) {
                    $query->whereDate('trn_medicine_purchase_return.return_date', '=', $request->return_date);
                }
                if ($request->has('pharmacy_id') && $request->pharmacy_id != "") {
                    $query->where('trn_medicine_purchase_return.pharmacy_id', $request->pharmacy_id);
                }
    
            $purchaseReturn = $query->get();
     
            return view('medicine_purchase_return.index', compact('pageTitle', 'purchaseReturn', 'pharmacies'));
        
      
    }

    public function create()
    {
        $pageTitle = "Create Medicine Purchase Return";
        $product = Mst_Medicine::pluck('medicine_name','id');
        $unit = Mst_Unit::pluck('unit_name','id');
        $suppliers = Mst_Supplier::pluck('supplier_name','supplier_id');
        $branches = Mst_Branch::where('is_active',1)->pluck('branch_name','branch_id');
        $pharmacies = Mst_Pharmacy::get();
        return view('medicine_purchase_return.create',compact('pageTitle','suppliers','branches','product','unit','pharmacies'));
    }

    public function getPurchaseInvoices(Request $request)
    {
       
        $purchaseInvoices = Trn_Medicine_Purchase_Invoice::where('supplier_id', $request->input('supplier_id'))
            ->pluck('purchase_invoice_no','purchase_invoice_id');

        return response()->json($purchaseInvoices);
    }

    public function getPurchaseInvoiceDetails(Request $request)
    {
        $purchaseInvoiceId = $request->input('purchase_invoice_id');
        $details = Trn_Medicine_Purchase_Invoice_Detail::where('invoice_id', $purchaseInvoiceId)->get();
    
        return response()->json($details);
    }

   

    public function store(Request $request)
    {
     
        $validator = Validator::make(
            $request->all(),
            [
                'supplier_id' => ['required'],
                'return_quantity' => ['required'],
                'return_date' => ['required'],
                'pharmacy_id' => ['required'],
                'return_rate' => ['required'],
              
            ],
            [
                'supplier_id.required' => 'Supplier field is required',
                'return_quantity.required' => 'Return Quantity is required',
                'return_date.required' => 'Date field is required',
                'pharmacy_id.required' => 'Pharmacy field is required',
                'return_rate.required' => 'Return rate field is required',
               
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $purchaseReturn = new Trn_Medicine_Purchase_Return([
            'supplier_id' => $request->input('supplier_id'),
            'purchase_invoice_id' => $request->input('purchase_invoice_id'),
            'return_date' => $request->input('return_date'),
            'pharmacy_id' => $request->input('pharmacy_id'),
            'sub_total' => $request->input('sub_total'), 
            'reason' => $request->input('notes'), 
            'created_by' => Auth::check() ? Auth::id() : null,
        ]);

        $purchaseReturn->save();
        $lastInsertedId = $purchaseReturn->purchase_return_id;
        $purchaseReturnNo = 'PRN' . $lastInsertedId;
        $purchaseReturn->purchase_return_no = $purchaseReturnNo;
        $purchaseReturn->save();


        $details = [];
        $subtotal = 0;
        
        $inputProductIds = $request->input('product_id');
        $inputQuantities = $request->input('quantity');
        $inputUnitIds = $request->input('unit_id');
        $inputRates = $request->input('rate');
        $inputFreeQuantities = $request->input('free_quantity');
        $inputReturnQuantities = $request->input('return_quantity');
        $inputReturnRates = $request->input('return_rate');

        array_shift($inputProductIds);
        array_shift($inputQuantities);
       
        array_shift($inputUnitIds);
        array_shift($inputRates);
        array_shift($inputFreeQuantities);
        array_shift($inputReturnQuantities);
        array_shift($inputReturnRates);
   
        foreach ($inputUnitIds as $key => $selected) {

                $productId = $inputProductIds[$key];
                $itemSubtotal = $inputQuantities[$key] * $inputRates[$key];
                $subtotal += $itemSubtotal;

                Trn_Medicine_Purchase_Return_Detail::create([
                    'purchase_return_id' => $purchaseReturn->purchase_return_id,
                    'product_id' => $productId,
                    'quantity_id' => $inputQuantities[$key],
                    'unit_id' => $inputUnitIds[$key],
                    'rate' => $inputRates[$key],
                    'return_quantity' => $inputReturnQuantities[$key],
                    'return_rate' => $inputReturnRates[$key],
                ]);                                               
            
        }
        
    
        return redirect()->route('medicinePurchaseReturn.index')->with('success', 'Medicine Purchase Returned successfully');
    }

    public function edit($id)
    {
        $pageTitle = "Edit Medicine Purchase Return";
        $medicinePurchaseReturn = DB::table('trn_medicine_purchase_return')
        ->join('mst_suppliers', 'trn_medicine_purchase_return.supplier_id', '=', 'mst_suppliers.supplier_id')
        ->join('mst_pharmacies', 'trn_medicine_purchase_return.pharmacy_id', '=', 'mst_pharmacies.id')
        ->join('trn_medicine_purchase_return_details', 'trn_medicine_purchase_return.purchase_return_id', '=', 'trn_medicine_purchase_return_details.purchase_return_id')
        ->where('trn_medicine_purchase_return.purchase_return_id', $id)
        ->select(
            'trn_medicine_purchase_return.*',
            'mst_suppliers.supplier_name',
            'trn_medicine_purchase_return_details.product_id',
            'mst_pharmacies.pharmacy_name',
        )
        ->first(); 
     
        $medicinePurchase = DB::table('trn_medicine_purchase_return_details')
        ->join('mst_medicines', 'trn_medicine_purchase_return_details.product_id', '=', 'mst_medicines.id')
        ->where('trn_medicine_purchase_return_details.purchase_return_id', $id)
        ->select(
            'trn_medicine_purchase_return_details.*',
            'mst_medicines.medicine_name', // Replace with the actual columns you need from mst_medicines
        )
       ->get();
        $product = Mst_Medicine::pluck('medicine_name','id');
        $unit = Mst_Unit::pluck('unit_name','id');
        $suppliers = Mst_Supplier::pluck('supplier_name','supplier_id');
        $branches = Mst_Branch::pluck('branch_name', 'branch_id');
        $details = Trn_Medicine_Purchase_Return_Detail::where('purchase_return_id', $id)->get();
        $pharmacies = Mst_Pharmacy::get();
        return view('medicine_purchase_return.edit',compact('pageTitle','suppliers','branches','product','unit','details','medicinePurchaseReturn','medicinePurchase','pharmacies'));
    }


    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
    
            $purchaseReturn = Trn_Medicine_Purchase_Return::findOrFail($id);
            $purchaseReturn->sub_total = $request->input('sub_total');
            $purchaseReturn->return_date = $request->input('return_date');
            $purchaseReturn->reason = $request->input('notes');
            $updatedBy = Auth::check() ? Auth::id() : null;
            $purchaseReturn->updated_by = $updatedBy;
            $purchaseReturn->save();

    
            foreach ($request->input('product_id') as $key => $productId) {
                Trn_Medicine_Purchase_Return_Detail::where('purchase_return_id', $id)
                    ->where('product_id', $productId)
                    ->update([
                        'return_quantity' => $request->input('return_quantity')[$key],
                        'return_rate' => $request->input('rate')[$key],
                    ]);

                    $returnRates = $request->input('rate');
                    if (!is_null($returnRates) && is_array($returnRates)) {
                        // Make sure $key is within the array bounds to avoid potential errors
                        if (array_key_exists($key, $returnRates)) {
                            Trn_Medicine_Purchase_Return::where('purchase_return_id', $id)
                                ->update([
                                    'sub_total' => array_sum($returnRates),
                                ]);
                        } else {
                            // Handle the case when $key is not present in the 'rate' array
                            // You might want to log an error, throw an exception, or take appropriate action.
                        }
                    } else {
                        // Handle the case when 'rate' is null or not an array
                        // For example, you might want to set a default value for 'sub_total'.
                        Trn_Medicine_Purchase_Return::where('purchase_return_id', $id)
                            ->update([
                                'sub_total' => 0,  // Set a default value or handle it as needed
                            ]);
                    }
    
                Trn_Medicine_Stock::where('medicine_id', $productId)
                    ->update([
                        'current_stock' => $request->input('quantity')[$key] - $request->input('return_quantity')[$key],
                    ]);
            }
    
            DB::commit();
    
            return redirect()->route('medicinePurchaseReturn.index', $id)->with('success', 'Medicine Purchase Return updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log or handle the exception
            return redirect()->back()->with('error', 'An error occurred while updating the Medicine Purchase Return');
        }
    }

    public function show($id)
    {
        $pageTitle = "View Medicine Purchase Return Details"; 
        $viewPurchaseReturn = DB::table('trn_medicine_purchase_return')
        ->join('mst_suppliers', 'trn_medicine_purchase_return.supplier_id', '=', 'mst_suppliers.supplier_id')
        ->join('mst_pharmacies', 'trn_medicine_purchase_return.pharmacy_id', '=', 'mst_pharmacies.id')
        ->join('trn_medicine_purchase_return_details', 'trn_medicine_purchase_return.purchase_return_id', '=', 'trn_medicine_purchase_return_details.purchase_return_id')
        ->where('trn_medicine_purchase_return.purchase_return_id', $id)
        ->select(
            'trn_medicine_purchase_return.*',
            'mst_suppliers.supplier_name',
            'trn_medicine_purchase_return_details.product_id',
            'mst_pharmacies.pharmacy_name',
        )
        ->first(); 
                                  
        $showDetails = DB::table('trn_medicine_purchase_return_details')
                    ->join('mst_medicines', 'trn_medicine_purchase_return_details.product_id', '=', 'mst_medicines.id')
                    ->where('trn_medicine_purchase_return_details.purchase_return_id', $id)
                    ->select(
                        'trn_medicine_purchase_return_details.*',
                        'mst_medicines.medicine_name', // Replace with the actual columns you need from mst_medicines
                    )
                   ->get();
        $product = Mst_Medicine::pluck('medicine_name','id');
        $unit = Mst_Unit::pluck('unit_name','id');
        $suppliers = Mst_Supplier::pluck('supplier_name','supplier_id');
      
        return view('medicine_purchase_return.show',compact('pageTitle','viewPurchaseReturn','showDetails','product','suppliers','unit'));
    } 
    public function getInvoiceBranch(Request $request)
    {
        $branches = Trn_Medicine_Purchase_Invoice::where('purchase_invoice_id', $request->input('purchase_invoice_id'))
            ->get();
    
        $data = [];
    
        foreach ($branches as $branch) {
            $branchData = Mst_Branch::where('branch_id', $branch->branch_id)->first();
    
            if ($branchData) {
                $data[$branchData->branch_id] = $branchData->branch_name;
            }
        }
    
        return response()->json($data);
    }

    public function destroy($id)
    {
            // Delete from 'medicine_purchase_return' table
            Trn_Medicine_Purchase_Return::where('purchase_return_id', $id)
                                       ->delete();
            return 1;
    }

    
    
   
}
