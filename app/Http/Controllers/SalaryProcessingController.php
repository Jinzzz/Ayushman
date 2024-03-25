<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trn_staff_salary_processing;
use App\Models\Trn_staff_salary_processing_detail;
use App\Models\Trn_Ledger_Posting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Holiday;
use App\Models\Mst_Branch;
use App\Models\Mst_Staff;
use App\Models\Mst_Salary;
use App\Models\Staff_Leave;
use App\Models\Mst_Leave_Type;
use App\Models\Salary_Head_Master;
use App\Models\Trn_Staff_Advance_Salary;
use App\Models\Mst_Master_Value;

class SalaryProcessingController extends Controller
{
    public function index(Request $request)
    {
        return view('salary-processing.index', [
            'processDatas' => Trn_staff_salary_processing::with('staff','branch')->orderBy('created_at','DESC')->get(),
            'pageTitle' => 'Salary Processing'
        ]);
    }

    public function create(Request $request)
    {
        return view('salary-processing.create', [
            'branches' => Mst_Branch::where('is_active','=',1)->orderBy('branch_name','ASC')->get(),
            'earnings' => Salary_Head_Master::where('salary_head_type','=',1)->get(),
            'paymentType' => Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id'),
            'deductions' => Salary_Head_Master::where('salary_head_type','=',2)->get(),
            'pageTitle' => 'Add Salary Processing'
        ]);
    }

    public function getWorkingDays(Request $request)
    {
        $selectedMonth = Carbon::parse($request->input('month'));
        $totalDays = $selectedMonth->daysInMonth;
        $holidays = Holiday::where(function ($query) use ($selectedMonth) {
            $query->where('year', '=', $selectedMonth->year)->whereMonth('from_date', '=', $selectedMonth->month);
        })
        ->orWhere(function ($query) use ($selectedMonth) {
            $query->where('year', '=', $selectedMonth->year)->whereMonth('to_date', '=', $selectedMonth->month);
        })
        ->orWhere(function ($query) use ($selectedMonth) {
            $query->where('from_date', '<=', $selectedMonth->endOfMonth())->where('to_date', '>=', $selectedMonth->startOfMonth());
        })
        ->get();
        $totalLeaveDays = 0;
        foreach ($holidays as $holiday) {
            $fromDate = Carbon::parse($holiday->from_date);
            $toDate = Carbon::parse($holiday->to_date);
            $leaveDays = $toDate->diffInDays($fromDate) + 1;
            if ($fromDate->month === $selectedMonth && $toDate->month === $selectedMonth) 
                {
                    $totalLeaveDays += $leaveDays;
                } 
                elseif ($fromDate->month === $selectedMonth) 
                    {
                    $totalLeaveDays += $toDate->diffInDays($selectedMonth->startOfMonth()) + 1; 
                    } 
                elseif ($toDate->month === $selectedMonth) 
                    {
                        $totalLeaveDays += $selectedMonth->endOfMonth()->diffInDays($fromDate) + 1; 
                    } 
            else {
                $totalLeaveDays += $leaveDays;
            }
        }
        $workingDays = $totalDays - $totalLeaveDays;
        return response()->json(['workingDays' => $workingDays]);
    }

    public function getStaffs($branch_id)
    {
        $staffs = Mst_Staff::where('branch_id', $branch_id)->select('staff_id','staff_name','staff_code')->get();
        return response()->json($staffs);
    }

    public function getStaffSalary($staff_id)
    {
        $totalSalary = Mst_Salary::where('staff_id', $staff_id)->where('salary_head_type','Earning')->sum('amount');
        return response()->json([
            'total_salary' => $totalSalary,
        ]);
    }

    public function getStaffLeaves($staff_id, $month)
    {

        $yearMonth = explode('-', $month);
        $year = $yearMonth[0];
        $month = $yearMonth[1];
        $totalLeaves = Staff_Leave::where('staff_id', $staff_id)->whereYear('created_at', $year)->whereMonth('created_at', $month)->sum('days');
        return response()->json([
            'total_leave' => $totalLeaves
        ]);
    }

