<?php

namespace App\Http\Controllers;

use App\Models\Mst_Branch;
use Illuminate\Support\Facades\Mail;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Staff;
use App\Models\Mst_User;
use App\Models\Sys_Salary_Type;
use App\Models\Mst_Staff_Transfer_Log;
use App\Models\Mst_Leave_Type;
use App\Models\Salary_Head_Type;
use App\Models\Mst_Leave_Config;
use App\Models\Mst_Salary;
use App\Models\Salary_Head_Master;
use App\Models\Mst_Staff_Commission_Log;
use App\Models\Trn_Staff_Salary_History;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Mail\PasswordEmail;
use App\Models\Mst_Pharmacy;
use App\Models\StaffPharmacyMapping;

class MstStaffController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = "staffs";
        $branch = Mst_Branch::pluck('branch_name','branch_id');
        $stafftype = Mst_Master_Value::where('master_id',4)->pluck('master_value','id');
        $query = Mst_Staff::query();

        if($request->has('staff_type')){
            $query->where('staff_type','LIKE',"%{$request->staff_type}%");
        }
        if($request->has('staff_name')){
            $query->where('staff_name','LIKE',"%{$request->staff_name}%");
        }
        if($request->has('staff_code')){
            $query->where('staff_code','LIKE',"%{$request->staff_code}%");
        }
        if($request->has('branch_id')){
            $query->where('branch_id','LIKE',"%{$request->branch_id}%");
        }
        if($request->has('contact_number')){
            $query->where('staff_contact_number','LIKE',"%{$request->contact_number}%");
        }
      
        $staffs = $query->orderBy('updated_at', 'desc')->get();
        return view('staffs.index',compact('pageTitle','staffs','branch','stafftype'));
    }

    public function create()
    {
        $pageTitle = "Create Staff";
        $stafftype = Mst_Master_Value::where('master_id',4)->pluck('master_value','id');
        $employmentType = Mst_Master_Value::where('master_id',5)->pluck('master_value','id');
        $gender = Mst_Master_Value::where('master_id',17)->pluck('master_value','id');
        $branchs = Mst_Branch::get();
        $salaryType = Sys_Salary_Type::pluck('salary_type','id');
        $commissiontype = Mst_Master_Value::where('master_id',8)->pluck('master_value','id');
        $leave_types = Mst_Leave_Type::where('is_active', 1)->get();
        $branch = Salary_Head_Type::get();
        $heads = Salary_Head_Master::where('status', 1)->get();
        $pharmacies = Mst_Pharmacy::get();
        return view('staffs.create',compact('pageTitle','stafftype','employmentType','gender','branchs','commissiontype','salaryType','leave_types','branch','heads','pharmacies'));
    }

    public function store(Request $request)
    {
 
        $request->validate([
            'staff_type' => 'required',
                'employment_type' => 'required',
                'staff_name' => 'required|max:100',
                'gender' => 'required',
                'branch_id' => 'required',
                'date_of_birth' => 'required|date',
                'staff_email' => 'required',
                'staff_contact_number' => 'required',
                'staff_address' => 'required',
                'staff_qualification' => 'required|max:100',
                'staff_specialization' => 'max:255',
                'staff_work_experience' => 'required',
                'staff_commission_type' => 'required',
                'staff_commission' => 'required',
                'access_card_number' => 'required',
                'date_of_join' => 'required',
                'is_active' => 'required',
            'salary_head_id' => 'required',
            'salary_head_type_id' => 'required',
            'amount' => 'required',
            'leave_type' => 'required',
            'credit_period' => 'required',
            'credit_limit' => 'required', 
        ]);
    
        // Process personal info
        $is_active = $request->input('is_active') ? 1 : 0;
        $is_login = $request->input('is_login') ? 1 : 0;
        $generatedPassword = Str::random(6);
    
        $staff = Mst_Staff::create([
            'staff_type' => $request->input('staff_type'),
            'employment_type' => $request->input('employment_type'),
            'staff_name' => $request->input('staff_name'),
            'gender' => $request->input('gender'),
            'branch_id' => $request->input('branch_id'),
            'date_of_birth' => $request->input('date_of_birth'),
            'staff_email' => $request->input('staff_email'),
            'staff_contact_number' => $request->input('staff_contact_number'),
            'staff_address' => $request->input('staff_address'),
            'staff_qualification' => $request->input('staff_qualification'),
            'staff_specialization' => $request->input('staff_specialization'),
            'staff_work_experience' => $request->input('staff_work_experience'),
            'staff_commission_type' => $request->input('staff_commission_type'),
            'staff_commission' => $request->input('staff_commission'),
            'access_card_number' => $request->input('access_card_number'),
            'date_of_join' => $request->input('date_of_join'),
            'is_active' => $is_active,
            'is_login' => $is_login,
            'created_by' => Auth::id(),
            'staff_username' =>$request->input('staff_username'),
        
        ]);

        if ($request->input('staff_type') == 96 && $request->has('pharmacy')) {
            $selectedPharmacies = $request->input('pharmacy');
            if (is_array($selectedPharmacies)) {
                foreach ($selectedPharmacies as $pharmacyId) {
                    StaffPharmacyMapping::create([
                        'staff_id' => $staff->staff_id,
                        'pharmacy' => $pharmacyId,
                    ]);
                }
            }
        }

        if ($request->has('staff_username')) {
            // Create a user entry
            $user = Mst_User::create([
                'username' => $request->input('staff_username'),
                'email' => $request->input('staff_email'),
                'password' => bcrypt($generatedPassword),
                'staff_id' => $staff->staff_id,
                'date_of_birth' => $request->input('date_of_birth'),
                'address' => $request->input('staff_address'),
                'gender' => $request->input('gender'),
                'user_type_id' => $request->input('employment_type'),
                'discount_percentage' => $request->input('discount_percentage'),
                'is_active' => $is_active,
                'last_login_time' => now(),
                'created_by' => Auth::id(),
                'last_updated_by' => Auth::id(),
            ]);

            $email = $request->staff_email;
            $username = $request->staff_username;
            $password = $generatedPassword;
            // Send an email with the generated password
            // Mail::to($email)->send(new PasswordEmail($username, $password));
        }
    
        // Update staff code
        $leadingZeros = str_pad('', 3 - strlen($staff->staff_id), '0', STR_PAD_LEFT);
        $staffCode = 'SC' . $leadingZeros . $staff->staff_id;
        Mst_Staff::where('staff_id', $staff->staff_id)->update([
                 'staff_code' => $staffCode
        ]);
        
        // Process salary info
        $salaryHeadIds = $request->input('salary_head_id');
        $salaryHeadTypeIds = $request->input('salary_head_type_id');
        $amounts = $request->input('amount');

        // Remove the last entry from the arrays
        array_pop($salaryHeadIds);
        array_pop($salaryHeadTypeIds);
        array_pop($amounts);

        // Assuming the arrays have the same length, you can use a loop to iterate through them
        foreach ($salaryHeadIds as $index => $headId) {
            Mst_Salary::create([
                'staff_id' => $staff->staff_id,
                'salary_head' => $headId,
                'salary_head_type' => $salaryHeadTypeIds[$index],
                'amount' => $amounts[$index],
            ]);
        }
        // Process available leaves info
        $leaveTypes = $request->input('leave_type');
        $creditPeriods = $request->input('credit_period');
        $creditLimits = $request->input('credit_limit');
    
        // Assuming the arrays have the same length, you can use a loop to iterate through them
        foreach ($leaveTypes as $index => $leaveType) {
            Mst_Leave_Config::create([
                'staff_id' => $staff->staff_id,
                'leave_type' => $leaveType,
                'credit_period' => $creditPeriods[$index],
                'credit_limit' => $creditLimits[$index],
            ]);

    }
    return redirect()->route('staffs.index')->with('success', 'Staff added successfully');
}
    
 

    public function edit($staff_id)
    {
        $pageTitle = "Edit Staff";
        $staffs = Mst_Staff::findOrFail($staff_id)
        ->join('mst_salary', 'mst_staffs.staff_id', '=', 'mst_salary.staff_id')
        ->join('mst_leave_config', 'mst_staffs.staff_id', '=', 'mst_leave_config.staff_id')
        ->select(
            'mst_staffs.*','mst_salary.*','mst_leave_config.*'
        )
        ->first();
   
       
        $stafftype = Mst_Master_Value::where('master_id',4)->pluck('master_value','id');
        $selectedLeaveTypes = DB::table('mst_leave_config')
        ->where('staff_id', $staff_id)
        ->select('leave_type', 'credit_period', 'credit_limit')
        ->get();
        $employmentType = Mst_Master_Value::where('master_id',5)->pluck('master_value','id');
        $gender = Mst_Master_Value::where('master_id',17)->pluck('master_value','id'); 
        $branchs = Mst_Branch::get();
        $salaryType = Sys_Salary_Type::pluck('salary_type','id');
        $commissiontype = Mst_Master_Value::where('master_id',8)->pluck('master_value','id');
        $heads = Salary_Head_Master::where('status', 1)->get();
        $leave_types = Mst_Leave_Type::where('is_active', 1)->get();
        $salaryData = DB::table('mst_salary')->where('staff_id', $staff_id)->get();
        $staff_id = $staff_id;
        
        return view('staffs.edit',compact('pageTitle','staffs','stafftype','employmentType','gender','branchs','salaryType','commissiontype','heads','leave_types','salaryData','selectedLeaveTypes','staff_id'));

    }

    public function update(Request $request, $staff_id)
    {
        $request->validate([
            'staff_type' => 'required',
            'employment_type' => 'required',
            'staff_name' =>'required',
            'gender' =>'required',
            'branch_id' =>'required',
            'date_of_birth' => 'required',
            'staff_contact_number' =>'required',
            'staff_address' => 'required',
            'staff_qualification' =>'required',
            'staff_specialization' => 'required',
            'staff_work_experience' => 'required',
            'staff_commission_type' => 'required',
            'staff_commission' => 'required',
            'access_card_number' =>'required',
            'date_of_join' => 'required',
            'is_active' => 'required',
        'salary_head_id' => 'required',
        'salary_head_type_id' => 'required',
        'amount' => 'required',
        'leave_type' => 'required',
        'credit_period' => 'required',
        'credit_limit' => 'required', 
                                           
        ]);
        $is_active = $request->input('is_active') ? 1 : 0;
        $is_login = $request->input('is_login') ? 1 : 0;
        $generatedPassword = Str::random(6);
        // Find the staff record you want to update
        $staff = Mst_Staff::find($staff_id);

        // Update the fields
        $staff->staff_type = $request->input('staff_type');
        $staff->employment_type = $request->input('employment_type');
        $staff->staff_name = $request->input('staff_name');
        $staff->gender = $request->input('gender');
        $staff->branch_id = $request->input('branch_id');
        $staff->date_of_birth = $request->input('date_of_birth');
        $staff->staff_contact_number = $request->input('staff_contact_number');
        $staff->staff_address = $request->input('staff_address');
        $staff->staff_qualification = $request->input('staff_qualification');
        $staff->staff_specialization = $request->input('staff_specialization');
        $staff->staff_work_experience = $request->input('staff_work_experience');
        $staff->staff_commission_type = $request->input('staff_commission_type');
        $staff->staff_commission = $request->input('staff_commission');
        $staff->access_card_number = $request->input('access_card_number');
        $staff->save();


        return redirect()->route('staffs.index')->with('success', 'Staff record updated successfully.');
    }
      

    public function show($id)
    {

            $pageTitle = "View staff details";
            $show = Mst_Staff::join('mst_salary', 'mst_salary.staff_id', '=', 'mst_staffs.staff_id')
                                ->join('mst_leave_config', 'mst_leave_config.staff_id', '=', 'mst_staffs.staff_id')
                                ->select('mst_staffs.*', 'mst_leave_config.*', 'mst_salary.*',)
                                ->findOrFail($id); 
            $leave_type = Mst_Leave_Type::where('leave_type_id', $show->leave_type)->pluck('name'); 
            $salary = Salary_Head_Master::where('id', $show->salary_head)->pluck('salary_head_name');                  
            return view('staffs.show',compact('pageTitle','show','leave_type','salary'));
      
    }
    
    public function destroy($staff_id)
    {
        $staff = Mst_Staff::findOrFail($staff_id);
        $staff->delete();
    
        return 1;
    }
    


    public function getSalaryHeadTypes($id)
    {
     
        // Your existing logic to retrieve salary_head_type
        $salaryHead = Salary_Head_Master::select('salary_head_types.*')
                      ->join('salary_head_types', 'salary_head_masters.salary_head_type', '=', 'salary_head_types.id')
                      ->where('salary_head_masters.id', $id)
                      ->first();
    
        // Check if the record exists
        if (!$salaryHead) {
            return response()->json(['error' => 'Salary head not found'], 404);
        }
    
        $salaryHeadType = $salaryHead->salary_head_type;
        $salaryHeadTypeId = $salaryHead->id;
    
        // Return the data as JSON with both id and salary_head_type
        return response()->json([
            'id' => $id,
            'salary_head_type' => $salaryHeadType,
            'salaryHeadTypeId' => $salaryHeadTypeId,
        ]);
    }

    public function changeStatus(Request $request, $staff_id)
    {
       
        $staff = Mst_Staff::findOrFail($staff_id);
        
            $staff->is_active = !$staff->is_active;
            $staff->save();
            return 1;
    }

        public function checkUniqueEmail(Request $request)
        {
            $email = $request->input('email');
            $isUnique = Mst_Staff::where('staff_email', $email)->count();
            $data=array();
            if($isUnique>0)
                {
                $data['status']=false;
                }
            else
                {
                $data['status']=true;
                }
            return response()->json($data);
        }

        public function checkUniqueUsername(Request $request)
        {
            $username = $request->input('username');
            $isUnique = Mst_Staff::where('staff_username', $username)->count();
            $data=array();
            if($isUnique>0)
                {
                $data['status']=false;
                }
            else
                {
                $data['status']=true;
                }
            return response()->json($data);

        }
        public function checkUniqueAccessCardNumber(Request $request)
        {
            $access_card_number = $request->input('accesscardnumber');
            $isUnique = Mst_Staff::where('access_card_number', $access_card_number)->count();
            $data=array();
            if($isUnique>0)
                {
                $data['status']=false;
                }
            else
                {
                $data['status']=true;
                }
            return response()->json($data);

        }
    
}
