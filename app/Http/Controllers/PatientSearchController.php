<?php

namespace App\Http\Controllers;

use App\Models\Mst_Medicine;
use App\Models\Mst_Patient;
use App\Models\Trn_Consultation_Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientSearchController extends Controller
{
    public function index(Request $request)
    {
        $patients = Mst_Patient::query();
    
        if ($request->filled('pat_code')) {
            $patients->where('patient_code', $request->input('pat_code'));
        }
    
        if ($request->filled('pat_name')) {
            $patients->where('patient_name', $request->input('pat_name'));
        }
    
        if ($request->filled('pat_mobile')) {
            $patients->where('patient_mobile', $request->input('pat_mobile'));
        }

        if ($request->filled('from_date')) {
            $patients->where('booking_date', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $patients->where('booking_date ', $request->input('to_date'));
        }
    
        $patients = $patients->get();
        $pageTitle = "Patient Serach";
    
        return view('patient_search.index', compact('patients','pageTitle'));
    }

    public function show($id)
    {
        $patient = Mst_Patient::where('id', $id)->first();   

        $patient_bookings = Mst_Patient::leftJoin('trn_consultation_bookings', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
            ->leftJoin('mst_staffs', 'trn_consultation_bookings.doctor_id', '=', 'mst_staffs.staff_id')
            ->leftJoin('mst_branches', 'trn_consultation_bookings.branch_id', '=', 'mst_branches.branch_id')
            ->leftJoin('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
            ->leftJoin('mst_master_values', 'trn_consultation_bookings.booking_status_id', '=', 'mst_master_values.id')
            ->where('trn_consultation_bookings.patient_id', $patient->id)
            ->select('mst_patients.*', 'trn_consultation_bookings.*', 'mst_staffs.*', 'mst_branches.*', 'mst_timeslots.*', 'mst_master_values.*') // Adjust the columns you want to select
            ->get();

            $medicines = Mst_Medicine::get();
      
        return view('patient_search.show', compact('patient','patient_bookings','medicines'));
    }

    public function storePrescription(Request $request)
    {
        
    }    

}
