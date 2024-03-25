<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Trn_Medicine_Sales_Invoice;
use App\Models\Trn_Medicine_Purchase_Invoice;
use App\Models\Staff_Leave;
use Carbon\Carbon;
use DB;
use App\Models\Mst_Staff;
use App\Models\Mst_Pharmacy;
use App\Models\Trn_Medicine_Stock;
use App\Models\Trn_Consultation_Booking;


class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $pageTitle="Dashboard";
        $dailySale = Trn_Medicine_Sales_Invoice::whereDate('invoice_date', Carbon::today())->select(DB::raw('ROUND(SUM(total_amount), 2) as daily_sales'))
         ->first();
        $medicineSaleWeekly = Trn_Medicine_Sales_Invoice::whereBetween('invoice_date', [Carbon::now()->startOfWeek()->format('Y-m-d'),Carbon::now()->endOfWeek()->format('Y-m-d')])
        ->select(DB::raw('ROUND(SUM(total_amount), 2) as weekly_sales'))
         ->first();
        $medicineSaleMonthly = Trn_Medicine_Sales_Invoice::whereBetween('invoice_date',[Carbon::now()->startOfMonth()->format('Y-m-d'),Carbon::now()->endOfMonth()->format('Y-m-d')])
         ->select(DB::raw('ROUND(SUM(total_amount), 2) as monthly_sales'))
         ->first();
        $totalSales = Trn_Medicine_Sales_Invoice::select(DB::raw('ROUND(SUM(total_amount), 2) as sales'))
         ->first();
        $purchases = Trn_Medicine_Purchase_Invoice::where('deleted_at','=',NULL)->count(); 
        $currentDayLeave = Staff_Leave::where(function ($query) {
                            $query->whereDate('from_date', '<=', Carbon::today())
                            ->whereDate('to_date', '>=', Carbon::today());
                        })->count();
        return view('home',compact('pageTitle','dailySale','medicineSaleWeekly','medicineSaleMonthly','totalSales','purchases','currentDayLeave'));
    }
    
    public function savePharmacy(Request $request)
    {
        $pharmacyId = $request->input('pharmacy_id');
           if($pharmacyId == "all")
            {
                $pharmacyName = "All Pharmacies";   
            }else{
                $pharmacy = Mst_Pharmacy::find($pharmacyId);
                $pharmacyName = $pharmacy->pharmacy_name;   
            }
        Session::put('pharmacy_id', $pharmacyId);
        Session::put('pharmacy_name', $pharmacyName);
        return response()->json(['message' => 'Pharmacy ID saved successfully']);
    }
    
    public function pharmaIndex()
    {
        $pageTitle="Pharmacy Dashboard";

        $staffId = auth()->user()->staff_id;
        $pharmacyIds = Mst_Staff::findOrFail($staffId)->pharmacies()->pluck('mst_pharmacies.id')->toArray();
        // $branchId = Mst_Staff::where('staff_id', $staffId)->value('branch_id');
        // $pharmacyId = Mst_Pharmacy::where('branch', $branchId)->value('id');

        $lowStock = Trn_Medicine_Stock::whereIn('pharmacy_id', $pharmacyIds)->where('current_stock','<',5)->count();
        $dailySale = Trn_Medicine_Sales_Invoice::whereIn('pharmacy_id', $pharmacyIds)->whereDate('invoice_date', Carbon::today())->select(DB::raw('ROUND(SUM(total_amount), 2) as daily_sales'))
         ->first();
        $medicineSaleWeekly = Trn_Medicine_Sales_Invoice::whereIn('pharmacy_id', $pharmacyIds)->whereBetween('invoice_date', [Carbon::now()->startOfWeek()->format('Y-m-d'),Carbon::now()->endOfWeek()->format('Y-m-d')])
        ->select(DB::raw('ROUND(SUM(total_amount), 2) as weekly_sales'))
         ->first();
        $medicineSaleMonthly = Trn_Medicine_Sales_Invoice::whereIn('pharmacy_id', $pharmacyIds)->whereBetween('invoice_date',[Carbon::now()->startOfMonth()->format('Y-m-d'),Carbon::now()->endOfMonth()->format('Y-m-d')])
         ->select(DB::raw('ROUND(SUM(total_amount), 2) as monthly_sales'))
         ->first();
        $totalSales = Trn_Medicine_Sales_Invoice::whereIn('pharmacy_id', $pharmacyIds)->select(DB::raw('ROUND(SUM(total_amount), 2) as sales'))
         ->first();
        
        return view('auth.pharmacy.home',compact('pageTitle','lowStock','dailySale','medicineSaleWeekly','medicineSaleMonthly','totalSales'));
    }
    
    public function receptionIndex()
    {
        $pageTitle="Reception Dashboard";
        $currentDate = Carbon::now()->toDateString();
        $staffId = auth()->user()->staff_id;
        $branchId = Mst_Staff::where('staff_id', $staffId)->value('branch_id');
        $currentDayLeave = Staff_Leave::where('branch_id', $branchId)->where(function ($query) {
            $query->whereDate('from_date', '<=', Carbon::today())
            ->whereDate('to_date', '>=', Carbon::today());
        })->count();
        $bookingCount = Trn_Consultation_Booking::where('branch_id', $branchId)
                ->whereDate('created_at', $currentDate)
                ->count();

        return view('auth.receptionist.home',compact('pageTitle','branchId','currentDayLeave','bookingCount'));
    }
    
     public function doctorIndex()
    {
        $pageTitle="Doctor Dashboard";
        $currentDate = Carbon::now()->toDateString();
        $staffId = auth()->user()->staff_id;
        $branchId = Mst_Staff::where('staff_id', $staffId)->value('branch_id');
        $currentDayBooking = Trn_Consultation_Booking::where('branch_id', $branchId)
                ->whereDate('booking_date', $currentDate)
                ->where('doctor_id', $staffId)
                ->where('booking_type_id', 84) //consultation
                ->where('booking_status_id', 88)
                ->count();
        $upComingBooking = Trn_Consultation_Booking::where('branch_id', $branchId)
                ->whereDate('booking_date','>', $currentDate)
                ->where('doctor_id', $staffId)
                ->where('booking_type_id', 84) //consultation
                ->where('booking_status_id', 88)
                ->count();
        $pendingBooking = Trn_Consultation_Booking::where('branch_id', $branchId)
                ->where('doctor_id', $staffId)
                ->where('booking_type_id', 84) //consultation
                ->where('booking_status_id', 88) //confirmed/approved
                ->count(); 

        return view('auth.doctor.home',compact('pageTitle','branchId','currentDayBooking','upComingBooking','pendingBooking'));
    }
    
    public function accountantIndex()
    {
        $pageTitle="Accountant Dashboard";
        $currentDate = Carbon::now()->toDateString();
        $staffId = auth()->user()->staff_id;
        $branchId = Mst_Staff::where('staff_id', $staffId)->value('branch_id');
        $totalSales = Trn_Medicine_Sales_Invoice::where('branch_id', $branchId)->whereNotNull('branch_id')->select(DB::raw('ROUND(SUM(total_amount), 2) as sales'))
         ->first();
        $totalPurchase = Trn_Medicine_Purchase_Invoice::where('branch_id', $branchId)->whereNotNull('branch_id')->select(DB::raw('ROUND(SUM(total_amount), 2) as purchase'))
         ->first();
        $creditPurchase = Trn_Medicine_Purchase_Invoice::where('branch_id', $branchId)->whereNotNull('branch_id')->where('is_paid',0)->count();
        return view('auth.accountant.home',compact('pageTitle','branchId','totalSales','totalPurchase','creditPurchase'));
    }
}
