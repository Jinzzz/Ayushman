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
            'total_amount'
        )
        ->with('pharmacy')
        ->withCount([
            'salesInvoiceDetails as sales_invoice_details_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);
        $saleQuery->where(function ($query) {
            
            if (request()->has('pharmacy_id')) {
                $query->where('pharmacy_id', request()->pharmacy_id);
            }
            if (request()->has('sales_invoice_number')) {
                $query->orWhere('sales_invoice_number', 'like', '%' . request()->sales_invoice_number . '%');
            }
        });
        $salesInvoices = $saleQuery->paginate(10);

        return view('reports.sales-report', [
            'pageTitle' => 'Sales Reports',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'sales' => $salesInvoices,
        ]);
    }

    
    public function SalaryReportDetail(Request $request, $id)
    {
        $SalesDetail = Trn_Medicine_Sales_Invoice_Details::where('sales_invoice_id',$request->id)->get();
        return view('reports.sales-report-detail', [
            'pageTitle' => 'Sales Report Detail',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'invoice_id' => $id,
            'sales_details' => $SalesDetail,
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
            'total_amount'
        )
        ->with(['Pharmacy', 'Supplier'])
        ->withCount([
            'purchaseInvoiceDetails as purchase_invoice_details_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);

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
            }
        });

        $Invoices= $purchaseQuery->paginate(10);
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

}
