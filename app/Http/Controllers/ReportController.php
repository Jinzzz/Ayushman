<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Trn_Medicine_Sales_Invoice;
use App\Models\Trn_Medicine_Sales_Invoice_Details;
use App\Models\Mst_Pharmacy;
use App\Models\Trn_Medicine_Purchase_Invoice;
use App\Models\Trn_Medicine_Purchase_Invoice_Detail;
use App\Models\Mst_Supplier;
use App\Models\Trn_Medicine_Purchase_Return;
use App\Models\Trn_Medicine_Purchase_Return_Detail;
use App\Models\Trn_Medicine_Sales_Return;
use App\Models\Trn_Medicine_Sales_Return_Details;
use App\Models\Trn_branch_stock_transfer;
use App\Models\Trn_branch_stock_transfer_detail;
use App\Models\Trn_Medicine_Stock;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Staff;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    
    public function SalesReport(Request $request)
    {

        $pageTitle = "Sales Report";
        
        $saleQuery = Trn_Medicine_Sales_Invoice::select(
            'sales_invoice_id',
            'sales_invoice_number',
            'invoice_date',
            'pharmacy_id',
            'total_amount',
        )
        ->with('pharmacy')
        ->withCount([
            'salesInvoiceDetails as sales_invoice_details_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $saleQuery->whereIn('pharmacy_id', $mappedPharmacies);
        }
        

            if ($request->filled('pharmacy_id')) {
                $saleQuery->where('pharmacy_id', $request->input('pharmacy_id'));
            }
    
            if ($request->filled('sales_invoice_number')) {
                $saleQuery->where('sales_invoice_number', $request->input('sales_invoice_number'));
            }
            $saleQuery->where(function ($query) use ($request) {
                if ($request->filled('invoice_from_date') && $request->filled('invoice_to_date')) {
                    $query->whereBetween('invoice_date', [$request->input('invoice_from_date'), $request->input('invoice_to_date')]);
                } elseif ($request->filled('invoice_from_date')) {
                    $query->where('invoice_date', '>=', $request->input('invoice_from_date'));
                } elseif ($request->filled('invoice_to_date')) {
                    $query->where('invoice_date', '<=', $request->input('invoice_to_date'));
                } else {
                    $query->whereDate('invoice_date', Carbon::today());
                }
            });

        $sumTotalAmount = $saleQuery->sum('total_amount');
        $salesInvoices = $saleQuery->orderBy('invoice_date','DESC')->get();

        return view('reports.sales-report', [
            'pageTitle' => 'Sales Reports',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'sales' => $salesInvoices,
            'sumTotalAmount' => $sumTotalAmount
        ]);
    }

    
    public function SalaryReportDetail(Request $request, $id)
    {
        $salesQuery = Trn_Medicine_Sales_Invoice_Details::join('trn__medicine__sales__invoices', 'trn__medicine__sales__invoice__details.sales_invoice_id', '=', 'trn__medicine__sales__invoices.sales_invoice_id')
            ->join('mst_medicines', 'trn__medicine__sales__invoice__details.medicine_id', '=', 'mst_medicines.id')
            ->where('trn__medicine__sales__invoice__details.sales_invoice_id', $request->id);
    
        if ($request->filled('medicine_name')) {
            $salesQuery->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
        }
    
        $SalesDetail = $salesQuery->get();
     
        return view('reports.sales-report-detail', [
            'pageTitle' => 'Sales Report Detail',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'invoice_id' => $id,
            'sales_details' => $SalesDetail,
            'id' => $id
        ]);
    }
    
    public function PurchaseReport(Request $request)
    {
        $purchaseQuery = Trn_Medicine_Purchase_Invoice::select(
            'purchase_invoice_id',
            'purchase_invoice_no',
            'supplier_id',
            'invoice_date',
            'pharmacy_id',
            'total_amount',
            'payment_mode'
        )
        ->with(['Pharmacy', 'Supplier','paymentMode'])
        ->withCount([
            'purchaseInvoiceDetails as purchase_invoice_details_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $purchaseQuery->whereIn('pharmacy_id', $mappedPharmacies);
        }

        if ($request->filled('pharmacy_id')) {
            $purchaseQuery->where('pharmacy_id', $request->input('pharmacy_id'));
        }

        if ($request->filled('supplier_id')) {
            $purchaseQuery->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('purchase_invoice_no')) {
            $purchaseQuery->where('purchase_invoice_no', $request->input('purchase_invoice_no'));
        }
        $purchaseQuery->where(function ($query) use ($request) {
            if ($request->filled('invoice_from_date') && $request->filled('invoice_to_date')) {
                $query->whereBetween('invoice_date', [$request->input('invoice_from_date'), $request->input('invoice_to_date')]);
            } elseif ($request->filled('invoice_from_date')) {
                $query->where('invoice_date', '>=', $request->input('invoice_from_date'));
            } elseif ($request->filled('invoice_to_date')) {
                $query->where('invoice_date', '<=', $request->input('invoice_to_date'));
            } else {
                    $query->whereDate('invoice_date', Carbon::today());
            }
        });

        $Invoices= $purchaseQuery->orderBy('invoice_date','DESC')->get();
        return view('reports.purchase-report', [
            'pageTitle' => 'Purchase Report',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'suppliers' => Mst_Supplier::orderBy('created_at','DESC')->get(),
            'purchase' => $Invoices,
        ]);
    }

    public function PurchaseReportDetail(Request $request, $id)
    {

        $purchaseQuery = Trn_Medicine_Purchase_Invoice_Detail::join('trn_medicine_purchase_invoices', 'trn_medicine_purchase_invoices.purchase_invoice_id', '=', 'trn_medicine_purchase_invoice_details.invoice_id')
            ->join('mst_medicines', 'trn_medicine_purchase_invoice_details.product_id', '=', 'mst_medicines.id')
            ->where('trn_medicine_purchase_invoice_details.invoice_id', $request->id);
    
        if ($request->filled('medicine_name')) {
            $purchaseQuery->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
        }
    
        $PurchaseDetail = $purchaseQuery->get();

        return view('reports.purchase-report-detail', [
            'pageTitle' => 'Purchase Report Detail',
            'invoice_id' => $id,
            'purchase_details' => $PurchaseDetail,
        ]);
    }
    
    public function PurchaseReturnReport(Request $request)
    {
        $purchaseReturnQuery = Trn_Medicine_Purchase_Return::select(
            'purchase_return_id',
            'purchase_return_no',
            'purchase_invoice_id',
            'supplier_id',
            'return_date',
            'pharmacy_id',
            'sub_total'
        )
        ->with(['pharmacy', 'supplier','PurchaseInvoice'])
        ->withCount([
            'PurchaseReturnDetails as purchase_invoice_return_details_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $purchaseReturnQuery->whereIn('pharmacy_id', $mappedPharmacies);
        }

        if ($request->filled('pharmacy_id')) {
            $purchaseReturnQuery->where('pharmacy_id', $request->input('pharmacy_id'));
        }

        if ($request->filled('supplier_id')) {
            $purchaseReturnQuery->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('purchase_return_no')) {
            $purchaseReturnQuery->where('purchase_return_no', $request->input('purchase_return_no'));
        }
        $purchaseReturnQuery->where(function ($query) use ($request) {
            if ($request->filled('return_from_date') && $request->filled('return_to_date')) {
                $query->whereBetween('return_date', [$request->input('return_from_date'), $request->input('return_to_date')]);
            } elseif ($request->filled('return_from_date')) {
                $query->where('return_date', '>=', $request->input('return_from_date'));
            } elseif ($request->filled('return_to_date')) {
                $query->where('return_date', '<=', $request->input('return_to_date'));
            }
        });

        $Invoices= $purchaseReturnQuery->paginate(10);
        return view('reports.return.purchase-return-report', [
            'pageTitle' => 'Purchase Return Report',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'suppliers' => Mst_Supplier::orderBy('created_at','DESC')->get(),
            'purchase_returns' => $Invoices,
        ]);
    }

    
    public function PurchaseReturnReportDetail(Request $request, $id)
    {

        $purchaseReturnQuery = Trn_Medicine_Purchase_Return_Detail::join('trn_medicine_purchase_return', 'trn_medicine_purchase_return.purchase_return_id', '=', 'trn_medicine_purchase_return_details.purchase_return_id')
            ->join('mst_medicines', 'trn_medicine_purchase_return_details.product_id', '=', 'mst_medicines.id')
            ->where('trn_medicine_purchase_return_details.purchase_return_id', $request->id);
    
        if ($request->filled('medicine_name')) {
            $purchaseReturnQuery->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
        }
    
        $PurchaseReturnDetail = $purchaseReturnQuery->get();

        return view('reports.return.purchase-return-detail', [
            'pageTitle' => 'Purchase Return Report Detail',
            'return_id' => $id,
            'purchase_return_details' => $PurchaseReturnDetail,
        ]);
    }

    
    public function SalesReturnReport(Request $request)
    {
        $saleReturnQuery = Trn_Medicine_Sales_Return::select(
            'sales_return_id',
            'sales_return_no',
            'sales_invoice_id',
            'patient_id',
            'pharmacy_id',
            'return_date',
            'total_amount'
        )
        ->with(['Pharmacy', 'Patient','Invoice'])
        ->withCount([
            'salesReturnDetails as sales_return_detail_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $saleReturnQuery->whereIn('pharmacy_id', $mappedPharmacies);
        }

        if ($request->filled('pharmacy_id')) {
            $saleReturnQuery->where('pharmacy_id', $request->input('pharmacy_id'));
        }

        if ($request->filled('sales_return_no')) {
            $saleReturnQuery->where('sales_return_no', $request->input('sales_return_no'));
        }
        $saleReturnQuery->where(function ($query) use ($request) {
            if ($request->filled('return_from_date') && $request->filled('return_to_date')) {
                $query->whereBetween('return_date', [$request->input('return_from_date'), $request->input('return_to_date')]);
            } elseif ($request->filled('return_from_date')) {
                $query->where('return_date', '>=', $request->input('return_from_date'));
            } elseif ($request->filled('return_to_date')) {
                $query->where('return_date', '<=', $request->input('return_to_date'));
            }
        });

        $Invoices= $saleReturnQuery->paginate(10);
        
        return view('reports.return.sale-return-report', [
            'pageTitle' => 'Sales Return Report',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'sales_returns' => $Invoices,
        ]);
    }


    public function SalesReturnReportDetail(Request $request, $id)
    {

        $salesReturnQuery = Trn_Medicine_Sales_Return_Details::join('trn__medicine__sales__returns', 'trn__medicine__sales__returns.sales_return_id', '=', 'trn__medicine__sales__return__details.sales_return_id')
            ->join('mst_medicines', 'trn__medicine__sales__return__details.medicine_id', '=', 'mst_medicines.id')
            ->where('trn__medicine__sales__return__details.sales_return_id', $request->id);
    
        if ($request->filled('medicine_name')) {
            $salesReturnQuery->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
        }
    
        $saleReturnDetail = $salesReturnQuery->get();

        return view('reports.return.sale-return-detail', [
            'pageTitle' => 'Sale Return Report Detail',
            'return_id' => $id,
            'sale_return_detail' => $saleReturnDetail,
        ]);
    }
    
    //stock transfer report
    
    public function StockTransferReport(Request $request)
    {
        $transferQuery = Trn_branch_stock_transfer::select(
            'id',
            'transfer_code',
            'transfer_date',
            'from_pharmacy_id',
            'to_pharmacy_id',
        )
        ->with(['pharmacy','pharmacys'])
        ->withCount([
            'stockTransferDetails as transfer_item_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $transferQuery->whereIn('from_pharmacy_id', $mappedPharmacies);
        }

        if ($request->filled('from_pharmacy_id')) {
            $transferQuery->where('from_pharmacy_id', $request->input('from_pharmacy_id'));
        }

        if ($request->filled('to_pharmacy_id')) {
            $transferQuery->where('to_pharmacy_id', $request->input('to_pharmacy_id'));
        }

        if ($request->filled('transfer_code')) {
            $transferQuery->where('transfer_code', $request->input('transfer_code'));
        }
        $transferQuery->where(function ($query) use ($request) {
            if ($request->filled('transfer_from_date') && $request->filled('transfer_to_date')) {
                $query->whereBetween('transfer_date', [$request->input('transfer_from_date'), $request->input('transfer_to_date')]);
            } elseif ($request->filled('transfer_from_date')) {
                $query->where('transfer_date', '>=', $request->input('transfer_from_date'));
            } elseif ($request->filled('transfer_to_date')) {
                $query->where('transfer_date', '<=', $request->input('transfer_to_date'));
            }
        });

        $queryData = $transferQuery->orderBy('created_at','DESC')->get();
        return view('reports.stock-transfer-report', [
            'pageTitle' => 'Stock Transfer Report',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'stock_transfers' => $queryData,
        ]);
    }

    public function StockTransferReportDetail(Request $request, $id)
    {

        $stocktransferQuery = Trn_branch_stock_transfer_detail::join('trn_branch_stock_transfers', 'trn_branch_stock_transfers.id', '=', 'trn_branch_stock_transfer_details.stock_transfer_id')
            ->join('mst_medicines', 'trn_branch_stock_transfer_details.medicine_id', '=', 'mst_medicines.id')
            ->where('trn_branch_stock_transfer_details.stock_transfer_id', $request->id);
    
        if ($request->filled('medicine_name')) {
            $stocktransferQuery->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
        }
    
        $stocktransferDetail = $stocktransferQuery->get();

        return view('reports.stock-transfer-report-detail', [
            'pageTitle' => 'Stock Transfer Report Detail',
            'transfer_id' => $id,
            'stock_transfer_detail' => $stocktransferDetail,
        ]);
    }
    
    //current stock report

    
    public function CurrentStockReport(Request $request)
    {
        $currentStock = Trn_Medicine_Stock::select(
            'stock_id',
            'stock_code',
            'medicine_id',
            'pharmacy_id',
            'batch_no',
            'mfd',
            'expd',
            'purchase_rate',
            'sale_rate',
            'current_stock',
        )
        ->with(['medicines','pharmacy']);
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $currentStock->whereIn('pharmacy_id', $mappedPharmacies);
        }

        if ($request->filled('pharmacy_id')) {
            $currentStock->where('pharmacy_id', $request->input('pharmacy_id'));
        }

        if ($request->filled('medicine_name')) {
            $currentStock->whereHas('medicines', function ($query) use ($request) {
                $query->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
            });
        }

        if ($request->filled('medicine_code')) {
            $currentStock->whereHas('medicines', function ($query) use ($request) {
                $query->where('medicine_code', 'like', '%' . $request->input('medicine_code') . '%');
            });
        }

        $queryData = $currentStock->orderBy('created_at','DESC')->get();
        return view('reports.current-stock-report', [
            'pageTitle' => 'Current Stock Report',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'current_stocks' => $queryData,
        ]);
    }
    
    public function PaymentMadeReport(Request $request)
    {
            $purchaseQuery = Trn_Medicine_Purchase_Invoice::select(
            'purchase_invoice_id',
            'purchase_invoice_no',
            'supplier_id',
            'invoice_date',
            'pharmacy_id',
            'total_amount',
            'paid_amount',
            'payment_mode',
            'is_paid',
        )
        ->with(['Supplier'])
        ->withCount([
            'purchaseInvoiceDetails as purchase_invoice_details_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $purchaseQuery->whereIn('pharmacy_id', $mappedPharmacies);
        }

        if ($request->filled('pharmacy_id')) {
            $purchaseQuery->where('pharmacy_id', $request->input('pharmacy_id'));
        }

        if ($request->filled('supplier_id')) {
            $purchaseQuery->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('purchase_invoice_no')) {
            $purchaseQuery->where('purchase_invoice_no', $request->input('purchase_invoice_no'));
        }
        $purchaseQuery->where(function ($query) use ($request) {
            if ($request->filled('invoice_from_date') && $request->filled('invoice_to_date')) {
                $query->whereBetween('invoice_date', [$request->input('invoice_from_date'), $request->input('invoice_to_date')]);
            } elseif ($request->filled('invoice_from_date')) {
                $query->where('invoice_date', '>=', $request->input('invoice_from_date'));
            } elseif ($request->filled('invoice_to_date')) {
                $query->where('invoice_date', '<=', $request->input('invoice_to_date'));
            } else {
                    $query->whereDate('invoice_date', Carbon::today());
            }
        });

        $Invoices= $purchaseQuery->orderBy('invoice_date','DESC')->get();
     
        return view('reports.payment-made-report', [
            'pageTitle' => 'Payment Made Report',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'suppliers' => Mst_Supplier::orderBy('created_at','DESC')->get(),
            'purchase' => $Invoices,
        ]);
    }
    
        public function PaymentMadeReportDetail(Request $request, $id)
    {

        $purchaseQuery = Trn_Medicine_Purchase_Invoice_Detail::join('trn_medicine_purchase_invoices', 'trn_medicine_purchase_invoices.purchase_invoice_id', '=', 'trn_medicine_purchase_invoice_details.invoice_id')
            ->join('mst_medicines', 'trn_medicine_purchase_invoice_details.product_id', '=', 'mst_medicines.id')
            ->where('trn_medicine_purchase_invoice_details.invoice_id', $request->id);
    
        if ($request->filled('medicine_name')) {
            $purchaseQuery->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
        }
    
        $PurchaseDetail = $purchaseQuery->get();

        return view('reports.patment-made-report-detail', [
            'pageTitle' => 'Payment Made Report Detail',
            'invoice_id' => $id,
            'purchase_details' => $PurchaseDetail,
        ]);
    }
    
}
