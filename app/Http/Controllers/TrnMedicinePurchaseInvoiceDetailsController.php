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
use App\Models\Mst_Unit;
use App\Models\Trn_Medicine_Stock;
use App\Models\Trn_Medicine_Stock_Detail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Trn_Stock_Transaction;
use Carbon\Carbon;
use App\Models\Trn_Ledger_Posting;
use App\Models\Mst_Staff;
use Exception;


class TrnMedicinePurchaseInvoiceDetailsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $pageTitle = "Medicine Purchase Invoice";
            $pharmacies = Mst_Pharmacy::get();
            $suppliers =  Mst_Supplier::get();
            
            if(Auth::check() && Auth::user()->user_type_id == 96) {
                $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
                $mappedpharma = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
    
            $query = Trn_Medicine_Purchase_Invoice::query();
            $query->join('mst_pharmacies', 'trn_medicine_purchase_invoices.pharmacy_id', '=', 'mst_pharmacies.id')
            ->whereIn('trn_medicine_purchase_invoices.pharmacy_id', $mappedpharma)
                  ->select('trn_medicine_purchase_invoices.*', 'mst_pharmacies.*');
            }else{
                 $query = Trn_Medicine_Purchase_Invoice::query();
                 $query->join('mst_pharmacies', 'trn_medicine_purchase_invoices.pharmacy_id', '=', 'mst_pharmacies.id')
                 ->join('mst_suppliers', 'trn_medicine_purchase_invoices.supplier_id', '=', 'mst_suppliers.supplier_id')
                  ->select('trn_medicine_purchase_invoices.*', 'mst_pharmacies.*','mst_suppliers.*');

            }

    
            if ($request->has('invoice_date') && $request->invoice_date != "") {
                $query->whereRaw('trn_medicine_purchase_invoices.invoice_date = ?', [$request->invoice_date]);
            }
            
            if ($request->has('due_date') && $request->due_date != "") {
                $query->whereRaw('trn_medicine_purchase_invoices.due_date = ?', [$request->due_date]);
            }
            
            if ($request->has('pharmacy_id') && $request->pharmacy_id != "") {
                $query->where('trn_medicine_purchase_invoices.pharmacy_id', $request->pharmacy_id);
            }
            
            if ($request->has('supplier_id') && $request->supplier_id != "") {
            $query->where('trn_medicine_purchase_invoices.supplier_id', $request->supplier_id);
            }
            
    
            $purchaseInvoice = $query->orderBy('trn_medicine_purchase_invoices.created_at', 'desc')->get();
        
            return view('medicine_purchase_invoice.index', compact('pageTitle', 'purchaseInvoice', 'pharmacies','suppliers'));
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

             $import = new ExcelImport();
            $excelData = Excel::toCollection($import, $request->file('products_file'));
            //dd($excelData);
            $processedData = $excelData->filter(function ($row, $index) {
        if ($index === 0) {
            return true;
        }
        return collect($row)->filter(function ($cell) {
            $value = trim((string) $cell);
            return !empty($value);
        })->isNotEmpty();
    })->map(function ($row) {

        //$row['Mdd'] = isset($row['Mdd']) ? Carbon::createFromFormat('d-m-Y', $row['Mdd'])->format('Y-m-d') : null;
        //$row['Expd'] = isset($row['Expd']) ? Carbon::createFromFormat('d-m-Y', $row['Expd'])->format('Y-m-d') : null;
        
        return $row;
    })->toArray();
          
            foreach ($processedData[0] as &$data) {
                if (isset($data['mdd'])) {
                    $data['mdd'] = date('Y-m-d',strtotime($data['mdd']));//Carbon::createFromTimestamp($data['mdd'])->format('d-m-Y');
                }
                if (isset($data['expd'])) {
                    $data['expd'] =date('Y-m-d',strtotime($data['expd']));// Carbon::createFromTimestamp($data['expd'])->format('d-m-Y');
                }
                $medicine = Mst_Medicine::where('medicine_code', $data['medicine_code'])->first();
                if ($medicine) {
                    $data['medicine_id'] = $medicine->id;
                } else {
                    $data['medicine_id'] = null;
                }
            }
            unset($data);
            //dd($processedData);

            return view('medicine_purchase_invoice.create', compact('processedData','excelData', 'pageTitle', 'suppliers', 'branch', 'medicines', 'paymentType','products','pharmacies'));
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
    
    
    public function checkInvoice(Request $request)
    {
        $supplierId = $request->supplier_id;
        $invoiceNo = $request->invoice_no;
        $currentYear = date('Y');
        $existingInvoice = Trn_Medicine_Purchase_Invoice::where('supplier_id', $supplierId)
            ->where('purchase_invoice_no', $invoiceNo)
            ->whereYear('invoice_date', $currentYear)
            ->exists();
    
        return response()->json(['exists' => $existingInvoice]);
    }

    public function store(Request $request)
    { 


        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required',
            'invoice_no' => 'required',
            'invoice_date' => 'required',
            'pharmacy_id' => 'required',
            'product_id' => 'required',
            'due_date' => 'required',
            'sub_total' => 'required',
            'total_amount' => 'required',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();

        }
           
            DB::beginTransaction();
            $branchId = Mst_Pharmacy::where('id', $request->pharmacy_id)->value('branch');
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
            $invoice->branch_id = $branchId;
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
            $invoice->is_paid = $request->total_amount == $request->paid_amount ? 1 : 0;
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
            $salesRate = $request->input('sales_rate');

       
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
            array_shift($salesRate);
    
            // $unit_ids = Mst_Unit::whereIn('unit_name', $unitIds)->get();
            $unit_ids = Mst_Unit::whereIn('unit_name', $unitIds)->pluck('id', 'unit_name');
            
            foreach ($productIds as $key => $productId) {
                
        
                $detail = new Trn_Medicine_Purchase_Invoice_Detail();
                $detail->invoice_id = $invoiceId;
                $detail->product_id = $productId;
                $detail->medicine_code = $medicineCodes[$key];
                $unitId = $unit_ids[$unitIds[$key]] ?? null;
                $detail->unit_id = $unitId;
                $detail->quantity = $quantities[$key];
                $detail->free_quantity = $freeQuantities[$key];
                $detail->batch_no = $batchNos[$key];
                $detail->mfd = $mfdDates[$key];
                $detail->expd = $expdDates[$key];
                $detail->sales_rate = $salesRate[$key];
                $detail->rate = $rates[$key];
                $detail->tax_amount = $taxes[$key];
                $detail->discount = $discounts[$key];
                $detail->amount = $amounts[$key];
                $detail->created_by = Auth::id();
                $detail->updated_by = Auth::id(); 
                $detail->save();
                $stock = Trn_Medicine_Stock::where('medicine_id', $productId)
                            ->where('expd', $expdDates[$key])
                            ->where('mfd', $mfdDates[$key])
                            ->where('batch_no',  $batchNos[$key])
                            ->where('pharmacy_id',$request->pharmacy_id)
                            ->first();
                //dd($stock);
                
                        

                if (!$stock) {
                $current_stock = $quantities[$key] +  $freeQuantities[$key];
                $stock = new Trn_Medicine_Stock();
                $stock->medicine_id = $productId;
                $stock->pharmacy_id =  $request->pharmacy_id;
                $stock->branch_id =  $branchId;
                $stock->batch_no = $batchNos[$key];
                $stock->mfd = $mfdDates[$key];
                $stock->expd = $expdDates[$key];
                $stock->sale_rate = $salesRate[$key];
                $stock->purchase_rate = $rates[$key];
                $unitId = $unit_ids[$unitIds[$key]] ?? null;
                $stock->old_stock =  0;
                $stock->invoive_id =$invoice->purchase_invoice_id; 
                $stock->current_stock =  $current_stock;
                $stock->stock_code = 'STK' . uniqid(mt_rand(), true);
                $stock->save();
                $stock->stock_code = 'STK' . $invoice->purchase_invoice_id;
                $stock->save();
                
                }
            else {
                $existingStock = Trn_Medicine_Stock::where('medicine_id', $productId)
                            ->where('expd', $expdDates[$key])
                            ->where('mfd', $mfdDates[$key])
                            ->where('batch_no',$batchNos[$key])
                            ->where('pharmacy_id',$request->pharmacy_id)
                            ->first();
                            //dd($existingStock);
                
                    
                if ($existingStock) {
                    $current_stock = $quantities[$key] +  $freeQuantities[$key] + $existingStock->current_stock;
                    $existingStock->current_stock = $current_stock;
                    $existingStock->update();
                }
            }
            
                $stockData = Trn_Medicine_Stock::where('medicine_id', $productId)
                    ->where('expd', $request->input('expd')[$key])
                    ->where('batch_no', $request->input('batch_no')[$key])
                    ->where('purchase_rate', $request->input('rate')[$key])
                    ->first();

                $remarks = "Added " . $quantities[$key] . " Quantities in Invoice #" . $invoiceId;

                $log = new Trn_Stock_Transaction();
                $log->medicine_id = $productId;
                $log->invoice_id =  $invoiceId;

                if (!$stockData) {
                    $log->old_stock = 0;
                    $newStock = $quantities[$key] + $freeQuantities[$key];
                } else {
                    $log->old_stock = $stockData->old_stock;
                    $newStock = $stockData->current_stock + $quantities[$key] + $freeQuantities[$key];
                }

                $log->new_stock = $newStock;
                $log->remark = $remarks;
                $log->updated_by = Auth::id();
                $log->updated_on = now();
                $log->save();

            }
            $totalRate = $request->paid_amount;
            $totalAmount = $request->total_amount;
            $subTotal = $request->sub_total;
            $branchId = Mst_Pharmacy::where('id', $request->pharmacy_id)->value('branch');
            
        //Accounts Payable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PINV' . $invoiceId,
            'account_ledger_id' => 27,
            'entity_id' => $request->supplier_id,
            'debit' => 0,
            'credit' => $totalAmount,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $invoiceId,
            'narration' => 'Purchase Invoice Payment'
        ]);
        // Inventory Asset
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PINV' . $invoiceId,
            'account_ledger_id' => 3,
            'entity_id' => 0,
            'debit' => $subTotal,
            'credit' => 0,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $invoiceId,
            'narration' => 'Purchase Invoice Payment'
        ]);

        //Accounts Payable   
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PINV' . $invoiceId,
            'account_ledger_id' => 27,
            'entity_id' =>$request->supplier_id,
            'debit' =>  $totalAmount,
            'credit' => 0,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $invoiceId,
            'narration' => 'Purchase Invoice Payment'
        ]);

        //Cash or Bank Account    
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PINV' . $invoiceId,
            'account_ledger_id' => 4,
            'entity_id' =>0,
            'debit' =>  0,
            'credit' => $totalAmount,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $invoiceId,
            'narration' => 'Purchase Invoice Payment'
         ]);
         
        if ($request->isigst == '1')
        {
            
        //CGST Input Tax
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PINV' . $invoiceId,
            'account_ledger_id' =>10,
            'entity_id' => 0,
            'debit' =>  0,
            'credit' => 0,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $invoiceId,
            'narration' => 'Purchase Invoice Payment'
        ]);
        
        //SGST Input Tax
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PINV' . $invoiceId,
            'account_ledger_id' =>11,
            'entity_id' => 0,
            'debit' =>  0,
            'credit' => 0,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $invoiceId,
            'narration' => 'Purchase Invoice Payment'
        ]);
        
        //IGST Input Tax
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PINV' . $invoiceId,
            'account_ledger_id' =>12,
            'entity_id' => 0,
            'debit' =>  $request->total_tax,
            'credit' => 0,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $invoiceId,
            'narration' => 'Purchase Invoice Payment'
        ]);
        
        }
        else
        {
        //CGST Input Tax

        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PINV' . $invoiceId,
            'account_ledger_id' =>10,
            'entity_id' => 0,
            'debit' =>  $request->total_tax/2,
            'credit' => 0,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $invoiceId,
            'narration' => 'Purchase Invoice Payment'
        ]);
        //SGST Input Tax

        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'PINV' . $invoiceId,
            'account_ledger_id' =>11,
            'entity_id' => 0,
            'debit' =>  $request->total_tax/2,
            'credit' => 0,
            'pharmacy_id' => $request->pharmacy_id,
            'branch_id' => $branchId,
            'transaction_id' => $invoiceId,
            'narration' => 'Purchase Invoice Payment'
        ]);
        }

            
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
        
        //due date calculation
        $creditPeriod = Mst_Supplier::where('supplier_id', $supplierId)->value('credit_period');
        $dueDate = now()->addDays($creditPeriod)->format('Y-m-d');
        if (!$creditPeriod) {
            $dueDate = now()->format('Y-m-d');
        }
        $total_paid=Trn_Medicine_Purchase_Invoice::where(['supplier_id'=>$supplierId,'is_paid'=>0])->whereNotNULL('paid_amount')->whereNotNULL('total_amount')->sum('paid_amount');
        $total_invoiced_amount=Trn_Medicine_Purchase_Invoice::where(['supplier_id'=>$supplierId,'is_paid'=>0])->whereNotNULL('paid_amount')->whereNotNULL('total_amount')->sum('total_amount');
        $utilizedAmount=$total_invoiced_amount-$total_paid;
        $remaining_credit_amount=$creditLimit-$utilizedAmount;
        if($remaining_credit_amount<0)
        {
            $remaining_credit_amount=0;
        }

        // Respond with JSON data
        return response()->json([
            'creditLimit' => $creditLimit,
            'currentCredit' => $currentCredit,
            'dueDate' => $dueDate,
            'utilizedCredit'=>$utilizedAmount,
            'remainingCredit'=>$remaining_credit_amount
        ]);
    }
    public function getMedicineDetails($productId)
    {
        $medicineDetails = Mst_Medicine::where('mst_medicines.id', $productId)
            ->leftJoin('mst_taxes', 'mst_medicines.tax_id', '=', 'mst_taxes.id')
            ->join('mst_units', 'mst_medicines.unit_id', '=', 'mst_units.id')
            ->select(
                'mst_medicines.*',
                'mst_taxes.tax_rate',
                'mst_units.id as unit_id',
                'mst_units.unit_name'
            )
            ->first();
            
        if ($medicineDetails !== null) {
            return response()->json([
                'medicine_code' => $medicineDetails->medicine_code,
                'unit_name' => $medicineDetails->unit_name,
                'unit_id' => $medicineDetails->unit_id,
                'unit_price' => $medicineDetails->unit_price,
                'batch_no' => $medicineDetails->batch_no,
                'tax_rate' => $medicineDetails->tax_rate,
            ]);
        } else {
            return response()->json([
                'error' => 'Medicine details not found.',
            ], 404); 
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
    public function view($id)
    {
        $pageTitle='View Purchase Invoice';
        $purchase_invoice=Trn_Medicine_Purchase_Invoice::with('purchaseInvoiceDetails','Supplier','Pharmacy','paymentMode')->where('purchase_invoice_id', $id)->first();
         //dd($purchase_invoice);
        return view('medicine_purchase_invoice.view', compact('pageTitle','purchase_invoice'));
        
    }

}
