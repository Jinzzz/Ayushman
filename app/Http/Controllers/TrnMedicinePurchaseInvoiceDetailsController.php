<?php

namespace App\Http\Controllers;

use App\Models\Mst_Branch;
use App\Models\Mst_Medicine;
use App\Models\Mst_Supplier;
use App\Models\Mst_Master_Value;
use App\Models\Trn_Medicine_Purchase_Invoice_Detail;
use App\Models\Trn_Medicine_Purchase_Invoice;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Imports\ExcelImport;
use App\Helpers\AdminHelper;
use App\Models\Mst_Account_Ledger;
use App\Models\Mst_Pharmacy;
use App\Models\Trn_Ledger_Posting;
use App\Models\Trn_Medicine_Stock;
use App\Models\Trn_Medicine_Stock_Detail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Trn_Stock_Transaction;


class TrnMedicinePurchaseInvoiceDetailsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $pageTitle = "Medicine Purchase Invoice";
            $pharmacies = Mst_Pharmacy::get();
    
            $query = Trn_Medicine_Purchase_Invoice::query();
            $query->join('mst_pharmacies', 'trn_medicine_purchase_invoices.pharmacy_id', '=', 'mst_pharmacies.id')
                  ->select('trn_medicine_purchase_invoices.*', 'mst_pharmacies.*');

    
            if ($request->has('invoice_date') && $request->invoice_date != "") {
                $query->whereRaw('trn_medicine_purchase_invoices.invoice_date = ?', [$request->invoice_date]);
            }
            
            if ($request->has('due_date') && $request->due_date != "") {
                $query->whereRaw('trn_medicine_purchase_invoices.due_date = ?', [$request->due_date]);
            }
            
            if ($request->has('pharmacy_id') && $request->pharmacy_id != "") {
                $query->where('trn_medicine_purchase_invoices.pharmacy_id', $request->pharmacy_id);
            }
    
            $purchaseInvoice = $query->get();
        
            return view('medicine_purchase_invoice.index', compact('pageTitle', 'purchaseInvoice', 'pharmacies'));
        } catch (\Exception $e) {
            return response()->view('errors.custom', ['error' => $e->getMessage()], 500);
        }
    }
    
    

    public function create(Request $request)
    {
        $pageTitle = "Create Medicine Purchase Invoice";
        $products = Mst_Medicine::get();
        foreach($products as $product)
        {
            $product->medicine_id=AdminHelper::getProductId($product->medicine_code);
        }
        $suppliers = Mst_Supplier::select('supplier_name','supplier_id','credit_period')->get();
        $branch = Mst_Branch::pluck('branch_name', 'branch_id');
        $medicines = Mst_Medicine::pluck('medicine_name', 'id');
        $pharmacies = Mst_Pharmacy::get();
     
        $paymentType = Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id');

        // Check if the form is submitted and a file is uploaded
        if ($request->hasFile('products_file')) {
            $request->validate([
                'products_file' => 'required|file|mimes:xlsx,xls',
            ]);

            $excelData = Excel::toArray(new ExcelImport, $request->file('products_file'));

            return view('medicine_purchase_invoice.create', compact('excelData', 'pageTitle', 'suppliers', 'branch', 'medicines', 'paymentType','products'));
        }

        // If no file is uploaded, render the view without $excelData
        return view('medicine_purchase_invoice.create', compact('pageTitle', 'suppliers', 'branch', 'medicines', 'paymentType','products','pharmacies'));
    }

    public function getProductId($medicineCode)
    {
        $productId = AdminHelper::getProductId($medicineCode);

        return response()->json(['product_id' => $productId]);
    }

    public function getUnitId($medicineCode)
    {
        $unitId = AdminHelper::getUnitId($medicineCode);

        return response()->json(['unit_id' => $unitId]);
    }

    public function getCreditDetails(Request $request, $supplierId)
    {
        // Calculate Total Amount Due
        $totalAmountDue = Trn_Medicine_Purchase_Invoice::where('supplier_id', $supplierId)
            ->where('is_paid', 0) // Unpaid invoices
            ->sum('total_amount');

        // Calculate Total Amount Paid
        $totalAmountPaid = Trn_Medicine_Purchase_Invoice::where('supplier_id', $supplierId)
            ->where('is_paid', 1) // Paid invoices
            ->sum('paid_amount');

        // Calculate Current Credit
        $currentCredit = $totalAmountDue - $totalAmountPaid;

        // Retrieve credit limit from the database
        $creditLimit = Mst_Supplier::where('supplier_id', $supplierId)->value('credit_limit');

        return response()->json([
            'creditLimit' => $creditLimit,
            'currentCredit' => $currentCredit,
        ]);
    }

    public function store(Request $request)
    { 
        
        
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required',
            'invoice_no' => 'required',
            'invoice_date' => 'required',
            'pharmacy_id' => 'required',
            'due_date' => 'required',
            'sub_total' => 'required',
            'total_amount' => 'required',
            'paid_amount' => 'required',
            'payment_mode' => 'required',
            'deposit_to' => 'required',
            'reference_code' => 'required',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();

        }
      
            DB::beginTransaction();
            $supplierId = $request->input('supplier_id');
            $totalAmountDue = Trn_Medicine_Purchase_Invoice::where('supplier_id', $supplierId)->sum('total_amount');
            $totalAmountPaid = Trn_Medicine_Purchase_Invoice::where('supplier_id', $supplierId)->sum('paid_amount');
            $currentCredit = $totalAmountDue - $totalAmountPaid;
            $creditLimit = Mst_Supplier::where('supplier_id', $supplierId)->value('credit_limit');

            $invoice = new Trn_Medicine_Purchase_Invoice();
            $invoice->supplier_id = $request->supplier_id;
            $invoice->purchase_invoice_no = $request->invoice_no;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->pharmacy_id = $request->pharmacy_id;
            $invoice->due_date = $request->due_date;
            $invoice->credit_limit = $creditLimit;
            $invoice->current_credit = $currentCredit;
            $invoice->sub_total = $request->sub_total;
            $invoice->item_wise_discount = $request->item_wise_discount;
            $invoice->bill_discount = $request->bill_discount;
            $invoice->total_tax = $request->total_tax;
            $invoice->round_off = $request->round_off;
            $invoice->total_amount = $request->total_amount;
            $invoice->paid_amount = $request->paid_amount;
            $invoice->payment_mode = $request->payment_mode;
            $invoice->deposit_to = $request->deposit_to;
            $invoice->reference_code = $request->reference_code;
            $invoice->created_by = Auth::id();
            $invoice->save();

            $invoiceId = $invoice->purchase_invoice_id;
            $productIds = $request->input('product_id');
            $medicineCodes = $request->input('medicine_code');
            $quantities = $request->input('quantity');
            $unitIds = $request->input('unit_id');
            $rates = $request->input('rate');
            $freeQuantities = $request->input('free_quantity');
            $batchNos = $request->input('batch_no');
            $mfdDates = $request->input('mfd');
            $expdDates = $request->input('expd');
            $taxes = $request->input('tax');
            $amounts = $request->input('amount');
            $discounts = $request->input('discount');

       
            array_shift($medicineCodes);
            array_shift($quantities);
            array_shift($unitIds);
            array_shift($rates);
            array_shift($freeQuantities);
            array_shift($batchNos);
            array_shift($mfdDates);
            array_shift($expdDates);
            array_shift($taxes);
            array_shift($amounts);
            array_shift($discounts);
    
       //dd($freeQuantities);
            foreach ($productIds as $key => $productId) {
        
                $detail = new Trn_Medicine_Purchase_Invoice_Detail();
                $detail->invoice_id = $invoiceId;
                $detail->product_id = $productId;
                $detail->medicine_code = $medicineCodes[$key];
                $detail->unit_id = $unitIds[$key];
                $detail->quantity = $quantities[$key];
                $detail->free_quantity = $freeQuantities[$key];
                $detail->batch_no = $batchNos[$key];
                $detail->mfd = $mfdDates[$key];
                $detail->expd = $expdDates[$key];
                $detail->rate = $rates[$key];
                $detail->tax_amount = $taxes[$key];
                $detail->discount = $discounts[$key];
                $detail->amount = $amounts[$key];
                $detail->created_by = Auth::id();
                $detail->updated_by = Auth::id(); 
                $detail->save();
                $stock = Trn_Medicine_Stock::where('medicine_id', $productId)
                            ->where('expd', $request->input('expd')[$key])
                            ->where('batch_no', $request->input('batch_no')[$key])
                            ->where('purchase_rate', $request->input('rate')[$key])
                            ->first();
                        

                if (!$stock) {
                $current_stock = $quantities[$key] +  $freeQuantities[$key];
                $stock = new Trn_Medicine_Stock();
                $stock->medicine_id = $productId;
                $stock->pharmacy_id =  $request->pharmacy_id;
                $stock->batch_no = $batchNos[$key];
                $stock->mfd = $mfdDates[$key];
                $stock->expd = $expdDates[$key];
                $stock->purchase_rate = $request->input('rate')[$key];
                $stock->purchase_unit_id = $request->input('unit_id')[$key];
                $stock->old_stock =  0;
                $stock->current_stock =  $current_stock;
                $stock->stock_code = 'STK' . uniqid(mt_rand(), true);
                $stock->save();
                $stockDetail = new Trn_Medicine_Stock_Detail();
                $stockDetail->stock_id = $stock->stock_id;
                $stockDetail->unit_id = $request->input('unit_id')[$key];
                $stockDetail->sales_rate = $request->input('rate')[$key];
                $stockDetail->mrp = $request->sub_total;
                $stockDetail->save();
                }
                else{
                    $stock_current = Trn_Medicine_Stock::where('medicine_id', $productId)
                                ->where('expd', $request->input('expd')[$key])
                                ->where('batch_no', $request->input('batch_no')[$key])
                                ->where('purchase_rate', $request->input('rate')[$key])->select('current_stock')
                                ->first();

                    $stock_current = $request->input('quantity')[$key] + $request->input('free_quantity')[$key] + $stock_current->current_stock;

                    $stock->current_stock = $stock_current;
                }
                $stockData = Trn_Medicine_Stock::where('medicine_id', $productId)
                    ->where('expd', $request->input('expd')[$key])
                    ->where('batch_no', $request->input('batch_no')[$key])
                    ->where('purchase_rate', $request->input('rate')[$key])
                    ->first();

                $remarks = "Added " . $request->input('quantity')[$key] . " Quantities in Invoice #" . $invoiceId;

                $log = new Trn_Stock_Transaction();
                $log->medicine_id = $productId;
                $log->invoice_id =  $invoiceId;

                if (!$stockData) {
                    $log->old_stock = 0;
                    $newStock = $request->input('quantity')[$key] + $request->input('free_quantity')[$key];
                } else {
                    $log->old_stock = $stockData->old_stock;
                    $newStock = $stockData->current_stock + $request->input('quantity')[$key] + $request->input('free_quantity')[$key];
                }

                $log->new_stock = $newStock;
                $log->remark = $remarks;
                $log->updated_by = Auth::id();
                $log->updated_on = now();
                $log->save();

            }

                $ledgerEntry = new Trn_Ledger_Posting();
                $ledgerEntry->posting_date = now();
                $ledgerEntry->account_ledger_id = $request->supplier_id;
                $ledgerEntry->pharmacy_id = $request->pharmacy_id;
                $ledgerEntry->master_id = $invoiceId;
                $ledgerEntry->transaction_amount = $request->total_amount;
                $ledgerEntry->narration = 'Purchase Invoice Payment';
                $ledgerEntry->reference_no =$request->reference_code;
                $ledgerEntry->debit = $request->total_amount;
                $ledgerEntry->credit = 0;
                $ledgerEntry->save();
            
                DB::commit();
                return redirect('/medicine-purchase-invoice/index')->with('success', 'Invoice saved successfully');
         
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
    

    public function edit(Request $request,$id)
    {
        $pageTitle = "Edit Purchase Invoice";
        $medicinePurchaseInvoice = Trn_Medicine_Purchase_Invoice::findOrFail($id);
        // dd( $medicinePurchaseInvoice);
        $products = Mst_Medicine::get();
        foreach($products as $product)
        {
            $product->medicine_id=AdminHelper::getProductId($product->medicine_code);
        }
        $suppliers = Mst_Supplier::select('supplier_name', 'supplier_id','credit_period')->get();
        $branch = Mst_Branch::pluck('branch_name','branch_id');
        $medicines = Mst_Medicine::pluck('medicine_name','id');
        $paymentType = Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id');
        $details = Trn_Medicine_Purchase_Invoice_Detail::where('invoice_id', $id)->get();

        // Check if the form is submitted and a file is uploaded
        if ($request->hasFile('products_file')) {
            $request->validate([
                'products_file' => 'required|file|mimes:xlsx,xls',
            ]);

            $excelData = Excel::toArray(new ExcelImport, $request->file('products_file'));

            return view('medicine_purchase_invoice.edit', compact('medicinePurchaseInvoice','excelData', 'pageTitle', 'suppliers', 'branch', 'medicines', 'paymentType','products','details'));
        }

        // If no file is uploaded, render the view without $excelData
        return view('medicine_purchase_invoice.edit', compact('pageTitle','medicinePurchaseInvoice','suppliers', 'branch', 'medicines', 'paymentType','products','details'));

    }
    public function getCreditInfo($supplierId)
    {
        // Fetch credit-related data from the database based on the supplier ID
        $medicinePurchaseInvoice = Mst_Supplier::where('supplier_id', $supplierId)->first();

        if (!$medicinePurchaseInvoice) {
            return response()->json(['error' => 'Supplier not found'], 404);
        }

        // Replace 'credit_limit' and 'current_credit' with your actual attribute names
        $creditLimit = $medicinePurchaseInvoice->credit_limit;
        $currentCredit = $medicinePurchaseInvoice->credit_period;

        // Respond with JSON data
        return response()->json([
            'creditLimit' => $creditLimit,
            'currentCredit' => $currentCredit,
        ]);
    }
    public function getMedicineDetails($productId)
    {
        $medicineDetails = Mst_Medicine::where('mst_medicines.id', $productId)
            ->leftJoin('mst_taxes', 'mst_medicines.tax_id', '=', 'mst_taxes.id')
            ->select(
                'mst_medicines.*',
                'mst_taxes.tax_rate'
            )
            ->first();
    
        // Check if $medicineDetails is not null before accessing its properties
        if ($medicineDetails !== null) {
            return response()->json([
                'medicine_code' => $medicineDetails->medicine_code,
                'unit_id' => $medicineDetails->unit_id,
                'unit_price' => $medicineDetails->unit_price,
                'batch_no' => $medicineDetails->batch_no,
                'tax_rate' => $medicineDetails->tax_rate,
            ]);
        } else {
            // Handle the case where $medicineDetails is null
            return response()->json([
                'error' => 'Medicine details not found.', // You can customize the error message
            ], 404); // You might use a different HTTP status code depending on your use case
        }
    }
    public function destroy($id)
    {

            DB::table('trn_medicine_purchase_invoice_details')
                    ->where('invoice_id', $id)
                    ->delete();

            Trn_Medicine_Purchase_Invoice::where('purchase_invoice_id', $id)->delete();

             DB::commit();
             return 1;     
    }  


}
