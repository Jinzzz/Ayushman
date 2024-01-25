<?php

namespace App\Http\Controllers;
use App\Models\Trn_Consultation_Booking;
use App\Models\Trn_Billing_Invoice;
use App\Models\Mst_Master_Value;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrnConsultationBillingController extends Controller
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
    
        return view('consultation_billing.index', compact('pageTitle', 'patientNames', 'firstPatientId', 'datas'));
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
        ->where('trn_consultation_bookings.booking_type_id', '=', 84)
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
        $paymentType = Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id');
        $percentage = Auth::user()->discount_percentage;
    
         return view('consultation_billing.create', compact('pageTitle','data','consultation_id','paymentType','percentage'));
    }
    public function generateInvoice(Request $request)
    {
dd($request->all());
            $validatedData = $request->validate([
                'booking_id' => 'required|string|max:255',
                'patient_id' => 'required|string|max:255',
                'invoice_date' => 'required|date',
                'booking_date' => 'required|date',
                'patient_name' => 'required',
                'patient_contact' => 'required',
                'due_amount' => 'required',
                'payment_mode' => 'required',
                'deposit_to' => 'required',
            ]);
            if ($request->input('discount_percentage')) {

            $discount_amount = $request->input('due_amount') - $request->input('total_amount');
            $consultationBilling = new Trn_Billing_Invoice;
            $consultationBilling->booking_id = $request->input('booking_id');
            $consultationBilling->patient_id = $request->input('patient_id');
            $consultationBilling->booking_invoice_number = 'INV' . $request->input('booking_id');
            $consultationBilling->booking_reference_number = $request->input('booking_reference_number');
            $consultationBilling->invoice_date = $validatedData['invoice_date'];
            $consultationBilling->booking_date = $request->input('booking_date');
            $consultationBilling->patient_name = $request->input('patient_name');
            $consultationBilling->patient_contact = $request->input('patient_contact');
            $consultationBilling->payment_mode = $request->input('payment_mode');
            $consultationBilling->deposit_to = $request->input('deposit_to');
            $consultationBilling->reference_code = $request->input('reference_code');
            $consultationBilling->amount = $request->input('due_amount');
            $consultationBilling->discount_amount = $discount_amount;
            $consultationBilling->discount_percentage = $request->input('discount_percentage');
            $consultationBilling->due_amount = $request->input('total_amount');
            $consultationBilling->created_by = Auth::id();
            $consultationBilling->save();
            return redirect()->route('consultation_billing.index')->with('success', 'Invoice generated successfully');
            }
            else{

            $consultationBilling = new Trn_Billing_Invoice;
            $consultationBilling->booking_id = $request->input('booking_id');
            $consultationBilling->patient_id = $request->input('patient_id');
            $consultationBilling->booking_invoice_number = 'INV' . $request->input('booking_id');
            $consultationBilling->booking_reference_number = $request->input('booking_reference_number');
            $consultationBilling->invoice_date = $validatedData['invoice_date'];
            $consultationBilling->booking_date = $request->input('booking_date');
            $consultationBilling->patient_name = $request->input('patient_name');
            $consultationBilling->patient_contact = $request->input('patient_contact');
            $consultationBilling->payment_mode = $request->input('payment_mode');
            $consultationBilling->deposit_to = $request->input('deposit_to');
            $consultationBilling->reference_code = $request->input('reference_code');
            $consultationBilling->amount = $request->input('due_amount');
            $consultationBilling->discount_amount = 0;
            $consultationBilling->discount_percentage = 0;
            $consultationBilling->due_amount = $request->input('due_amount');
            $consultationBilling->created_by = Auth::id();
            $consultationBilling->save();

            }
            return redirect()->back()->with('success', 'Invoice generated successfully');
       
    }
      
    
}
