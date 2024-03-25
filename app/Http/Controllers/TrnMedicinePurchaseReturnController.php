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
use Carbon\Carbon;
use App\Models\Trn_Ledger_Posting;
use App\Models\Mst_Staff;
use App\Models\Mst_Master_Value;
use App\Models\Trn_Purchase_Invoice_Payment;
use App\Models\Mst_Account_Ledger;
class TrnMedicinePurchaseReturnController extends Controller
{

    public function index(Request $request)
    {

        $pageTitle = "Medicine Purchase Return";
        $pharmacies = Mst_Pharmacy::get();
        $suppliers = Mst_Supplier::get();

        if(Auth::check() && Auth::user()->user_type_id == 96) {
                $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
                $mappedpharma = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
        $query = Trn_Medicine_Purchase_Return::query();
        $query->join('mst_pharmacies', 'trn_medicine_purchase_return.pharmacy_id', '=', 'mst_pharmacies.id')
            ->join('mst_suppliers', 'trn_medicine_purchase_return.supplier_id', '=', 'mst_suppliers.supplier_id')
            ->whereIn('trn_medicine_purchase_return.pharmacy_id', $mappedpharma)
            ->select('trn_medicine_purchase_return.*', 'mst_pharmacies.*', 'mst_suppliers.*');
        }else{
            $query = Trn_Medicine_Purchase_Return::query();
            $query->join('mst_pharmacies', 'trn_medicine_purchase_return.pharmacy_id', '=', 'mst_pharmacies.id')
            ->join('mst_suppliers', 'trn_medicine_purchase_return.supplier_id', '=', 'mst_suppliers.supplier_id')
            ->select('trn_medicine_purchase_return.*', 'mst_pharmacies.*', 'mst_suppliers.*');
        }


        if ($request->has('purchase_return_no') && $request->purchase_return_no != "") {
            $query->where('trn_medicine_purchase_return.purchase_return_no', $request->purchase_return_no);
        }

        if ($request->filled('return_date')) {
            $query->whereDate('trn_medicine_purchase_return.return_date', '=', $request->return_date);
        }
        if ($request->has('pharmacy_id') && $request->pharmacy_id != "") {
            $query->where('trn_medicine_purchase_return.pharmacy_id', $request->pharmacy_id);
        }
        
        if ($request->has('supplier_id') && $request->supplier_id != "") {
            $query->where('trn_medicine_purchase_return.supplier_id', $request->supplier_id);
        }

        $purchaseReturn = $query->latest('trn_medicine_purchase_return.created_at')->get();


        return view('medicine_purchase_return.index', compact('pageTitle', 'purchaseReturn', 'pharmacies','suppliers'));
    }

    public function create()
    {
        $pageTitle = "Create Medicine Purchase Return";
        $product = Mst_Medicine::pluck('medicine_name', 'id');
        $unit = Mst_Unit::pluck('unit_name', 'id');
        $suppliers = Mst_Supplier::pluck('supplier_name', 'supplier_id');
        $branches = Mst_Branch::where('is_active', 1)->pluck('branch_name', 'branch_id');
        $pharmacies = Mst_Pharmacy::get();
        $paymentType = Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id');
        return view('medicine_purchase_return.create', compact('pageTitle', 'suppliers', 'branches', 'product', 'unit', 'pharmacies','paymentType'));
    }

    public function getPurchaseInvoices(Request $request)
    {

        $purchaseInvoices = Trn_Medicine_Purchase_Invoice::where('supplier_id', $request->input('supplier_id'))
            ->pluck('purchase_invoice_no', 'purchase_invoice_id');

        return response()->json($purchaseInvoices);
    }

