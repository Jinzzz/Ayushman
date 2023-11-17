<?php

namespace App\Http\Controllers;

use App\Models\Mst_Branch;
use App\Models\Mst_Medicine;
use App\Models\Mst_Supplier;
use App\Models\Mst_Unit;
use App\Models\Trn_Medicine_Purchase_Invoice;
use App\Models\Trn_Medicine_Purchase_Invoice_Detail;
use App\Models\Trn_Medicine_Purchase_Return;
use App\Models\Trn_Medicine_Purchase_Return_Detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrnMedicinePurchaseReturnController extends Controller
{

    public function index()
    {
        $pageTitle = "Medicine Purchase Return";
        $purchaseReturn = Trn_Medicine_Purchase_Return::with('supplier','Branch')->latest()->get();
        return view('medicine_purchase_return.index',compact('pageTitle','purchaseReturn'));

    }


    public function create()
    {
        $pageTitle = "Create Medicine Purchase Return";
        $product = Mst_Medicine::pluck('medicine_name','id');
        $unit = Mst_Unit::pluck('unit_name','id');
        $suppliers = Mst_Supplier::pluck('supplier_name','supplier_id');
        $branches = Mst_Branch::where('is_active',1)->pluck('branch_name','branch_id');
        return view('medicine_purchase_return.create',compact('pageTitle','suppliers','branches','product','unit'));
    }

    public function getPurchaseInvoices(Request $request)
    {
        // Fetch purchase invoices based on the selected supplier
        $purchaseInvoices = Trn_Medicine_Purchase_Invoice::where('supplier_id', $request->input('supplier_id'))
            ->pluck('purchase_invoice_no','purchase_invoice_id');

        return response()->json($purchaseInvoices);
    }

    public function getPurchaseInvoiceDetails(Request $request)
    {
        $purchaseInvoiceId = $request->input('purchase_invoice_id');

        // Fetch details from the database based on $purchaseInvoiceId
        $details = Trn_Medicine_Purchase_Invoice_Detail::where('invoice_id', $purchaseInvoiceId)->get();
    
        return response()->json($details);
    }

   

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'supplier_id' => ['required'],
                // 'purchase_invoice_id' => ['required'],
                'return_date' => ['required'],
                'branch_id' => ['required'],
              
            ],
            [
                'supplier_id.required' => 'Supplier field is required',
                // 'purchase_invoice_id.required' => 'Purchase Invoice is required',
                'return_date.required' => 'Date field is required',
                'branch_id.required' => 'Branch field is required',
               
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create a new MedicinePurchaseReturn instance and save general information
        $purchaseReturn = new Trn_Medicine_Purchase_Return([
            'supplier_id' => $request->input('supplier_id'),
            'purchase_invoice_id' => $request->input('purchase_invoice_id'),
            'return_date' => $request->input('return_date'),
            'branch_id' => $request->input('branch_id'),
            'reason' => $request->input('notes'), 
            'created_by' => 1,
        ]);

        $purchaseReturn->save();
        $lastInsertedId = $purchaseReturn->purchase_return_id;
        $purchaseReturnNo = 'PRN' . $lastInsertedId;
        $purchaseReturn->purchase_return_no = $purchaseReturnNo;
        $purchaseReturn->save();


        // Save details to the MedicinePurchaseReturnDetail table
        $details = [];
        $subtotal = 0;

        foreach ($request->input('product_id') as $key => $productId) {
            if ($key != 0) {
                $itemSubtotal = $request->input('quantity')[$key] * $request->input('rate')[$key];
                $subtotal += $itemSubtotal;
                $details[] = [
                    'purchase_return_id' => $purchaseReturn->purchase_return_id,
                    'product_id' => $productId,
                    'quantity_id' => $request->input('quantity')[$key], // Corrected the field name
                    'unit_id' => $request->input('unit_id')[$key],
                    'rate' => $request->input('rate')[$key],
                    'free_quantity' => $request->input('free_quantity')[$key],
                ];
            }
        }
    
        $purchaseReturn->sub_total = $subtotal;
        $purchaseReturn->save(); // Save the purchase return after updating the subtotal
    
        Trn_Medicine_Purchase_Return_Detail::insert($details);
    
        return redirect()->route('medicinePurchaseReturn.index')->with('success', 'Medicine Purchase Returned successfully');
    }

    public function edit($id)
    {
        $pageTitle = "Edit Medicine Purchase Return";
        $medicinePurchaseReturn = Trn_Medicine_Purchase_Return::findOrFail($id);
        $product = Mst_Medicine::pluck('medicine_name','id');
        $unit = Mst_Unit::pluck('unit_name','id');
        $suppliers = Mst_Supplier::pluck('supplier_name','supplier_id');
        $branches = Mst_Branch::where('is_active',1)->pluck('branch_name','branch_id');

        $details = Trn_Medicine_Purchase_Return_Detail::where('purchase_return_id', $id)->get();

        return view('medicine_purchase_return.edit',compact('pageTitle','suppliers','branches','product','unit','details','medicinePurchaseReturn'));
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'supplier_id' => ['required'],
                'purchase_invoice_id' => ['required'],
                'return_date' => ['required'],
                'branch_id' => ['required'],
            ],
            [
                'supplier_id.required' => 'Supplier field is required',
                'purchase_invoice_id.required' => 'Purchase Invoice is required',
                'return_date.required' => 'Date field is required',
                'branch_id.required' => 'Branch field is required',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Find the MedicinePurchaseReturn instance by ID
        $purchaseReturn = Trn_Medicine_Purchase_Return::findOrFail($id);

        // Update general information
        $purchaseReturn->supplier_id = $request->input('supplier_id');
        $purchaseReturn->purchase_invoice_id = $request->input('purchase_invoice_id');
        $purchaseReturn->return_date = $request->input('return_date');
        $purchaseReturn->branch_id = $request->input('branch_id');
        $purchaseReturn->reason = $request->input('notes');
        $purchaseReturn->updated_by = 1; // Assuming you have a user ID for the updater
        $purchaseReturn->save();

        // Clear existing details for this purchase return
        Trn_Medicine_Purchase_Return_Detail::where('purchase_return_id', $id)->delete();

        // Save updated details to the MedicinePurchaseReturnDetail table
        $details = [];
        $subtotal = 0;

        foreach ($request->input('product_id') as $key => $productId) {
            if ($key != 0) {
                $itemSubtotal = $request->input('quantity')[$key] * $request->input('rate')[$key];
                $subtotal += $itemSubtotal;
                $details[] = [
                    'purchase_return_id' => $id,
                    'product_id' => $productId,
                    'quantity_id' => $request->input('quantity')[$key], // Corrected the field name
                    'unit_id' => $request->input('unit_id')[$key],
                    'rate' => $request->input('rate')[$key],
                    'free_quantity' => $request->input('free_quantity')[$key],
                ];
            }
        }

        $purchaseReturn->sub_total = $subtotal;
        $purchaseReturn->save(); // Save the purchase return after updating the subtotal

        Trn_Medicine_Purchase_Return_Detail::insert($details);

        return redirect()->route('medicinePurchaseReturn.index', $id)->with('success', 'Medicine Purchase Return updated successfully');
    }

    public function show($id)
    {
        $pageTitle = "View Medicine Purchase Return Details";
        $viewPurchaseReturn = Trn_Medicine_Purchase_Return::findOrFail($id);
        $showDetails = Trn_Medicine_Purchase_Return_Detail::where('purchase_return_id', $id)->get();
        $product = Mst_Medicine::pluck('medicine_name','id');
        $unit = Mst_Unit::pluck('unit_name','id');
        $suppliers = Mst_Supplier::pluck('supplier_name','supplier_id');
        $branches = Mst_Branch::where('is_active',1)->pluck('branch_name','branch_id');
        return view('medicine_purchase_return.show',compact('pageTitle','viewPurchaseReturn','showDetails','product','branches','suppliers','unit'));
    } 
   
}
