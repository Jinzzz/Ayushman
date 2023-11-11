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
use App\Models\Trn_Ledger_Posting;
use App\Models\Trn_Medicine_Stock;
use App\Models\Trn_Medicine_Stock_Detail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class TrnMedicinePurchaseInvoiceDetailsController extends Controller
{

    public function index()
    {
        $pageTitle = "Medicine Purchase Invoice";
        $purchaseInvoice = Trn_Medicine_Purchase_Invoice::with('Supplier','Branch')->latest()->get();
        return view('medicine_purchase_invoice.index',compact('pageTitle','purchaseInvoice'));

    }


    public function create(Request $request)
    {
        $pageTitle = "Create Medicine Purchase Invoice";
        $products = Mst_Medicine::get();
        foreach($products as $product)
        {
            $product->medicine_id=AdminHelper::getProductId($product->medicine_code);
        }
        $suppliers = Mst_Supplier::select('supplier_name', 'supplier_id','credit_period')->get();
        $branch = Mst_Branch::pluck('branch_name', 'branch_id');
        $medicines = Mst_Medicine::pluck('medicine_name', 'id');
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
        return view('medicine_purchase_invoice.create', compact('pageTitle', 'suppliers', 'branch', 'medicines', 'paymentType','products'));
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
  
    

    public function store(Request $request)
    {
    //   dd($request->all());
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required',
            'invoice_no' => 'required',
            'invoice_date' => 'required',
            'branch_id' => 'required',
            'due_date' => 'required',
            'sub_total' => 'required',
            'item_wise_discount' => 'required',
            'bill_discount' => 'required',
            'total_tax' => 'required',
            'round_off' => 'required',
            'total_amount' => 'required',
            'paid_amount' => 'required',
            'payment_mode' => 'required',
            'deposit_to' => 'required',
            'reference_code' => 'required',
        ]);
    
        // Begin a database transaction
        DB::beginTransaction();
    
        try {
            if ($validator->fails()) {
                throw new \Exception("Validation failed");
            }
    
            // Save data to trn_medicine_purchase_invoices table
            $invoice = new Trn_Medicine_Purchase_Invoice();
            $invoice->supplier_id = $request->supplier_id;
            $invoice->purchase_invoice_no = $request->invoice_no;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->branch_id = $request->branch_id;
            $invoice->due_date = $request->due_date;
            $invoice->credit_limit = $request->credit_limit;
            $invoice->current_credit = $request->current_credit;
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
            $invoice->created_by = 1; // You may adjust the user ID as needed
            $invoice->save();
    
            // Get the ID of the saved invoice
            $invoiceId = $invoice->purchase_invoice_id;
    
            // Loop through products and save details and stocks
            foreach ($request->input('product_id') as $key => $productId) {
                if ($key != 0) {
                    // Save data to trn_medicine_purchase_invoice_details table
                    $detail = new Trn_Medicine_Purchase_Invoice_Detail();
                    $detail->invoice_id = $invoiceId;
                    $detail->product_id = $productId;
                    $detail->medicine_code = $request->input('medicine_code')[$key];
                    $detail->unit_id = $request->input('unit_id')[$key];
                    $detail->quantity = $request->input('quantity')[$key];
                    $detail->free_quantity = $request->input('free_quantity')[$key];
                    $detail->batch_no = $request->input('batch_no')[$key];
                    $detail->mfd = $request->input('mfd')[$key];
                    $detail->expd = $request->input('expd')[$key];
                    $detail->rate = $request->input('rate')[$key];
                    $detail->tax_amount = $request->input('tax')[$key];
                    $detail->discount = $request->input('discount')[$key];
                    $detail->amount = $request->input('amount')[$key];
                    $detail->save();
    
                    // Save data to trn_medicine_stocks table
                    $stock = Trn_Medicine_Stock::where('medicine_id', $productId)
                        ->where('expd', $request->input('expd')[$key])
                        ->where('batch_no', $request->input('batch_no')[$key])
                        ->where('purchase_rate', $request->input('rate')[$key])
                        ->first();
    
                    if (!$stock) {
                        $stock = new Trn_Medicine_Stock();
                        $stock->stock_code = 'STK' . str_pad($stock->stock_id, 5, '0', STR_PAD_LEFT);
                        $stock->medicine_id = $productId;
                        $stock->branch_id = $request->branch_id;
                        $stock->batch_no = $request->input('batch_no')[$key];
                        $stock->mfd = $request->input('mfd')[$key];
                        $stock->expd = $request->input('expd')[$key];
                        $stock->purchase_rate = $request->input('rate')[$key];
                        $stock->purchase_unit_id = $request->input('unit_id')[$key]; // Adjust as needed
                        $stock->opening_stock = 0; // Set the initial opening stock as needed
                        $stock->current_stock = 0; // Set the initial current stock as needed
                        // $stock->quantity = 0; // Set the initial quantity as needed
                        $stock->save();
                    }
    
                    //Update the stock quantity
                    $stock->current_stock += $request->input('quantity')[$key];
                    $stock->save();
    
                    // Save data to trn_medicine_stock_details table
                    $stockDetail = new Trn_Medicine_Stock_Detail();
                    $stockDetail->stock_id = $stock->stock_id;
                    $stockDetail->unit_id = $request->input('unit_id')[$key];
                    $stockDetail->sales_rate = $request->input('rate')[$key];
                    $stockDetail->save();


                    // Perform ledger posting
                    $ledgerEntry = new Trn_Ledger_Posting();
                    $ledgerEntry->posting_date = now(); // You may adjust the posting date based on your business logic
                    $ledgerEntry->account_ledger_id = $request->supplier_id; // Adjust as needed
                    $ledgerEntry->branch_id = $request->branch_id;
                    $ledgerEntry->transaction_amount = $request->total_amount; // You may need to calculate the actual amount based on your business logic
                    $ledgerEntry->narration = 'Purchase Invoice Payment'; // You may adjust the narration based on your business logic
            
                    // Determine whether it's a debit or credit entry based on your business logic
                    // In this example, I assumed it's a credit entry for supplier payment
                    $ledgerEntry->debit = 0;
                    $ledgerEntry->credit = $request->total_amount;
            
                    $ledgerEntry->save();
                }
            }
    
            // Commit the database transaction
            DB::commit();
    
            // Redirect or respond as needed
            return redirect('/medicine-purchase-invoice/index')->with('success', 'Invoice saved successfully');
        } catch (\Exception $e) {
            dd($e->getMessage());
            // An error occurred, rollback the transaction
            DB::rollback();
    
            // Log the error or handle it as needed
            return redirect()->back()->with('error', 'Failed to save invoice. Please try again.');
        }
    }

    public function getLedgerNames(Request $request)
    {
        // Retrieve the selected payment mode from the request
        $paymentMode = $request->input('payment_mode');
   
        // Set the default sub group id to 44 (for 'Card' and 'Bank')
        $subGroupId = 45;
    
        // If the payment mode is 'Cash', set the sub group id to 45
        if ($paymentMode == '122') {
            $subGroupId = 45;
        } elseif ($paymentMode == '123' || $paymentMode == '124') {
            // If the payment mode is 'Card' or 'Bank', set the sub group id to 44
            $subGroupId = 44;
        }
    
        // Perform a query to fetch ledger names based on the selected payment mode
        $ledgerNames = Mst_Account_Ledger::where('account_sub_group_id', $subGroupId)
            ->pluck('ledger_name', 'id');
    
        return response()->json($ledgerNames);
    }
    

    public function edit(Request $request,$id)
    {
        $pageTitle = "Edit Purchase Invoice";
        $medicinePurchaseInvoice = Trn_Medicine_Purchase_Invoice::findOrFail($id);
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
}