    public function getPurchaseInvoiceDetails(Request $request)
    {
        
        $purchaseInvoiceId = $request->input('purchase_invoice_id');
        $details = Trn_Medicine_Purchase_Invoice_Detail::where('invoice_id', $purchaseInvoiceId)->get();
        
        $returnQtys = Trn_Medicine_Purchase_Return::join('trn_medicine_purchase_return_details', 'trn_medicine_purchase_return_details.purchase_return_id', '=', 'trn_medicine_purchase_return.purchase_return_id')
            ->where('trn_medicine_purchase_return.purchase_invoice_id', $purchaseInvoiceId)
            ->get();
        $combinedData = [];
        foreach ($details as $detail) {
            $combinedData[] = [
                'product_id' => $detail->product_id,
                'mfd' => $detail->mfd,
                'expd' => $detail->expd,
                'batch_no' => $detail->batch_no,
                'quantity' => $detail->quantity,
                'unit_id' => $detail->unit_id,
                'rate' => $detail->rate,
                'tax' => $detail->tax_amount,
                'free_quantity' => $detail->free_quantity,
                'returnQty' =>$detail->quantity - $returnQtys->where('product_id', $detail->product_id)->sum('return_quantity') 
            ];
        }
    

return response()->json($combinedData);

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
        
        $branchId = Mst_Pharmacy::where('id', $request->pharmacy_id)->value('branch');
        $purchaseReturn = new Trn_Medicine_Purchase_Return([
            'supplier_id' => $request->input('supplier_id'),
            'purchase_invoice_id' => $request->input('purchase_invoice_id'),
            'return_date' => $request->input('return_date'),
            'pharmacy_id' => $request->input('pharmacy_id'),
            'branch_id' => $branchId,
            'sub_total' => $request->input('total_amount'),
            'reason' => $request->input('notes'),
            'created_by' => Auth::check() ? Auth::id() : null,
        ]);

        $purchaseReturn->save();
        $lastInsertedId = $purchaseReturn->purchase_return_id;
        $purchaseReturnNo = 'PRN' . $lastInsertedId;
        Trn_Medicine_Purchase_Return::where('purchase_return_id', $lastInsertedId)
                                     ->update([
                                         'purchase_return_no' =>$purchaseReturnNo,
                                         ]);
    

        $details = [];
        $subtotal = 0;

        $inputProductIds = $request->input('product_id');
        $inputQuantities = $request->input('quantity');
        $inputUnitIds = $request->input('unit_id');
        $inputRates = $request->input('rate');
        $inputReturnQuantities = $request->input('return_quantity');
        $inputReturnRates = $request->input('return_rate');
        $inputBatchNo = $request->input('batch_no');
        $inputMfd = $request->input('mfd');
        $inputExpd = $request->input('expd');

        array_shift($inputProductIds);
        array_shift($inputQuantities);

        array_shift($inputUnitIds);
        array_shift($inputRates);
        array_shift($inputReturnQuantities);
        array_shift($inputReturnRates);
        array_shift($inputMfd);
         array_shift($inputExpd);
         array_shift($inputBatchNo);


       
        foreach ($inputProductIds as $key => $productId) {
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
                'mfd' =>  $inputMfd[$key],
                'expd' =>  $inputExpd[$key],
                'batch_no' =>  $inputBatchNo[$key],
                // Add other fields as needed
            ]);
            
