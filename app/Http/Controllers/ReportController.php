<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Trn_Medicine_Sales_Invoice;
use App\Models\Trn_Medicine_Sales_Invoice_Details;
use App\Models\Mst_Pharmacy;


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
}
