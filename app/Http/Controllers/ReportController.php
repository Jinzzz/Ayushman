<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Mst_Branch;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function salesReport(Request $request)
    {
       
    
        $datas = DB::table('trn_medicine_sale_invoices')
            ->leftjoin('trn_medicine_sales_orders', 'trn_medicine_sales_orders.sales_order_id', '=', 'trn_medicine_sale_invoices.sales_order_id')
            ->leftjoin('mst_patients', 'mst_patients.id', '=', 'trn_medicine_sales_orders.patient_id')
            ->leftjoin('mst_branches', 'mst_branches.branch_id', '=', 'trn_medicine_sales_orders.branch_id')
            ->leftjoin('trn_consultation_bookings', 'trn_consultation_bookings.id', '=', 'trn_medicine_sales_orders.booking_id')
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
            $data = $datas->get();
        
        return view('reports.sales', compact('data'));
    }
    
    

    public function purchaseReport()
    {
        dd(2);
    }

    public function returnReport()
    {
        dd(3);
    }

    public function stockTransferReport()
    {
        dd(4);
    }

    public function currentStockReport()
    {
        dd(5);
    }

    public function paymentReceivedReport()
    {
        dd(6);
    }

    public function receivableReport()
    {
        dd(7);
    }

    public function paymentMadeReport()
    {
        dd(8);
    }

    public function payableReport()
    {
        dd(9);
    }

    public function ledgerReport()
    {
        dd(10);
    }

    public function profitAndLossReport()
    {
        dd(11);
    }

    public function trailBalanceReport()
    {
        dd(12);
    }
    public function balanceSheetReport()
    {
        dd(13);
    }
}