    public function getDeductibleLeaveCount($staffId, $month)
    {
        $yearMonth = explode('-', $month);
        $year = $yearMonth[0];
        $month = $yearMonth[1];
        $leaves = Staff_Leave::where('staff_id', $staffId)->whereYear('created_at', $year)->whereMonth('created_at', $month)->get();
        $deductibleLeaveCount = 0;
        foreach($leaves as $leave) {
            $leaveType = Mst_Leave_Type::find($leave->leave_type);
            if($leaveType && $leaveType->is_dedactable == 1) {
                $deductibleLeaveCount++;
            }
        }
        
        return response()->json(['deductible_leave_count' => $deductibleLeaveCount]);
    }

    
    public function getSalaryHeads($staffId, Request $request)
    {
        $earnings = Mst_Salary::where('staff_id', $staffId)
            ->leftJoin('salary_head_masters', 'mst_salary.salary_head', '=', 'salary_head_masters.id')
            ->where('salary_head_masters.salary_head_type', 1)
            ->get(['salary_head_masters.salary_head_name', 'mst_salary.amount']);
        $deductions = Mst_Salary::where('staff_id', $staffId)
            ->leftJoin('salary_head_masters', 'mst_salary.salary_head', '=', 'salary_head_masters.id')
            ->where('salary_head_masters.salary_head_type', 2)
            ->get(['salary_head_masters.salary_head_name', 'mst_salary.amount']);

        return response()->json([
            'earnings' => $earnings,
            'deductions' => $deductions
        ]);
    }
    public function store(Request $request)
    {
       //dd('under development');
       try {
       $existingProcessing = Trn_staff_salary_processing::where('salary_month', $request->salary_month)
            ->where('staff_id', $request->staff_id)
            ->exists();

        if ($existingProcessing) {
            return redirect()->route('salary-processing.index')->with('error', 'Salary Processing of this month for this staff already initiated.');
        }
      
       $salary_processing=new Trn_staff_salary_processing();
       $salary_processing->salary_month=$request->salary_month;
       $salary_processing->staff_id=$request->staff_id;
       $salary_processing->branch_id=$request->branch_id;
       $salary_processing->processed_date=Carbon::now();
       $salary_processing->account_ledger_id=$request->account_ledger_id;
       $salary_processing->bonus=$request->branch_id;
       $salary_processing->overtime_allowance=$request->overtime;
       $salary_processing->other_earnings=$request->other_earnings;
       $salary_processing->other_deductions=$request->other_deductions;
       $salary_processing->lop=$request->lop;
       $salary_processing->total_earnings=$request->total_earnings;
       $salary_processing->total_deductions=$request->total_deductions;
       $salary_processing->net_earning=$request->net_earnings;
       $salary_processing->reference_number=$request->reference_number;
       $salary_processing->processing_status=$request->status;
       $salary_processing->remarks=$request->remarks;
       $salary_processing->payment_mode=$request->payment_mode;
       $salary_processing->save();
       $salary_processing_id = $salary_processing->id;
       $salaries=Mst_Salary::where('staff_id',$request->staff_id)->get();
       foreach($salaries as $salary)
       {
           $salary_details=new Trn_staff_salary_processing_detail();
           $salary_details->salary_head_id=$salary->salary_head;
           $salary_details->amount=$salary->amount;
           $salary_details->salary_processing_id=$salary_processing_id;
           $salary_details->save();
           
       }
       if($request->net_earnings>0)//if advance salary is fully taken then net earnings will be zero
       {
               if($request->status==1)
               {
                   Trn_Ledger_Posting::create([
                    'posting_date' => Carbon::now(),
                    'master_id' => 'SAL_PRO' . $salary_processing_id,
                    'account_ledger_id' => 93,
                    'entity_id' => $request->staff_id,
                    'debit' =>$request->net_earnings,
                    'credit' => 0,
                    'branch_id' => $request->input('branch_id'),
                    'transaction_id' =>  $salary_processing_id,
                    'narration' => 'Salary Processing'
                ]);
                   
               }
                if($request->status==2)
               {
                   Trn_Ledger_Posting::create([
                    'posting_date' => Carbon::now(),
                    'master_id' => 'SAL_PRO' . $salary_processing_id,
                    'account_ledger_id' => 92,
                    'entity_id' => $request->staff_id,
                    'debit' =>0,
                    'credit' => $request->net_earnings,
                    'branch_id' => $request->input('branch_id'),
                    'transaction_id' =>  $salary_processing_id,
                    'narration' => 'Salary Processing'
                ]);
                   
               }
       }
        
       return redirect()->route('salary-processing.index')->with('success','Salary Processing updated Successfully');
      
    } catch (\Exception $e) {
        return redirect()->route('salary-processing.index')->with('error', 'Error occurred while processing salary: ' . $e->getMessage());
    }
       
         
       //Trn_staff_salary_processing_detail;
    }
    public function SalaryProcessingView($id)
    {
          return view('salary-processing.view', [
            'salary_process'=> Trn_staff_salary_processing::findOrFail($id),
            'pageTitle' => 'View Salary Processing'
        ]);
        
    }
    //Salry processing change status
    public function changeStatus($id)
    {
        try {
            $s_process = Trn_staff_salary_processing::findOrFail($id);
            $s_process->processing_status = 2;
            $s_process->save();
            if($s_process->net_earning>0)
            {
                Trn_Ledger_Posting::create([
                    'posting_date' => Carbon::now(),
                    'master_id' => 'SAL_PRO' . $id,
                    'account_ledger_id' => 92,
                    'entity_id' => $s_process->staff_id,
                    'debit' =>0,
                    'credit' => $s_process->net_earning,
                    'branch_id' => $s_process->branch_id,
                    'transaction_id' =>  $id,
                    'narration' => 'Salary Processing'
                ]);
                
            }
            
            return 1;
        } catch (QueryException $e) {
            return redirect()->route('salary-processing.index')->with('error', 'Something went wrong');
        }
    }
    
    
     public function AdvanceSalaryIndex(Request $request)
    {
        return view('advance-salary.index', [
            'processDatas' => Trn_Staff_Advance_Salary::orderBy('created_at','DESC')->get(),
            'pageTitle' => 'Advance Salary'
        ]);
    }
    
