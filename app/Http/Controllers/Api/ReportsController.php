<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mst_Branch;
use App\Models\Mst_Supplier;
use App\Models\Mst_Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
     public function medicineSalesReport(Request $request)
    {
        $data = array();
        $pageNumber = $request->input('page', 1);
        $data['status'] = 1;
        $data['message']='Data fetched';
        $salesQuery=DB::table('trn_medicine_sale_invoices')
               ->leftjoin('trn_medicine_sales_orders','trn_medicine_sales_orders.sales_order_id','=','trn_medicine_sale_invoices.sales_order_id')
               ->leftjoin('mst_patients','mst_patients.id','=','trn_medicine_sales_orders.patient_id')
               ->leftjoin('mst_branches','mst_branches.branch_id','=','trn_medicine_sales_orders.branch_id')
               ->leftjoin('trn_consultation_bookings','trn_consultation_bookings.id','=','trn_medicine_sales_orders.booking_id')
               ->select(
               'trn_medicine_sales_orders.sales_order_no',
               'trn_medicine_sales_orders.sales_order_date',
               'trn_medicine_sales_orders.patient_id',
               'mst_patients.patient_name',
               'mst_patients.patient_mobile',
               'trn_medicine_sales_orders.branch_id',
               'mst_branches.branch_name',
               'trn_consultation_bookings.booking_type_id'
               );
        if (request()->has('branch_id')) {
            $salesQuery->where('trn_medicine_sales_orders.branch_id','=',request()->has('branch_id'));
        }

        if (request()->has('order_no')) {
            $salesQuery->where('trn_medicine_sales_orders.sales_order_no', 'like', '%' . request()->has('order_no') . '%');
        }
              
        if (request()->has('patient_name')) {
            $salesQuery->where('mst_patients.patient_name', 'like', '%' . request('patient_name') . '%');
        }
       
            
        if (request()->has('patient_mobile')) {
            $salesQuery->where('mst_patients.patient_mobile', 'like', '%' . request('patient_mobile') . '%');
        }
        if (request()->has('patient_booking_type')) {
            $salesQuery->where('trn_consultation_bookings.booking_type_id', '=',  request('patient_booking_type'));
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
    
            $salesQuery->whereBetween('trn_medicine_sales_orders.sales_order_date', [$start_date, $end_date]);
        }
        $total_records = $salesQuery->count();
        if($pageNumber=='all')
        {
            $saleReportRecords = $salesQuery->get();
            $data['saleReport']=$saleReportRecords;
        }
        else
        {
            $saleReportRecords = $salesQuery->paginate(30, ['*'], 'page', $pageNumber) ;
            $data['saleReport']=$saleReportRecords->items();
        }
        //$saleReportRecords = $salesQuery->paginate(30, ['*'], 'page', $pageNumber) ;
        //$data['saleReport']=$saleReportRecords->items();
        $data['total']=$total_records;
        return response($data);
       
    }
       public function medicinePurchasesReport(Request $request)
    {
        $data = array();
        $pageNumber = $request->input('page', 1);
        $data['status'] = 1;
        $data['message']='Data fetched';
        $purchasesQuery=DB::table('trn_medicine_purchase_invoices')
               ->leftjoin('mst_suppliers','mst_suppliers.supplier_id','=','trn_medicine_purchase_invoices.supplier_id')
               ->leftjoin('mst_master_values','mst_master_values.id','=','trn_medicine_purchase_invoices.payment_mode')
               ->leftjoin('mst_branches','mst_branches.branch_id','=','trn_medicine_purchase_invoices.branch_id')
               ->select(
               'trn_medicine_purchase_invoices.purchase_invoice_no',
               'trn_medicine_purchase_invoices.invoice_date',
               //'trn_medicine_purchase_invoices.due_date',
               'trn_medicine_purchase_invoices.sub_total',
               DB::raw('trn_medicine_purchase_invoices.total_tax / 2 as cgst'), // Calculate cgst
               DB::raw('trn_medicine_purchase_invoices.total_tax / 2 as sgst'), // Calculate sgst
               'trn_medicine_purchase_invoices.total_amount',
               'trn_medicine_purchase_invoices.payment_mode',
               'mst_master_values.master_value as payment_mode_name',
               'mst_suppliers.supplier_id',
               'mst_suppliers.supplier_name',
               //'mst_branches.branch_id',
               //'mst_branches.branch_name',
              
               );
      /*  if (request()->has('branch_id')) {
            $purchasesQuery->where('trn_medicine_purchase_invoices.branch_id','=',request('branch_id'));
        }*/

       
              
        if (request()->has('supplier_name')) {
            $purchasesQuery->where('mst_suppliers.supplier_name', 'like', '%' . request('patient_name') . '%');
        }

                
        if (request()->has('supplier_id')) {
            $purchasesQuery->where('mst_suppliers.supplier_id','=',request('supplier_id'));
        }

                 
        if (request()->has('payment_mode')) {
            $purchasesQuery->where('trn_medicine_purchase_invoices.payment_mode','=',request('payment_mode'));
        }
       
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
    
            $purchasesQuery->whereBetween('trn_medicine_purchase_invoices.invoice_date', [$start_date, $end_date]);
        }
        $total_records = $purchasesQuery->count();
        if($pageNumber=='all')
        {
            $purchaseReportRecords = $purchasesQuery->get();
            $data['purchaseReport']=$purchaseReportRecords;
        }
        else
        {
            $purchaseReportRecords = $purchasesQuery->paginate(30, ['*'], 'page', $pageNumber) ;
            $data['purchaseReport']=$purchaseReportRecords->items();
        }
       
        foreach($data['purchaseReport'] as $report)
        {
            $report->status="Paid";
        }
       
        $data['total']=$total_records;
        return response($data);
       
    }
    public function medicinePurchasesReturnReport(Request $request)
    {
        $data = array();
        $pageNumber = $request->input('page', 1);
        $data['status'] = 1;
        $data['message']='Data fetched';
        $purchasesQuery=DB::table('trn_medicine_purchase_return_details')
               ->leftjoin('trn_medicine_purchase_return','trn_medicine_purchase_return.purchase_return_id','=','trn_medicine_purchase_return_details.purchase_return_id')
               ->leftjoin('trn_medicine_purchase_invoices','trn_medicine_purchase_invoices.purchase_invoice_id','=','trn_medicine_purchase_return.purchase_invoice_id')
               ->leftjoin('mst_suppliers','mst_suppliers.supplier_id','=','trn_medicine_purchase_return.supplier_id')
               ->leftjoin('mst_master_values','mst_master_values.id','=','trn_medicine_purchase_invoices.payment_mode')
               ->leftjoin('mst_branches','mst_branches.branch_id','=','trn_medicine_purchase_return.branch_id')
               ->leftJoin('mst_medicines', 'trn_medicine_purchase_return_details.product_id', '=', 'mst_medicines.id')
               ->select(
               'trn_medicine_purchase_invoices.purchase_invoice_id',
               'trn_medicine_purchase_return_details.product_id',
               'trn_medicine_purchase_invoices.purchase_invoice_no',
               'trn_medicine_purchase_return_details.free_quantity as return_quantity',
               'trn_medicine_purchase_return_details.rate',
               'trn_medicine_purchase_return.purchase_return_no',
               'trn_medicine_purchase_return.return_date',
              'trn_medicine_purchase_invoices.purchase_invoice_no',
               //'trn_medicine_purchase_invoices.invoice_date',
               //'trn_medicine_purchase_invoices.due_date',
               'trn_medicine_purchase_invoices.payment_mode',
               'mst_master_values.master_value as payment_mode_name',
               //'trn_medicine_purchase_invoice_details.free_quantity as total_quantity',
               'mst_suppliers.supplier_id',
               'mst_suppliers.supplier_name',
               'mst_branches.branch_id',
               'mst_branches.branch_name',
               'mst_medicines.medicine_name'
               
               );
        if (request()->has('branch_id')) {
            $purchasesQuery->where('trn_medicine_purchase_invoices.branch_id','=',request('branch_id'));
        }           
        if (request()->has('supplier_name')) {
            $purchasesQuery->where('mst_suppliers.supplier_name', 'like', '%' . request('supplier__name') . '%');
        }
        if (request()->has('medicine_name')) {
            $purchasesQuery->where('mst_medicines.medicine_name', 'like', '%' . request('medicine_name') . '%');
        }

                
        if (request()->has('supplier_id')) {
            $purchasesQuery->where('mst_suppliers.supplier_id','=',request('supplier_id'));
        }

        if (request()->has('payment_mode')) {
            $purchasesQuery->where('trn_medicine_purchase_invoices.payment_mode','=',request('payment_mode'));
        }
       
            
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $purchasesQuery->whereBetween('trn_medicine_purchase_return.return_date', [$start_date, $end_date]);
        }
        $total_records = $purchasesQuery->count();
        if($pageNumber=='all')
        {
            $purchaseReportRecords = $purchasesQuery->whereNotNull('trn_medicine_purchase_invoices.purchase_invoice_no')->get();
            $data['purchaseReturnReport']=$purchaseReportRecords;
        }
        else
        {
            $purchaseReportRecords = $purchasesQuery->whereNotNull('trn_medicine_purchase_invoices.purchase_invoice_no')->paginate(30, ['*'], 'page', $pageNumber) ;
            $data['purchaseReturnReport']=$purchaseReportRecords->items();
        }
            
        foreach($data['purchaseReturnReport'] as $pReport)
        {
            $inv_detail=DB::table('trn_medicine_purchase_invoice_details')->where('invoice_id',$pReport->purchase_invoice_id)->where('product_id',$pReport->product_id)->first();
            $pReport->total_quantity=$inv_detail->free_quantity;
            $pReport->sub_total=number_format($pReport->rate*(90/100),2);
            $pReport->total=$pReport->rate;
            $pReport->cgst=number_format($pReport->rate/5,2);
            $pReport->sgst=number_format($pReport->rate/5,2);

        }
        
        $data['total']=$total_records;
        return response($data);
       
    }
     public function stockTransferReport(Request $request)
    {
        $data=array();
        $pageNumber=$request->input('page',1);
        $data['status']=1;
        $data['message']="Data Fetched";
        $stock_transfers=DB::table('trn_medicine_stock_transfers')
                         ->leftJoin('mst_medicines','mst_medicines.id','=','trn_medicine_stock_transfers.medicine_id')
                         ->select('trn_medicine_stock_transfers.transfer_date','trn_medicine_stock_transfers.medicine_id','mst_medicines.medicine_name','trn_medicine_stock_transfers.batch_number','trn_medicine_stock_transfers.transfered_quantity','trn_medicine_stock_transfers.branch_from_id','trn_medicine_stock_transfers.branch_to_id');
        if (request()->has('medicine_name')) {
            $stock_transfers->where('mst_medicines.medicine_name', 'like', '%' . request('medicine_name') . '%');
        }
        if (request()->has('branch_from_id')) {
            $stock_transfers->where('trn_medicine_stock_transfers.branch_from_id','=',request('branch_from_id'));
        }
        if (request()->has('branch_to_id')) {
            $stock_transfers->where('trn_medicine_stock_transfers.branch_to_id','=',request('branch_to_id'));
        }
        if (request()->has('batch_number')) {
            $stock_transfers->where('trn_medicine_stock_transfers.batch_number','=',request('batch_no'));
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $stock_transfers->whereBetween('trn_medicine_stock_transfers.transfer_date', [$start_date, $end_date]);
        }
        $total_records = $stock_transfers->count();
        if($pageNumber=='all')
        {
            $stockTransferRecords = $stock_transfers->get();
            $data['stockTransferReport']=$stockTransferRecords;
        }
        else
        {
            $stockTransferRecords = $stock_transfers->paginate(30, ['*'], 'page', $pageNumber) ;
            $data['stockTransferReport']=$stockTransferRecords->items();
        }

        foreach($stockTransferRecords as $sRec)
        {
            $fromBranch=$this->getBranch($sRec->branch_from_id);
            $toBranch=$this->getBranch($sRec->branch_to_id);
            if($fromBranch)
            {
                $sRec->from_branch_name=$fromBranch->branch_name;
            }
            else
            {
                $sRec->from_branch_name='No Branch';
            }
            if($toBranch)
            {
                $sRec->to_branch_name=$toBranch->branch_name;
            }
            else
            {
                $sRec->to_branch_name='No Branch';
            }
            $fromStock=$this->getStock($sRec->medicine_id,$sRec->batch_number,$sRec->branch_from_id);
            $toStock=$this->getStock($sRec->medicine_id,$sRec->batch_number,$sRec->branch_to_id);
            if($fromStock)
            {
                $sRec->from_branch_current_stock=$fromStock->current_stock;
            }
            else
            {
                $sRec->from_branch_current_stock='0.00';
            }
            if($toStock)
            {
                $sRec->to_branch_current_stock=$toStock->current_stock;
            }
            else
            {
                $sRec->to_branch_current_stock='0.00';
            }


        }
        
        $data['total']=$total_records;
        return response($data);
                         
    }
    public function getBranch($branch_id)
    {
        $branch=Mst_Branch::where('branch_id',$branch_id)->first();
        return $branch;
    }
    public function getStock($medicine_id,$batch,$branch)
    {
        $stock=DB::table('trn_medicine_stock')->where('medicine_id',$medicine_id)->where('branch_id',$branch)->where('batch_no',$batch)->first();
        return $stock;
    }
     public function currentStockReport(Request $request)
    {
        $data=array();
        $pageNumber=$request->input('page',1);
        $data['status']=1;
        $data['message']="Data Fetched";
        $currentStockQuery=DB::table('trn_medicine_stock')
        ->leftjoin('mst_branches','mst_branches.branch_id','=','trn_medicine_stock.branch_id')
        ->leftjoin('mst_medicines','mst_medicines.id','=','trn_medicine_stock.medicine_id')
        ->select('trn_medicine_stock.*','mst_branches.branch_id','mst_branches.branch_name','mst_medicines.medicine_name');
        if (request()->has('medicine_name')) {
            $currentStockQuery->where('mst_medicines.medicine_name', 'like', '%' . request('medicine_name') . '%');
        }
        if (request()->has('branch_id')) {
            $currentStockQuery->where('mst_branches.branch_id','=',request('branch_id'));
        }
        if (request()->has('batch_no')) {
            $currentStockQuery->where('trn_medicine_stock.batch_no','=',request('batch_no'));
        }
        $total_records = $currentStockQuery->count();
        if($pageNumber=='all')
        {
            $currentStockReportRecords = $currentStockQuery->get();
            $data['currentStockReport']=$currentStockReportRecords;
        }
        else
        {
            $currentStockReportRecords = $currentStockQuery->paginate(30, ['*'], 'page', $pageNumber) ;
            $data['currentStockReport']=$currentStockReportRecords->items();
        }
        $data['total']=$total_records;
       
        return response($data);
    }
     public function paymentMadeReport(Request $request)
    {
        $data=array();
        $pageNumber=$request->input('page',1);
        $data['status']=1;
        $data['message']="Data Fetched";
        $paymentMadeQuery=DB::table('trn_payment_voucher')
        ->leftjoin('trn_medicine_purchase_invoices','trn_medicine_purchase_invoices.purchase_invoice_id','=','trn_payment_voucher.purchase_invoice_id')
        ->leftjoin('mst_suppliers','mst_suppliers.supplier_id','=','trn_payment_voucher.supplier_id')
        ->select('trn_payment_voucher.payment_voucher_no','trn_medicine_purchase_invoices.purchase_invoice_no','trn_payment_voucher.supplier_id','mst_suppliers.supplier_name','trn_payment_voucher.payment_date','trn_medicine_purchase_invoices.total_amount as invoice_amount','trn_payment_voucher.amount_paid');
        if (request()->has('voucher_number')) {
            $paymentMadeQuery->where('trn_payment_voucher.payment_voucher_no','=',request('voucher_number'));
        }
        if (request()->has('invoice_number')) {
            $paymentMadeQuery->where('trn_medicine_purchase_invoices.purchase_invoice_no','=',request('invoice_number'));
        }
        if (request()->has('supplier_id')) {
            $paymentMadeQuery->where('trn_payment_voucher.supplier_id','=',request('supplier_id'));
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
    
            $paymentMadeQuery->whereBetween('trn_payment_voucher.payment_date', [$start_date, $end_date]);
        }
        $total_records = $paymentMadeQuery->count();
        if($pageNumber=='all')
        {
            $paymentMadeReportRecords = $paymentMadeQuery->get();
            $data['paymentMadeReport']=$paymentMadeReportRecords;
        }
        else
        {
            $paymentMadeReportRecords = $paymentMadeQuery->paginate(30, ['*'], 'page', $pageNumber) ;
            $data['paymentMadeReport']=$paymentMadeReportRecords->items();
        }
        foreach($data['paymentMadeReport'] as $pMadeReport)
        {
            $pMadeReport->due_amount=$pMadeReport->invoice_amount-$pMadeReport->amount_paid;
            //$pMadeReport->due_amount=(string)$pMadeReport->due_amount;
        }
        $data['total']=$total_records;
       
        return response($data);

    }
     public function paymentReceivedReport(Request $request)
    {
        $data=array();
        $pageNumber=$request->input('page',1);
        $data['status']=1;
        $data['message']="Data Fetched";
        $paymentReceivedQuery=DB::table('trn_sale_receipt')
        ->leftjoin('trn_medicine_sale_invoices','trn_medicine_sale_invoices.sales_invoice_id','=','trn_sale_receipt.sale_invoice_id')
        ->leftjoin('mst_patients','mst_patients.id','=','trn_sale_receipt.customer_id')
        ->select('trn_sale_receipt.invoice_receipt_no','trn_medicine_sale_invoices.sales_invoice_no','trn_sale_receipt.reference_code','trn_sale_receipt.payment_date as receipt_date','mst_patients.patient_name','trn_sale_receipt.payment_date','trn_medicine_sale_invoices.total_amount as invoice_amount','trn_sale_receipt.amount_received');
        if (request()->has('receipt_number')) {
            $paymentReceivedQuery->where('trn_sale_receipt.invoice_receipt_no','=',request('receipt_number'));
        }
        if (request()->has('invoice_number')) {
            $paymentReceivedQuery->where('trn_medicine_sale_invoices.sales_invoice_no','=',request('invoice_number'));
        }
        if (request()->has('patient_name')) {
            $paymentReceivedQuery->where('mst_patients.patient_name', 'like', '%' . request('patient_name') . '%');
        }
       /* if (request()->has('supplier_id')) {
            $paymentMadeQuery->where('trn_payment_voucher.supplier_id','=',request('supplier_id'));
        }*/
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
    
            $paymentReceivedQuery->whereBetween('trn_sale_receipt.payment_date', [$start_date, $end_date]);
        }
        $total_records = $paymentReceivedQuery->count();
        if($pageNumber=='all')
        {
            $paymentReceivedReportRecords = $paymentReceivedQuery->get();
            $data['paymentReceivedReport']=$paymentReceivedReportRecords;
        }
        else
        {
            $paymentReceivedReportRecords = $paymentReceivedQuery->paginate(30, ['*'], 'page', $pageNumber) ;
            $data['paymentReceivedReport']=$paymentReceivedReportRecords->items();
        }
        foreach($data['paymentReceivedReport'] as $pRecReport)
        {
            $pRecReport->due_amount=$pRecReport->invoice_amount-$pRecReport->amount_received;
            //$pRecReport->due_amount=(string)$pRecReport->due_amount;
        }
        $data['total']=$total_records;
       
        return response($data);

    }
     public function payableReport(Request $request)
    {
        $data = array();
        $data['status'] = 1;
        $data['message'] = "Data Fetched";
        $pageNumber=$request->input('page',1);
    
        // Query to retrieve payable information
        $payablesQuery = DB::table('trn_medicine_purchase_invoices')
            ->leftjoin('trn_payment_voucher', 'trn_payment_voucher.purchase_invoice_id', '=', 'trn_medicine_purchase_invoices.purchase_invoice_id')
            ->leftjoin('mst_suppliers','mst_suppliers.supplier_id','=','trn_medicine_purchase_invoices.supplier_id')
            ->select(
                'trn_medicine_purchase_invoices.purchase_invoice_no',
                'trn_medicine_purchase_invoices.supplier_id',
                'trn_medicine_purchase_invoices.invoice_date',
                DB::raw('COALESCE(SUM(trn_medicine_purchase_invoices.total_amount), 0) as total_payable_amount'),
                DB::raw('COALESCE(SUM(trn_payment_voucher.amount_paid), 0) as total_paid')
            )
            ->groupBy(
                'trn_medicine_purchase_invoices.purchase_invoice_no',
                'trn_medicine_purchase_invoices.supplier_id',
                'trn_medicine_purchase_invoices.invoice_date',
            )
            ->havingRaw('total_payable_amount - total_paid > 0');
            //->whereColumn('trn_medicine_purchase_invoices.total_invoice_amount', '>', 'trn_payment_voucher.total_paid');
            if (request()->has('invoice_number')) {
                $payablesQuery->where('trn_medicine_purchase_invoices.purchase_invoice_no','=',request('invoice_number'));
            }
            if (request()->has('supplier_id')) {
                $payablesQuery->where('trn_medicine_purchase_invoices.supplier_id','=',request('supplier_id'));
            }
            if ($request->has('start_date') && $request->has('end_date')) {
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');
        
                $payablesQuery->whereBetween('trn_medicine_purchase_invoices.invoice_date', [$start_date, $end_date]);
            }
        $total_records = $payablesQuery->count();
        $payableReportRecords = $payablesQuery->get();
        if($pageNumber=='all')
        {
            $payableReportRecords = $payablesQuery->get();
            $data['payableReport'] = $payableReportRecords;
        }
        else
        {
            $payableReportRecords = $payablesQuery->paginate(30, ['*'], 'page', $pageNumber) ;
            $data['payableReport']=$payableReportRecords->items();
        }
    
        foreach ($payableReportRecords as $payable) {
            $supplier=Mst_Supplier::where('supplier_id',$payable->supplier_id)->first();
            if($supplier)
            {
                $payable->supplier_name=$supplier->supplier_name;
            }
             $payable->due_payable_amount = number_format(($payable->total_payable_amount - $payable->total_paid),2);
              $payable->total_payable_amount=number_format($payable->total_payable_amount,2);
        }
    
        $data['total']=$total_records;
        return response($data);
    }
    public function receivableReport(Request $request)
    {
        $data=array();
        $pageNumber=$request->input('page',1);
        $data['status']=1;
        $data['message']="Data Fetched";
        $receivablesQuery= DB::table('trn_medicine_sale_invoices')
        ->leftjoin('trn_sale_receipt','trn_medicine_sale_invoices.sales_invoice_id','=','trn_sale_receipt.sale_invoice_id')
        ->leftjoin('mst_patients','mst_patients.id','=','trn_medicine_sale_invoices.patient_id')
        ->select(
            'trn_medicine_sale_invoices.sales_invoice_no',
            'trn_medicine_sale_invoices.patient_id',
            'trn_medicine_sale_invoices.invoice_date',
            DB::raw('COALESCE(SUM(trn_medicine_sale_invoices.total_amount), 0) as total_receivable_amount'),
            DB::raw('COALESCE(SUM(trn_sale_receipt.amount_received), 0) as total_received')
        )
        ->groupBy(
            'trn_medicine_sale_invoices.sales_invoice_no',
            'trn_medicine_sale_invoices.patient_id',
            'trn_medicine_sale_invoices.invoice_date',
        )
        ->havingRaw('total_receivable_amount - total_received > 0');
        if (request()->has('invoice_number')) {
            $receivablesQuery->where('trn_medicine_sale_invoices.sales_invoice_no','=',request('invoice_number'));
        }
        if (request()->has('patient_name')) {
            $receivablesQuery->where('mst_patients.patient_name', 'like', '%' . request('patient_name') . '%');
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');

            $receivablesQuery->whereBetween('trn_medicine_sale_invoices.invoice_date', [$start_date, $end_date]);
        }
        $total_records = $receivablesQuery->count();
        if($pageNumber=='all')
        {
            $recReportRecords = $receivablesQuery->get();
            $data['receivablesReport'] = $recReportRecords;
        }
        else
        {
            $recReportRecords = $receivablesQuery->paginate(30, ['*'], 'page', $pageNumber) ;
            $data['receivablesReport'] = $recReportRecords->items();
        }
        foreach ($recReportRecords as $receivable) {
            $patient=Mst_Patient::where('id',$receivable->patient_id)->first();
            if($patient)
            {
                $receivable->patient_name=$patient->patient_name;
            }
            else{
                $receivable->patient_name='Guest';
            }
            $receivable->due_receivable_amount = $receivable->total_receivable_amount - $receivable->total_received;
        }
        $data['total']=$total_records;
        return response($data);
    }
     public function getAccountGroups()
    {
        $data = array();
        try {
            $accounts = DB::table('mst_account_sub_head')->get(['id as account_id', 'account_sub_group_name'])->toArray();

            if ($accounts) {
                $data['status'] = 1;
                $data['message'] = "Data fetched.";
                $data['data'] = $accounts;
            } else {
                $data['status'] = 0;
                $data['message'] = "Groups not detected.";
            }
            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }


    }
    public function ledgerReport(Request $request)
    {
    $data = array();
    $pageNumber = $request->input('page', 1);
    $data['status'] = 1;
    $data['message'] = "Data Fetched";

    $ledgerReport = DB::table('mst_account_sub_head')
        ->leftjoin('mst__account__ledgers', 'mst_account_sub_head.id', '=', 'mst__account__ledgers.account_sub_group_id')
        ->leftjoin('trn_ledger_postings', 'mst__account__ledgers.id', '=', 'trn_ledger_postings.account_ledger_id')
        ->where('mst_account_sub_head.is_active', 1)
        ->select(
            'mst_account_sub_head.id',
            'mst_account_sub_head.account_sub_group_name',
            DB::raw('SUM(trn_ledger_postings.debit) as total_debit'),
            DB::raw('SUM(trn_ledger_postings.credit) as total_credit')
        )
        ->groupBy('mst_account_sub_head.id', 'mst_account_sub_head.account_sub_group_name')
        ->orderByRaw('mst_account_sub_head.account_sub_group_name');
        
   
     if ($request->has('branch_id')) {
            $ledgerReport->where('trn_ledger_postings.branch_id',request('branch_id'));
        }
    if ($request->has('account_id')) {
            $ledgerReport->where('mst_account_sub_head.id',request('account_id'));
    }
     if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');

            $ledgerReport->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date]);
        }
     
     
     if($pageNumber=='all')
        {
           $data['total']=$ledgerReport->get()->count();
            $data['ledgerReport'] = $ledgerReport->get();
        }
        else
        {
             $data['total']=$ledgerReport->get()->count();
            $ledRep = $ledgerReport->paginate(30, ['*'], 'page', $pageNumber) ;
            $data['ledgerReport'] = $ledRep->items();
        }

    foreach($data['ledgerReport'] as $lReport)
    {
        if(is_null($lReport->total_debit))
        {
            $lReport->total_debit='0.000';
        }
        if(is_null($lReport->total_credit))
        {
            $lReport->total_credit='0.000';
        }
        $lReport->balance=$lReport->total_debit-$lReport->total_credit;

    }
    
    return response()->json($data);
}
public function generateProfitAndLoss(Request $request)
{
        // Fetch relevant data from the database
        $data=array();
        $data['status']=1;
        $data['message']="Data fetched";


       
        $tradingAccountSales = DB::table('trn_ledger_postings')
            ->join('mst__account__ledgers', 'trn_ledger_postings.account_ledger_id', '=', 'mst__account__ledgers.id')
            ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
            ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
            ->whereIn('mst_account_sub_head.account_sub_group_name', ['Sales Accounts'])
            ->select('mst_account_sub_head.account_sub_group_name', DB::raw('SUM(trn_ledger_postings.transaction_amount) as total_amount'))
            ->groupBy('mst_account_sub_head.account_sub_group_name');
           
        if ($request->has('branch_id')) {
            $tradingAccountSales->where('trn_ledger_postings.branch_id',request('branch_id'));
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');

            $tradingAccountSales->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date]);
        }
        $tradingAccountSales=$tradingAccountSales->get();
        $tradingAccountSales = $tradingAccountSales->map(function ($item) {
            $item->total_amount = doubleval($item->total_amount);
            return $item;
        });
        
        

        $tradingAccountCostOfSales = DB::table('trn_ledger_postings')
            ->join('mst__account__ledgers', 'trn_ledger_postings.account_ledger_id', '=', 'mst__account__ledgers.id')
            ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
            ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
            ->whereIn('mst_account_sub_head.account_sub_group_name', ['Cost of Sales'])
            ->select('mst_account_sub_head.account_sub_group_name', DB::raw('SUM(trn_ledger_postings.transaction_amount) as total_amount'))
            ->groupBy('mst_account_sub_head.account_sub_group_name');
            if ($request->has('branch_id')) {
                $tradingAccountCostOfSales ->where('trn_ledger_postings.branch_id',request('branch_id'));
            }
            if ($request->has('start_date') && $request->has('end_date')) {
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');
    
                $tradingAccountCostOfSales ->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date]);
            }
            $tradingAccountCostOfSales=$tradingAccountCostOfSales->get();
            $tradingAccountCostOfSales = $tradingAccountCostOfSales->map(function ($item) {
                $item->total_amount = doubleval($item->total_amount);
                return $item;
            });
            

        $grossProfit = $tradingAccountSales->sum('total_amount') - $tradingAccountCostOfSales->sum('total_amount');

        $incomeStatement = DB::table('trn_ledger_postings')
            ->join('mst__account__ledgers', 'trn_ledger_postings.account_ledger_id', '=', 'mst__account__ledgers.id')
            ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
            ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
            ->whereIn('mst_account_sub_head.account_sub_group_name', ['Indirect Incomes', 'Indirect Expenses'])
            ->select('mst_account_sub_head.account_sub_group_name', DB::raw('SUM(trn_ledger_postings.transaction_amount) as total_amount'))
            ->groupBy('mst_account_sub_head.account_sub_group_name');
            if ($request->has('branch_id')) {
                $incomeStatement->where('trn_ledger_postings.branch_id',request('branch_id'));
            }
            if ($request->has('start_date') && $request->has('end_date')) {
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');
    
                $incomeStatement ->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date]);
            }
            $incomeStatement=$incomeStatement->get();
        //conversion to double
        $incomeStatement = $incomeStatement->map(function ($item) {
            $item->total_amount = doubleval($item->total_amount);
            return $item;
        });
        
        
           //return  $incomeStatement;
        $netProfit = $incomeStatement->where('account_sub_group_name', 'Indirect Incomes')->sum('total_amount') - $incomeStatement->where('account_sub_group_name', 'Indirect Expenses')->sum('total_amount');

        return response()->json([
            'status'=>1,
            'message'=>'Data fetched',
            'trading_account_sales' => $tradingAccountSales,
            'trading_account_cost_of_sales' => $tradingAccountCostOfSales,
            'gross_profit' => $grossProfit,
            'income_statement' => $incomeStatement,
            'net_profit' => $netProfit
        ]);
}
public function trialBalanceReport(Request $request)
{
    $data = array();
    $pageNumber = $request->input('page', 1);
    $data['status'] = 1;
    $data['message'] = "Data Fetched";
    $start_date = $request->input('start_date');
    $end_date = $request->input('end_date');

    $accounts = DB::table('mst_account_sub_head')
        ->join('mst__account__ledgers', 'mst_account_sub_head.id', '=', 'mst__account__ledgers.account_sub_group_id')
        ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
        ->join('trn_ledger_postings', 'mst__account__ledgers.id', '=', 'trn_ledger_postings.account_ledger_id')
        ->select(
            'sys__account__groups.account_group_name as group_name',
            'mst_account_sub_head.account_sub_group_name as account_name',
            DB::raw('SUM(trn_ledger_postings.debit) as total_debit'),
            DB::raw('SUM(trn_ledger_postings.credit) as total_credit')
        )
        ->where('mst_account_sub_head.is_active', 1)
        //->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date])
        ->groupBy('sys__account__groups.account_group_name', 'mst_account_sub_head.account_sub_group_name');
        if ($request->has('branch_id')) {
            $accounts->where('trn_ledger_postings.branch_id',request('branch_id'));
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');

            $accounts->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date]);
        }
        $accounts=$accounts->get();
       

        $trial_balance = [
            'Asset' => ['total_debit' => 0, 'total_credit' => 0, 'details' => []],
            'Income' => ['total_debit' => 0, 'total_credit' => 0, 'details' => []],
            'Expense' => ['total_debit' => 0, 'total_credit' => 0, 'details' => []],
            'Liability' => ['total_debit' => 0, 'total_credit' => 0, 'details' => []],
            'total_final_debit' => 0,
            'total_final_credit' => 0,
        ];
    
        foreach ($accounts as $account) {
            $debit = doubleval($account->total_debit);
            $credit = doubleval($account->total_credit);
    
            $balance = $debit - $credit;
    
            $group_name = $account->group_name;
    
            $trial_balance[$group_name]['total_debit'] += $debit;
            $trial_balance[$group_name]['total_credit'] += $credit;
    
            $trial_balance[$group_name]['details'][] = [
                'account_name' => $account->account_name,
                'debit' => $debit,
                'credit' => $credit,
                //'balance' => $balance
            ];
    
            $trial_balance['total_final_debit'] += $debit;
            $trial_balance['total_final_credit'] += $credit;
        }
        $data['trial_balance']=$trial_balance;
    
        return response()->json($data);
}
public function balanceSheetReport(Request $request)
{
    
    $start_date = $request->input('start_date');
    $end_date= $request->input('end_date');

    $accounts = DB::table('mst_account_sub_head')
        ->join('mst__account__ledgers', 'mst_account_sub_head.id', '=', 'mst__account__ledgers.account_sub_group_id')
        ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
        ->join('trn_ledger_postings', 'mst__account__ledgers.id', '=', 'trn_ledger_postings.account_ledger_id')
        ->select(
            'sys__account__groups.account_group_name as group_name',
            'mst_account_sub_head.account_sub_group_name as account_name',
            'mst__account__ledgers.ledger_name as ledger_name',
            DB::raw('SUM(trn_ledger_postings.debit) as total_debit'),
            DB::raw('SUM(trn_ledger_postings.credit) as total_credit')
        )
        ->where('mst_account_sub_head.is_active', 1)
       // ->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date])
        ->groupBy(
            'sys__account__groups.account_group_name',
            'mst_account_sub_head.account_sub_group_name',
            'mst__account__ledgers.ledger_name'
        );
        if ($request->has('branch_id')) {
            $accounts->where('trn_ledger_postings.branch_id',request('branch_id'));
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
    
            $accounts->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date]);
        }
       $accounts=$accounts ->get();
    //return $accounts;

    $balance_sheet = [
        'assets' => [
            'current_assets' => [
                'account_ledgers' => [],
                'total_credit'=>0,
                'total_debit'=>0,
                'total_balance' => 0,
            ],
            'other_current_assets' => [
                'account_ledgers' => [],
                'total_credit'=>0,
                'total_debit'=>0,
                'total_balance' => 0,
            ]
        ],
        'liabilities' => [
            'current_liabilities' => [
                'account_ledgers' => [],
                'total_credit'=>0,
                'total_debit'=>0,
                'total_balance' => 0,
            ],
            'other_liabilities' => [
                'account_ledgers' => [],
                'total_credit'=>0,
                'total_debit'=>0,
                'total_balance' => 0,
                
            ]
        ]
    ];

    foreach ($accounts as $account) {
        $debit = floatval($account->total_debit);
        $credit = floatval($account->total_credit);

        $balance = $debit - $credit;
        $account_ledger = [
            'ledger_name' => $account->ledger_name,
            'debit'=>$debit,
            'credit'=>$credit,
            'balance' => $balance
        ];

        if ($account->group_name === 'Asset') {
            if ($account->account_name === 'Current Assets') {
                $balance_sheet['assets']['current_assets']['account_ledgers'][] = $account_ledger;
                $balance_sheet['assets']['current_assets']['total_debit'] += $debit;
                $balance_sheet['assets']['current_assets']['total_credit'] += $credit;
                $balance_sheet['assets']['current_assets']['total_balance'] += $balance;
            } elseif ($account->account_name === 'Other Current Asset') {
                $balance_sheet['assets']['other_current_assets']['account_ledgers'][] = $account_ledger;
                $balance_sheet['assets']['current_assets']['total_debit'] += $debit;
                $balance_sheet['assets']['current_assets']['total_credit'] += $credit;
                $balance_sheet['assets']['other_current_assets']['total_balance'] += $balance;
            }
        } elseif ($account->group_name === 'Liability') {
            if ($account->account_name === 'Current Liabilities') {
                $balance_sheet['liabilities']['current_liabilities']['account_ledgers'][] = $account_ledger;
                $balance_sheet['assets']['current_assets']['total_debit'] += $debit;
                $balance_sheet['assets']['current_assets']['total_credit'] += $credit;
                $balance_sheet['liabilities']['current_liabilities']['total_balance'] += $balance;
            } else {
                $balance_sheet['liabilities']['other_liabilities']['account_ledgers'][] = $account_ledger;
                $balance_sheet['assets']['current_assets']['total_debit'] += $debit;
                $balance_sheet['assets']['current_assets']['total_credit'] += $credit;
                $balance_sheet['liabilities']['other_liabilities']['total_balance'] += $balance;
            }
        }
    }
    //Profit loss
