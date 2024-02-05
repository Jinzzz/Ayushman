<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function home(Request $request)
    {
        $data = array();
        $data['status'] = 1;
        $data['saleCount'] = 1200;
        $data['purchaseCount'] = 2500;
        $data['credit'] = 5000.5;
        $data['message'] = 'Dashboard data fetched';

        $pageNumber = $request->input('page', 1); // Get the page number from the request, default to 1 if not provided

        $low_stock_medicines = DB::table('trn_medicine_stock')
            ->leftJoin('mst_medicines', 'trn_medicine_stock.medicine_id', '=', 'mst_medicines.id')
            ->leftJoin('mst_master_values', 'mst_medicines.manufacturer', '=', 'mst_master_values.id')
            ->select('mst_medicines.medicine_name', 'mst_master_values.master_value as company_name', 'trn_medicine_stock.current_stock')
            ->whereRaw('trn_medicine_stock.current_stock <= mst_medicines.reorder_limit');
        $low_stock_count=$low_stock_medicines->count();
        $data['low_stock_total']=$low_stock_count;
        $low_stock_medicines=$low_stock_medicines->paginate(10, ['*'], 'page', $pageNumber); // Change 10 to the number of items per page you want
        $data['low_stock_medicines'] = $low_stock_medicines->items();
        $staff_leaves=DB::table('trn_staff_leaves')
                    ->leftjoin('mst_staffs','mst_staffs.staff_id', '=', 'trn_staff_leaves.user_id')
                    ->leftjoin('mst_master_values as leave_type','leave_type.id', '=', 'trn_staff_leaves.leave_type_id')          
                    ->select('mst_staffs.staff_name','leave_type.master_value','trn_staff_leaves.leave_duration','trn_staff_leaves.leave_date')
                    ->get(); 
        foreach($staff_leaves as $staff_leave)
        {
            $leave_duration=DB::table('mst_master_values')->where('id',$staff_leave->leave_duration)->first()->master_value;
            $staff_leave->leave_duration=$leave_duration;

        }
        $data['staff_leaves']=$staff_leaves;
        return response()->json($data);
    }
    public function getGraph(Request $request)
    {
        $data = array();
        try {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $branch_id = $request->input('branch_id');
        $graph_type=$request->graph_type;
        if(!isset($request->graph_type))
        {
            $data['status']=0;
            $data['message']="Graph type required";
            return response($data);
        }
        $previousWeekdays = [];
        $days=$request->days;
    
        if ($start_date && $end_date) {
            $dates = \Carbon\CarbonPeriod::create($start_date, $end_date);
        } else {
            $dates = \Carbon\CarbonPeriod::create(now()->subDays($days-1), now());
        }
    
        foreach ($dates as $date) {
            $weekday = $date->format('l'); // Get the weekday name (e.g., Monday)
            if($graph_type=='medicine_sale')
            {
                $query = DB::table('trn_medicine_sale_invoices')->whereDate('invoice_date', $date)->where('is_paid', 1);
    
                if ($branch_id) {
                    $query->where('branch_id', $branch_id);
                }
        
                $totalAmount =  floatval($query->sum('total_amount'));

            }
            elseif($graph_type=='other_sale')
            {
                $query = DB::table('trn_therapy_booking_invoices')->whereDate('invoice_date', $date)->where('is_paid', 1);
    
                if ($branch_id) {
                    $query->where('branch_id', $branch_id);
                }
        
                $totalAmount =  floatval($query->sum('paid_amount'));

            }
            elseif($graph_type=='total_booking')
            {
                $query = DB::table('trn_consultation_booking_invoices')->whereDate('invoice_date', $date)->where('is_paid', 1);
    
                if ($branch_id) {
                    $query->where('branch_id', $branch_id);
                }
        
                $totalAmount =  floatval($query->sum('paid_amount'));

            }
            else
            {
                $data['status']=0;
                $data['message']="Graph type is invalid";
                return response($data);

            }
           
    
            $previousWeekdays[] = $weekday;
            $previousDates[] = $date->format('Y-m-d');
            $totalSaleAmounts[] = $totalAmount;
        }
    
        //$saleGraph['previous_weekdays'] = $previousWeekdays;
        $saleGraph['dates'] = $previousDates;
        $saleGraph['amounts'] = $totalSaleAmounts;
        $data['status'] = 1;
        $data['message'] = 'Graph data fetched';
        if($graph_type=='medicine_sale')
        {
            $graph_title='Total Medicine Sales';
        }
        elseif($graph_type=='other_sale')
        {
            $graph_title='Other Sales';
        }
        elseif($graph_type=='total_booking')
        {
            $graph_title='Total Bookings';
        }
        
        $data['graph_title']=$graph_title;
        $data['graph'] = $saleGraph;
        return response()->json($data);
        }catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
   }
}