    public function AdvanceSalaryCreate(Request $request)
    {
        return view('advance-salary.create', [
            'branches' => Mst_Branch::where('is_active','=',1)->orderBy('branch_name','ASC')->get(),
            'paymentType' => Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id'),
            'pageTitle' => 'Add Advance Salary'
        ]);
    }
     public function AdvanceSalaryStore(Request $request)
    {
       //dd('under development');
       try {
       $existingAdvance = Trn_Staff_Advance_Salary::where('salary_month', $request->salary_month)
            ->where('staff_id', $request->staff_id)
            ->exists();

        if ($existingAdvance) {
            return redirect()->route('advance-salary.index')->with('error', 'Advance salary of this month for this staff already initiated.');
        }
         $existingProcessing = Trn_staff_salary_processing::where('salary_month', $request->salary_month)
            ->where('staff_id', $request->staff_id)
            ->exists();

        if ($existingProcessing) {
            return redirect()->route('advance-salary.index')->with('error', 'Salary Processing of this month for this staff already initiated.');
        }
         if ($request->paid_amount>$request->net_earnings) {
            return redirect()->route('advance-salary.index')->with('error', 'Payment amount should be less than net earnings.');
        }
       
       $advance_salary=new Trn_Staff_Advance_Salary();
       $advance_salary->salary_month=$request->salary_month;
       $advance_salary->staff_id=$request->staff_id;
       $advance_salary->branch_id=$request->branch_id;
       $advance_salary->payed_date=$request->payed_date;
       $advance_salary->paid_amount=$request->paid_amount;
       $advance_salary->payed_through_ledger_id=$request->payed_through_ledger_id;
       $advance_salary->payed_through_mode=$request->payed_through_mode;
       $advance_salary->net_earnings=$request->net_earnings;
       $advance_salary->reference_number=$request->reference_number;
       $advance_salary->remarks=$request->remarks;
       $advance_salary->payment_mode=$request->payment_mode;
       $advance_salary->created_by=Auth::user()->id;
       $advance_salary->save();


    
         
         Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'ADV_SAL' . $advance_salary->id,
            'account_ledger_id' => 94,
            'entity_id' => $request->staff_id,
            'debit' =>$request->paid_amount,
            'credit' => 0,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $advance_salary->id,
            'narration' => 'Advance Salary'
        ]);
     
          Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'SADV_SAL' . $advance_salary->id,
            'account_ledger_id' => 93,
            'entity_id' => $request->staff_id,
            'debit' =>0,
            'credit' => $request->paid_amount,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $advance_salary->id,
            'narration' => 'Advance Salary'
        ]);
         Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'SADV_SAL' . $advance_salary->id,
            'account_ledger_id' => $request->payed_through_ledger_id,
            'entity_id' => $request->staff_id,
            'debit' =>0,
            'credit' => $request->paid_amount,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $advance_salary->id,
            'narration' => 'Advance Salary'
        ]);
           
      
        
       return redirect()->route('advance-salary.index')->with('success','Advance salary  Processing completed Successfully');
      
    } catch (\Exception $e) {
        return redirect()->route('advance-salary.index')->with('error', 'Error occurred while advance salary process: ' . $e->getMessage());
    }
       
         
       //Trn_staff_salary_processing_detail;
    }
     public function AdvanceSalaryView($id)
    {
          return view('advance-salary.view', [
            'salary_process'=> Trn_Staff_Advance_Salary::findOrFail($id),
            'pageTitle' => 'View Advance Salary'
        ]);
        
    }
     public function getAdvanceSalary($staffId,$salaryMonth, Request $request)
    {
        
        $advance_salary=Trn_Staff_Advance_Salary::where('staff_id',$staffId)->where('salary_month',$salaryMonth)->first();
        if($advance_salary)
        {
            $paid_amount=$advance_salary->paid_amount;
        }
        else
        {
            $paid_amount=0;
        }

        return response()->json([
            'advance_salary' => $paid_amount
        ]);
    }
    

}