$tradingAccountSales = DB::table('trn_ledger_postings')
    ->join('mst__account__ledgers', 'trn_ledger_postings.account_ledger_id', '=', 'mst__account__ledgers.id')
    ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
    ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
    ->whereIn('mst_account_sub_head.account_sub_group_name', ['Sales Accounts'])
    ->select('mst_account_sub_head.account_sub_group_name', DB::raw('SUM(trn_ledger_postings.transaction_amount) as total_amount'))
    ->groupBy('mst_account_sub_head.account_sub_group_name');
  
if ($request->has('branch_id')) {
    $tradingAccountSales->where('trn_ledger_postings.branch_id',request('branch_id'));
}
if ($request->has('start_date') && $request->has('end_date')) {
    $start_date = $request->input('start_date');
    $end_date = $request->input('end_date');

    $tradingAccountSales->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date]);
}
$tradingAccountSales=$tradingAccountSales->get();
$tradingAccountSales = $tradingAccountSales->map(function ($item) {
    $item->total_amount = doubleval($item->total_amount);
    return $item;
});



$tradingAccountCostOfSales = DB::table('trn_ledger_postings')
    ->join('mst__account__ledgers', 'trn_ledger_postings.account_ledger_id', '=', 'mst__account__ledgers.id')
    ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
    ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
    ->whereIn('mst_account_sub_head.account_sub_group_name', ['Cost of Sales'])
    ->select('mst_account_sub_head.account_sub_group_name', DB::raw('SUM(trn_ledger_postings.transaction_amount) as total_amount'))
    ->groupBy('mst_account_sub_head.account_sub_group_name');
    if ($request->has('branch_id')) {
        $tradingAccountCostOfSales ->where('trn_ledger_postings.branch_id',request('branch_id'));
    }
    if ($request->has('start_date') && $request->has('end_date')) {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $tradingAccountCostOfSales ->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date]);
    }
    $tradingAccountCostOfSales=$tradingAccountCostOfSales->get();
    $tradingAccountCostOfSales = $tradingAccountCostOfSales->map(function ($item) {
        $item->total_amount = doubleval($item->total_amount);
        return $item;
    });
    