            $stocks = Trn_Medicine_Stock::where('medicine_id', $productId)
                ->where('mfd', $inputMfd[$key])
                ->where('expd', $inputExpd[$key])
                ->where('batch_no', $inputBatchNo[$key])
                ->where('pharmacy_id', $request->pharmacy_id)
                ->where('invoive_id', $request->purchase_invoice_id)
                ->get();
            if ($stocks->isNotEmpty()) {
                foreach ($stocks as $stock) {
                    $current_stock = $stock->current_stock - $inputReturnQuantities[$key];
                    $stock->update(['current_stock' => $current_stock]);
                }
            }
            }

        $branchId = Mst_Pharmacy::where('id', $request->pharmacy_id)->value('branch');
        //Accounts Payable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PIR' . $purchaseReturn->purchase_return_id,
            'account_ledger_id' => 27,
            'entity_id' => $request->input('supplier_id'),
            'debit' => $request->total_amount,
            'credit' => 0,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $purchaseReturn->purchase_return_id,
            'narration' => 'Purchase Invoice Returns'
        ]);
        // Inventory Asset
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PIR' . $purchaseReturn->purchase_return_id,
            'account_ledger_id' => 3,
            'entity_id' => 0,
            'debit' => 0,
            'credit' => $request->sub_total,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $purchaseReturn->purchase_return_id,
            'narration' => 'Purchase Invoice Returns'
        ]);
        //CGST Input Tax

        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PIR' . $purchaseReturn->purchase_return_id,
            'account_ledger_id' => 10,
            'entity_id' => 0,
            'debit' => 0,
            'credit' =>  $request->total_tax/2,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $purchaseReturn->purchase_return_id,
            'narration' => 'Purchase Invoice Returns'
        ]);
        //SGST Input Tax
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PIR' . $purchaseReturn->purchase_return_id,
            'account_ledger_id' => 11,
            'entity_id' => 0,
            'debit' => 0,
            'credit' => $request->total_tax/2,
            'branch_id' => $branchId,
            'transaction_id' => $purchaseReturn->purchase_return_id,
            'narration' => 'Purchase Invoice Returns'
        ]);
        //Accounts Payable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PIR' . $purchaseReturn->purchase_return_id,
            'account_ledger_id' => 27,
            'entity_id' => $request->input('supplier_id'),
            'debit' => 0,
            'credit' => $request->total_amount,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $purchaseReturn->purchase_return_id,
            'narration' => 'Purchase Invoice Returns'
        ]);
        //Cash/Bank
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PIR' . $purchaseReturn->purchase_return_id,
            'account_ledger_id' => 4,
            'entity_id' => 0,
            'debit' => $request->total_amount,
            'credit' => 0,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $purchaseReturn->purchase_return_id,
            'narration' => 'Purchase Invoice Returns'
        ]);
        // Trn_Purchase_Invoice_Payment::create([
        //             'purchase_invoice_id' => $request->purchase_invoice_id,
        //             'paid_amount' => $request->total_amount,
        //             'payment_mode' => $request->payment_mode,
        //             'deposit_to' => $request->deposit_to,
        //         ]);

        return redirect()->route('medicinePurchaseReturn.index')->with('success', 'Medicine Purchase Returned successfully');
    
    }

    public function edit($id)
    {

        $pageTitle = "Edit Medicine Purchase Return";
        $medicinePurchaseReturn = DB::table('trn_medicine_purchase_return')
            ->join('mst_suppliers', 'trn_medicine_purchase_return.supplier_id', '=', 'mst_suppliers.supplier_id')
            ->join('trn_medicine_purchase_invoices', 'trn_medicine_purchase_return.purchase_invoice_id', '=', 'trn_medicine_purchase_invoices.purchase_invoice_id')
            ->join('mst_pharmacies', 'trn_medicine_purchase_return.pharmacy_id', '=', 'mst_pharmacies.id')
            ->join('trn_medicine_purchase_return_details', 'trn_medicine_purchase_return.purchase_return_id', '=', 'trn_medicine_purchase_return_details.purchase_return_id')
            ->where('trn_medicine_purchase_return.purchase_return_id', $id)
            ->select(
                'trn_medicine_purchase_return.*',
                'mst_suppliers.supplier_name',
                'trn_medicine_purchase_return_details.product_id',
                'mst_pharmacies.pharmacy_name',
                'trn_medicine_purchase_invoices.purchase_invoice_no'
            )
            ->first(); 
          

     
    

        $medicinePurchase = DB::table('trn_medicine_purchase_return_details')
            ->join('mst_medicines', 'trn_medicine_purchase_return_details.product_id', '=', 'mst_medicines.id')
            ->join('mst_taxes', 'mst_medicines.tax_id', '=', 'mst_taxes.id')
            ->where('trn_medicine_purchase_return_details.purchase_return_id', $id)
            ->select(
                'trn_medicine_purchase_return_details.*',
                'mst_medicines.medicine_name',
                'mst_medicines.unit_price',
                'mst_taxes.tax_rate' // Ensure this is the correct column name for tax rate
            )
            ->get();
         
                                                  
    
           $patymentDetails = Trn_Purchase_Invoice_Payment::select('trn__purchase__invoice__payments.*', 'mst_master_values.*','mst__account__ledgers.ledger_name')
                 ->leftJoin('mst_master_values', 'mst_master_values.id', '=', 'trn__purchase__invoice__payments.payment_mode')
                 ->leftJoin('mst__account__ledgers', 'mst__account__ledgers.id', '=', 'trn__purchase__invoice__payments.deposit_to')
                 ->where('trn__purchase__invoice__payments.purchase_invoice_id', $medicinePurchaseReturn->purchase_invoice_id)
                ->first();

        $product = Mst_Medicine::pluck('medicine_name', 'id');
        $unit = Mst_Unit::pluck('unit_name', 'id');
        $suppliers = Mst_Supplier::pluck('supplier_name', 'supplier_id');
        $branches = Mst_Branch::pluck('branch_name', 'branch_id');
        $details = Trn_Medicine_Purchase_Return_Detail::where('purchase_return_id', $id)->get();
        $pharmacies = Mst_Pharmacy::get();
        $paymentType = Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id');
        return view('medicine_purchase_return.edit', compact('pageTitle', 'suppliers', 'branches', 'product', 'unit', 'details', 'medicinePurchaseReturn', 'medicinePurchase', 'pharmacies','paymentType','patymentDetails'));
    }


    public function update(Request $request, $id)
    {


            DB::beginTransaction();

            $purchaseReturn = Trn_Medicine_Purchase_Return::findOrFail($id);
            $purchaseReturn->sub_total = $request->input('sub_total');
            $purchaseReturn->return_date = $request->input('return_date');
            $purchaseReturn->reason = $request->input('notes');
            $updatedBy = Auth::check() ? Auth::id() : null;
            $purchaseReturn->updated_by = $updatedBy;
            $purchaseReturn->save();
            
           // Iterate over the product returns
            foreach ($request->input('product_id') as $key => $productId) {
                // Retrieve the old return quantity before updating the return detail table
                $oldReturnQuantity = Trn_Medicine_Purchase_Return_Detail::where('purchase_return_id', $id)
                    ->where('product_id', $productId)
                    ->value('return_quantity');
        
                $returnQuantity = $request->input('return_quantity')[$key];
                $returnRate = $request->input('rate')[$key];
        
                // Update return quantity and rate for each product return
                Trn_Medicine_Purchase_Return_Detail::where('purchase_return_id', $id)
                    ->where('product_id', $productId)
                    ->update([
                        'return_quantity' => $returnQuantity,
                        'return_rate' => $returnRate,
                    ]);
        
                // Calculate quantity difference and update current stock
                $quantityDifference = $oldReturnQuantity - $returnQuantity;
                $purchaseInvoiceId = $purchaseReturn->purchase_invoice_id;
                $productDetail = Trn_Medicine_Purchase_Return_Detail::where('purchase_return_id', $id)
                ->where('product_id', $productId)
                ->select('mfd', 'expd', 'batch_no')
                ->first();
                if ($productDetail) {
                    $mfd = $productDetail->mfd;
                    $expd = $productDetail->expd;
                    $batchNo = $productDetail->batch_no;
                $stocks = Trn_Medicine_Stock::where('medicine_id', $productId)
                        ->where('mfd', $mfd)
                        ->where('expd', $expd)
                        ->where('batch_no', $batchNo)
                        ->where('invoive_id', $purchaseInvoiceId)
                        ->get();
                if ($stocks->isNotEmpty()) {
                     foreach ($stocks as $stock) {
                        $newCurrentStock = $stock->current_stock + $quantityDifference;
                        $stock->update(['current_stock' => $newCurrentStock]);
                    }
                    }
                }
            }
        
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
                
            $returnedMedicines = Trn_Medicine_Purchase_Return_Detail::where('purchase_return_id', $id)
                ->get();
                
            
            // foreach ($returnedMedicines as $returnedMedicine) {
            //     $productId = $returnedMedicine->product_id;
            //     $oldReturnQuantity = $returnedMedicine->getOriginal('return_quantity');
            //     $returnQuantity = $returnedMedicine->return_quantity;
            //     $purchaseInvoiceId = Trn_Medicine_Purchase_Return::where('purchase_return_id', $id)
            //         ->value('purchase_invoice_id');
            //     $existingStock = Trn_Medicine_Stock::where('medicine_id', $productId)
            //         ->where('invoive_id', $purchaseInvoiceId)
            //         ->first();
                    
               
            
            //     if ($existingStock) {
                    
            //         $quantityDifference = $oldReturnQuantity - $returnQuantity;
            //         $newCurrentStock = $existingStock->current_stock + $quantityDifference;
            //          dd($quantityDifference);
                    
            //         Trn_Medicine_Stock::where('medicine_id', $productId)
            //         ->where('invoive_id', $purchaseInvoiceId)
            //         ->update([
            //             'current_stock' => $newCurrentStock,
            //             ]);
                    
            //         // $existingStock->current_stock = $newCurrentStock;
            //         // $existingStock->save();
            //     } else {
            //         // Handle the case where the stock record is not found
            //     }
            // }


         
            

            DB::commit();

            return redirect()->route('medicinePurchaseReturn.index')->with('success', 'Medicine Purchase Return updated successfully');
       
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
            ->join('mst_taxes', 'mst_medicines.tax_id', '=', 'mst_taxes.id')
            ->where('trn_medicine_purchase_return_details.purchase_return_id', $id)
            ->select(
                'trn_medicine_purchase_return_details.*',
                'mst_medicines.medicine_name', // Replace with the actual columns you need from mst_medicines
                'mst_taxes.tax_rate',
            )
            ->get();
        $product = Mst_Medicine::pluck('medicine_name', 'id');
        $unit = Mst_Unit::pluck('unit_name', 'id');
        $suppliers = Mst_Supplier::pluck('supplier_name', 'supplier_id');

        return view('medicine_purchase_return.show', compact('pageTitle', 'viewPurchaseReturn', 'showDetails', 'product', 'suppliers', 'unit'));
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
        $returnedMedicines = Trn_Medicine_Purchase_Return_Detail::where('purchase_return_id', $id)
        ->get();
        foreach ($returnedMedicines as $returnedMedicine) {
            $productId = $returnedMedicine->product_id;
            $returnQuantity = $returnedMedicine->return_quantity;
            $purchaseInvoiceId = Trn_Medicine_Purchase_Return::where('purchase_return_id', $id)
                ->value('purchase_invoice_id');
            $existingStock = Trn_Medicine_Stock::where('medicine_id', $productId)
                ->where('invoive_id', $purchaseInvoiceId)
                ->first();
            if ($existingStock) {
                $existingStock->current_stock += $returnQuantity;
                $existingStock->save();
            } 
        }

        Trn_Medicine_Purchase_Return_Detail::where('purchase_return_id', $id)->delete();
        Trn_Medicine_Purchase_Return::where('purchase_return_id', $id)->delete();
        return 1;
    }
    
        public function getLedgerNames(Request $request)
    {
       
        $paymentMode = $request->input('payment_mode');
        // Set the default sub group id to 44 (for 'Card' and 'Bank')
        $subGroupId = 45;
     
        if ($paymentMode == '122') {
            $subGroupId = 45;
        } elseif ($paymentMode == '123' || $paymentMode == '124') {
         
            $subGroupId = 44;
        }
    
        $ledgerNames = Mst_Account_Ledger::where('account_sub_group_id', $subGroupId)
            ->pluck('ledger_name','id');
    
        return response()->json($ledgerNames);
    }
}
