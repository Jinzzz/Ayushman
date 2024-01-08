<?php

namespace App\Http\Controllers;
use App\Models\Trn_Wellness_Booking_Invoice;
use App\Models\Mst_Wellness;
use App\Models\Trn_Consultation_Booking;
use App\Models\Trn_Billing_Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrnWellnessBillingController extends Controller
{
    public function index()
    {
        $pageTitle = "Consultation Billing";
        $consultationTable = 'trn_consultation_bookings';
        $patientTable = 'mst_patients';
    
        $patients = DB::table($consultationTable)
            ->join($patientTable, "$consultationTable.patient_id", '=', "$patientTable.id")
            ->select(
                "$consultationTable.id as consultation_id",
                "$consultationTable.*",
                "$patientTable.id as patient_id",
                "$patientTable.*"
            )
            ->get();
           
        $patientNames = $patients->pluck('patient_name', 'patient_id');
        $firstPatientId = $patients->isNotEmpty() ? $patients->first()->patient_id : null;
        $datas = []; // Add this line to initialize $datas
    
        return view('wellness_billing.index', compact('pageTitle', 'patientNames', 'firstPatientId', 'datas'));
    } 

    public function patientSearch(Request $request, $id)
    {   
        $consultationTable = 'trn_consultation_bookings';
        $patientTable = 'mst_patients';
    
        $patients = DB::table($consultationTable)
            ->join($patientTable, "$consultationTable.patient_id", '=', "$patientTable.id")
            ->select(
                "$consultationTable.id as consultation_id",
                "$consultationTable.*",
                "$patientTable.id as patient_id",
                "$patientTable.*"
            )
            ->get();
            
        $patientNames = $patients->pluck('patient_name', 'patient_id');
        $patientId = $request->input('patient_id');
        $datas = Trn_Consultation_Booking::join('mst_patients', 'trn_consultation_bookings.patient_id', '=', 'mst_patients.id')
        ->where('trn_consultation_bookings.is_billable', 1)
        ->where('trn_consultation_bookings.booking_type_id', '=', 85)
        ->where('trn_consultation_bookings.patient_id', $patientId)
        ->select('trn_consultation_bookings.id as consultation_id', 'trn_consultation_bookings.*', 'mst_patients.*')
        ->get();
        $firstPatientId =  $patientId;
        return view('consultation_billing.index', compact('datas', 'patientId','firstPatientId','patientNames','patients'));
    }
    public function create($consultation_id)
    {
        $pageTitle = "Create Invoice";
        $data = Trn_Consultation_Booking::join('mst_patients', 'trn_consultation_bookings.patient_id', '=', 'mst_patients.id')
        ->where('trn_consultation_bookings.is_billable', 1)->where('trn_consultation_bookings.id', $consultation_id)
        ->select('trn_consultation_bookings.*','mst_patients.*')
        ->first();
    
         return view('wellness_billing.create', compact('pageTitle','data','consultation_id'));
    }

    public function generateInvoice(Request $request)
    {
        {
            // Validate the form data
            $validatedData = $request->validate([
                'booking_id' => 'required|string|max:255',
                'patient_id' => 'required|string|max:255',
                'invoice_date' => 'required|date',
                // Add more validation rules for other fields
            ]);
            $consultationBilling = new Trn_Billing_Invoice;
    
            // Assign values from the form to the model attributes
            $consultationBilling->booking_id = $request->input('booking_id');
            $consultationBilling->patient_id = $request->input('patient_id');
            $consultationBilling->created_by = Auth::id();
            $consultationBilling->booking_date = $request->input('booking_date');
            $consultationBilling->invoice_date = $validatedData['invoice_date'];
            $consultationBilling->patient_name = $request->input('patient_name');
            $consultationBilling->patient_contact = $request->input('patient_contact');
            $consultationBilling->booking_invoice_number = 'INV' . $request->input('booking_id');
            $consultationBilling->due_amount = $request->input('due_amount');
            $consultationBilling->paid_amount = $request->input('due_amount');
            $consultationBilling->save();
            return redirect()->route('consultation_billing.index')->with('success', 'Invoice generated successfully');
        }
    }


}
