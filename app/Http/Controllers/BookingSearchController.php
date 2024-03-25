<?php

namespace App\Http\Controllers;
use App\Models\Mst_Patient;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Staff;
use App\Models\Mst_Master_Value;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class BookingSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $userTypeId = Auth::user()->user_type_id;
        $branchId = null;

        if ($userTypeId != 1) {
            
            $staffId = Auth::user()->staff_id;
            
            $branchId = Mst_Staff::where('staff_id', $staffId)->value('branch_id');
            $patients = Trn_Consultation_Booking::leftJoin('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
            ->leftJoin('mst_master_values as booking_type', 'booking_type.id', '=', 'trn_consultation_bookings.booking_type_id')
            ->leftJoin('mst_master_values as booking_status', 'booking_status.id', '=', 'trn_consultation_bookings.booking_status_id')
            ->leftJoin('mst_branches', 'mst_branches.branch_id', '=', 'trn_consultation_bookings.branch_id')
            ->where('trn_consultation_bookings.branch_id', $branchId)
            ->select(
                'trn_consultation_bookings.*',
                'mst_patients.patient_code',
                'mst_patients.patient_name',
                'mst_patients.patient_email',
                'mst_patients.patient_mobile',
                'booking_type.master_value as booking_type_value',
                'booking_status.master_value as booking_status_value',
                'mst_branches.*'
            );
        
            if ($request->filled('booking_reference_number')) {
                $patients->where('booking_reference_number', $request->input('booking_reference_number'));
            }
        
            if ($request->filled('booking_type_id')) {
                $patients->where('booking_type_id', $request->input('booking_type_id'));
            }
            if ($request->filled('booking_status')) {
                $patients->where('booking_status.master_value', $request->input('booking_status'));
            }
                    
        
            $patients = $patients->orderBy('trn_consultation_bookings.created_at','DESC')->limit(6)->get(); 
        
            $statuses =  Mst_Master_Value::where('master_id', 22)->get(); //list of statuses from master table
           
        
            $Bookingtypes = Mst_Master_Value::whereIn('id', [84, 85, 86])->get();
        
            $pageTitle = "Booking Search";
        
            return view('booking_search.index', compact('patients', 'pageTitle', 'Bookingtypes','statuses'));
        } else {
            $patients = Trn_Consultation_Booking::leftJoin('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
            ->leftJoin('mst_master_values as booking_type', 'booking_type.id', '=', 'trn_consultation_bookings.booking_type_id')
            ->leftJoin('mst_master_values as booking_status', 'booking_status.id', '=', 'trn_consultation_bookings.booking_status_id')
            ->leftJoin('mst_branches', 'mst_branches.branch_id', '=', 'trn_consultation_bookings.branch_id')
            ->select(
                'trn_consultation_bookings.*',
                'mst_patients.patient_code',
                'mst_patients.patient_name',
                'mst_patients.patient_email',
                'mst_patients.patient_mobile',
                'booking_type.master_value as booking_type_value',
                'booking_status.master_value as booking_status_value',
                'mst_branches.*'
            );
        
            if ($request->filled('booking_reference_number')) {
                $patients->where('booking_reference_number', $request->input('booking_reference_number'));
            }
        
            if ($request->filled('booking_type_id')) {
                $patients->where('booking_type_id', $request->input('booking_type_id'));
            }
            if ($request->filled('booking_status')) {
                $patients->where('trn_consultation_bookings.booking_status_id', $request->input('booking_status'));
            }
                    
        
            $patients = $patients->orderBy('trn_consultation_bookings.created_at','DESC')->limit(6)->get(); 
            $statuses =  Mst_Master_Value::where('master_id', 22)->get();
           
        
            $Bookingtypes = Mst_Master_Value::whereIn('id', [84, 85, 86])->get();
        
            $pageTitle = "Booking Search";
        
            return view('booking_search.index', compact('patients', 'pageTitle', 'Bookingtypes','statuses'));
        }

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
        $patient = Trn_Consultation_Booking::leftJoin('mst_patients as p', 'trn_consultation_bookings.patient_id', '=', 'p.id')
        ->leftJoin('mst_staffs as s', 'trn_consultation_bookings.doctor_id', '=', 's.staff_id')
        ->leftJoin('mst_branches as b', 'trn_consultation_bookings.branch_id', '=', 'b.branch_id')
        ->leftJoin('mst_timeslots as t', 'trn_consultation_bookings.time_slot_id', '=', 't.id')
        ->leftJoin('mst_master_values as mv', 'trn_consultation_bookings.booking_status_id', '=', 'mv.id')
        ->select(
            'p.*', 'trn_consultation_bookings.*',
            's.*', 'b.*', 't.*', 'mv.*',
            'trn_consultation_bookings.created_at' // Include created_at field
        )
        ->where('trn_consultation_bookings.id', $id)
        ->first();
    
      
        return view('booking_search.show', compact('patient'));
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
}