$grossProfit = $tradingAccountSales->sum('total_amount') - $tradingAccountCostOfSales->sum('total_amount');

$incomeStatement = DB::table('trn_ledger_postings')
    ->join('mst__account__ledgers', 'trn_ledger_postings.account_ledger_id', '=', 'mst__account__ledgers.id')
    ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
    ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
    ->whereIn('mst_account_sub_head.account_sub_group_name', ['Indirect Incomes', 'Indirect Expenses'])
    ->select('mst_account_sub_head.account_sub_group_name', DB::raw('SUM(trn_ledger_postings.transaction_amount) as total_amount'))
    ->groupBy('mst_account_sub_head.account_sub_group_name');
    if ($request->has('branch_id')) {
        $incomeStatement->where('trn_ledger_postings.branch_id',request('branch_id'));
    }
    if ($request->has('start_date') && $request->has('end_date')) {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $incomeStatement ->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date]);
    }
    $incomeStatement=$incomeStatement->get();
//conversion to double
$incomeStatement = $incomeStatement->map(function ($item) {
    $item->total_amount = doubleval($item->total_amount);
    return $item;
});


   //return  $incomeStatement;
$netProfit = $incomeStatement->where('account_sub_group_name', 'Indirect Incomes')->sum('total_amount') - $incomeStatement->where('account_sub_group_name', 'Indirect Expenses')->sum('total_amount');

    return response()->json(['status'=>1,'message'=>'Data fetched','balance_sheet' => $balance_sheet,'profit_loss_account'=>$netProfit]);
}


    
}
