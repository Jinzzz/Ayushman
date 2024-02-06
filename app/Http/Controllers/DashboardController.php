<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Trn_Medicine_Sales_Invoice;
use App\Models\Trn_Medicine_Purchase_Invoice;
use App\Models\Staff_Leave;
use Carbon\Carbon;
use DB;


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

    
    public function pharmaIndex()
    {
        $pageTitle="Pharmacy Dashboard";
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
        
        return view('auth.pharmacy.home',compact('pageTitle','dailySale','medicineSaleWeekly','medicineSaleMonthly','totalSales'));
    }
}
