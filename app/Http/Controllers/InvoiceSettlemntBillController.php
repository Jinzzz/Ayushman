<?php

namespace App\Http\Controllers;
use App\Models\Mst_Patient;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Master_Value;
use App\Models\Mst_User;
use App\Models\Trn_Consultation_Booking_Invoice;
use App\Models\Trn_Ledger_Posting;
use Illuminate\Http\Request;
use App\Models\Trn_Consultation_Booking_Invoice_Payment;
use App\Models\Trn_Wellness_Booking_Invoice_Payment;
use App\Models\Trn_Therapy_Booking_Invoice_Payment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InvoiceSettlemntBillController extends Controller
{
    public function invoiceSettlemntBill (Request $request)
    {
        
        $userTypeId = Auth::user()->user_type_id;
        $branchId = null;

        if ($userTypeId != 1) {
            
        $staffId = Auth::user()->staff_id;
        $patients = Trn_Consultation_Booking::leftJoin('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
        ->leftJoin('mst_master_values as booking_type', 'booking_type.id', '=', 'trn_consultation_bookings.booking_type_id')
        ->leftJoin('mst_master_values as booking_status', 'booking_status.id', '=', 'trn_consultation_bookings.booking_status_id')
        ->leftJoin('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
        ->leftJoin('mst_staffs', 'mst_staffs.staff_id', '=', 'trn_consultation_bookings.doctor_id')
        ->leftJoin('mst_branches', 'mst_branches.branch_id', '=', 'trn_consultation_bookings.branch_id')
        ->whereIn('trn_consultation_bookings.booking_type_id', [86, 85, 84])
        ->where('trn_consultation_bookings.branch_id', $branchId)
        ->where('trn_consultation_bookings.is_billable', 0)
        ->select('trn_consultation_bookings.*', 'mst_patients.patient_code', 'mst_patients.patient_name', 'mst_patients.patient_email', 'mst_patients.patient_mobile','mst_staffs.*','booking_type.master_value as booking_type_value',
        'booking_status.master_value as booking_status_value','mst_branches.*');
    
    if ($request->filled('patient_name')) {
        $patients->where('mst_patients.patient_name', $request->input('patient_name'));
    }
    
    if ($request->filled('booking_date')) {
        $patients->whereDate('trn_consultation_bookings.booking_date', $request->input('booking_date'));
    }
    if ($request->filled('booking_type_id')) {
        $patients->where('booking_type_id', $request->input('booking_type_id'));
    }
                
    $patients = $patients->get(); 
 
        
    $pageTitle = "Invoice Settlement";
    
    $patientLists = Mst_Patient::get();

    $Bookingtypes = Mst_Master_Value::whereIn('id', [84, 85, 86])->get();
    
    return view('invoice-settlemnt.index', compact('patients', 'pageTitle','patientLists','Bookingtypes'));
    }
    else{
        $patients = Trn_Consultation_Booking::leftJoin('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
        ->leftJoin('mst_master_values as booking_type', 'booking_type.id', '=', 'trn_consultation_bookings.booking_type_id')
        ->leftJoin('mst_master_values as booking_status', 'booking_status.id', '=', 'trn_consultation_bookings.booking_status_id')
        ->leftJoin('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
        ->leftJoin('mst_staffs', 'mst_staffs.staff_id', '=', 'trn_consultation_bookings.doctor_id')
        ->leftJoin('mst_branches', 'mst_branches.branch_id', '=', 'trn_consultation_bookings.branch_id')
        ->whereIn('trn_consultation_bookings.booking_type_id', [86, 85, 84])
        ->where('trn_consultation_bookings.is_billable', 0)
        ->select('trn_consultation_bookings.*', 'mst_patients.patient_code', 'mst_patients.patient_name', 'mst_patients.patient_email', 'mst_patients.patient_mobile','mst_staffs.*',
        'booking_type.master_value as booking_type_value',
        'booking_status.master_value as booking_status_value','mst_branches.*');
    
    if ($request->filled('patient_name')) {
        $patients->where('mst_patients.patient_name', $request->input('patient_name'));
    }
    
    if ($request->filled('booking_date')) {
        $patients->whereDate('trn_consultation_bookings.booking_date', $request->input('booking_date'));
    }
    if ($request->filled('booking_type_id')) {
        $patients->where('booking_type_id', $request->input('booking_type_id'));
    }
                
    $patients = $patients->get(); 
 
        
    $pageTitle = "Invoice Settlement";
    
    $patientLists = Mst_Patient::get();

    $Bookingtypes = Mst_Master_Value::whereIn('id', [84, 85, 86])->get();
    
    return view('invoice-settlemnt.index', compact('patients', 'pageTitle','patientLists','Bookingtypes'));
    }
}
    
    
        public function generateInvoice($id)
        {
      
            $invoice  = Trn_Consultation_Booking::leftJoin('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
            ->leftJoin('mst_staffs', 'mst_staffs.staff_id', '=', 'trn_consultation_bookings.doctor_id')
            ->where('trn_consultation_bookings.id', $id)->first(); 
       
        
             $booking_id = $id;
        
            $pageTitle = "Therapy Billing";
            $user_id = Auth::id();
            $discount = Mst_User::where('user_id',$user_id)->value('discount_percentage');
            $patientLists = Mst_Patient::get();
            $paymentType = Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id');
            return view('invoice-settlemnt.create', compact('invoice', 'pageTitle','patientLists','paymentType','discount','booking_id'));
        }
    
        public function saveInvoice(Request $request, $id)
        {
     
            $request->validate([
                'booking_id' => 'required',
                'branch_id' => 'required',
                'booking_date' => 'required',
                'invoice_date' => 'required',
                'consultation_fee' => 'required',
            ]);
            $user_id = Auth::id();
            $invoice = new Trn_Consultation_Booking_Invoice();
            $invoice->booking_id = $request->input('booking_id');
            $invoice->branch_id = $request->input('branch_id');
            $invoice->booking_date = $request->input('booking_date');
            $invoice->invoice_date = $request->input('invoice_date');
            $invoice->paid_amount = $request->input('paid_amount');
            $invoice->created_by = $user_id;
            $invoice->paid_amount = $request->input('paid_amount');
            $invoice->discount = $request->input('discount');
           $invoice->amount = collect($request->input('amount'))->sum();

            $invoice->is_paid = 1;
            $invoice->save();
    
            // Updating the booking_invoice_number field
            $invoice->booking_invoice_number = 'INV100' . $invoice->id;
            if ($request->booking_type_id == 86) {
                $invoice->bill_token = 'TP_TKN' . $invoice->id;
            } elseif ($request->booking_type_id == 85) {
                $invoice->bill_token = 'WL_TKN' . $invoice->id;
            } elseif ($request->booking_type_id == 84) {
                $invoice->bill_token = 'CN_TKN' . $invoice->id;
            }
            $invoice->save();
    
            $booking = Trn_Consultation_Booking::find($request->input('booking_id'));
            if ($booking) {
                $booking->is_billable = 1;
                $booking->save();
            }
      
            if ($request->booking_type_id == 86) {
                
        $payableAmounts = $request->input('amount');
        $paymentModes = $request->input('payment_mode');
        $depositTo = $request->input('deposit_to');
        $referenceNos = $request->input('reference_no');

        // Check if all arrays are not null and have the same number of elements
        if ($payableAmounts && $paymentModes && $depositTo && $referenceNos &&
            count($payableAmounts) === count($paymentModes) &&
            count($payableAmounts) === count($depositTo) &&
            count($payableAmounts) === count($referenceNos)) {
        
            foreach ($payableAmounts as $key => $value) {
                $invoicePayment = new Trn_Therapy_Booking_Invoice_Payment();
        
                $invoicePayment->consultation_booking_invoice_id = $invoice->id;
                $invoicePayment->paid_amount = $payableAmounts[$key];
                $invoicePayment->payment_mode = $paymentModes[$key];
                $invoicePayment->deposit_to = $depositTo[$key];
                $invoicePayment->reference_no = $referenceNos[$key];
        
                //Accounts Payable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'TP_TKN' . $booking->id,
            'account_ledger_id' => 1,
            'entity_id' => $request->patient_id,
            'debit' => array_sum($request->input('booking_fee')),
            'credit' => 0,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Therapy Booking Invoice Payment'
        ]);

        //Wellness Revenue
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'TP_TKN' . $booking->id,
            'account_ledger_id' => 86,
            'entity_id' => 0,
            'debit' => 0,
            'credit' => collect($request->input('amount'))->sum(),
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Therapy Booking Invoice Payment'
        ]);
        //Accounts Receivable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'TP_TKN' . $booking->id,
            'account_ledger_id' => 1,
            'entity_id' => $request->patient_id,
            'debit' => 0,
            'credit' => collect($request->input('amount'))->sum(),
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Therapy Booking Invoice Payment'
        ]);
        //Cash or Bank Account
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'TP_TKN' . $booking->id,
            'account_ledger_id' => 4,
            'entity_id' => $request->patient_id,
            'debit' => collect($request->input('amount'))->sum(),
            'credit' => 0,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Therapy Booking Invoice Payment'
        ]);

        
                $invoicePayment->save();
            }
        } else {
            // Handle error: Arrays are null or have different number of elements
        }
               
            } elseif ($request->booking_type_id == 85) {
            foreach ($request->input('amount') as $key => $value) {
            $invoicePayment = new Trn_Wellness_Booking_Invoice_Payment();
            $invoicePayment->consultation_booking_invoice_id = $invoice->id;
            $invoicePayment->paid_amount = $request->input('amount')[$key];
            $invoicePayment->payment_mode = $request->input('payment_mode')[$key];
            $invoicePayment->deposit_to = $request->input('deposit_to')[$key];
            $invoicePayment->reference_no = $request->input('reference_no')[$key];
            // Add other fields as needed
            $invoicePayment->save();
        }
                //Accounts Payable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'WL_TKN' . $booking->id,
            'account_ledger_id' => 1,
            'entity_id' => $request->patient_id,
            'debit' =>collect($request->input('amount'))->sum(),
            'credit' => 0,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Wellness Booking Invoice Payment'
        ]);

        //Wellness Revenue
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'WL_TKN' . $booking->id,
            'account_ledger_id' => 85,
            'entity_id' => 0,
            'debit' => 0,
            'credit' => collect($request->input('amount'))->sum(),
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Wellness Booking Invoice Payment'
        ]);
        //Accounts Receivable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'WL_TKN' . $booking->id,
            'account_ledger_id' => 1,
            'entity_id' => $request->patient_id,
            'debit' => 0,
            'credit' => collect($request->input('amount'))->sum(),
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Wellness Booking Invoice Payment'
        ]);
        //Cash or Bank Account
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'WL_TKN' . $booking->id,
            'account_ledger_id' => 4,
            'entity_id' => $request->patient_id,
            'debit' => collect($request->input('amount'))->sum(),
            'credit' => 0,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Wellness Booking Invoice Payment'
        ]);
                
            } 
            elseif ($request->booking_type_id == 84) {
$amounts = $request->input('amount');
if (!is_null($amounts) && is_array($amounts)) {
    foreach ($amounts as $key => $value) {
        $invoicePayment = new Trn_Consultation_Booking_Invoice_Payment();
        $invoicePayment->consultation_booking_invoice_id = $invoice->id;
        $invoicePayment->paid_amount = $amounts[$key];
        $invoicePayment->payment_mode = $request->input('payment_mode')[$key];
        $invoicePayment->deposit_to = $request->input('deposit_to')[$key];
        $invoicePayment->reference_no = $request->input('reference_no')[$key];
        // Add other fields as needed
        $invoicePayment->save();
    }
} else {
    // Handle the case where the 'amount' array is null or empty
    // For example, you can log an error message or return a response
}

                //Accounts Payable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'CN_TKN' . $invoice->id,
            'account_ledger_id' => 1,
            'entity_id' => $request->patient_id,
            'debit' => collect($request->input('amount'))->sum(),
            'credit' => 0,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $invoice->id,
            'narration' => 'Consultation Booking Invoice Payment'
        ]);

        // Consulting Revenue
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'CN_TKN' . $invoice->id,
            'account_ledger_id' => 84,
            'entity_id' => 0,
            'debit' => 0,
            'credit' => collect($request->input('amount'))->sum(),
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $invoice->id,
            'narration' => 'Consultation Booking Invoice Payment'
        ]);
        //Accounts Receivable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'CN_TKN' . $invoice->id,
            'account_ledger_id' => 1,
            'entity_id' => $request->patient_id,
            'debit' => 0,
            'credit' => collect($request->input('amount'))->sum(),
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $invoice->id,
            'narration' => 'Consultation Booking Invoice Payment'
        ]);
        //Cash or Bank Account
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'CN_TKN' . $invoice->id,
            'account_ledger_id' => 4,
            'entity_id' => $request->patient_id,
            'debit' => collect($request->input('amount'))->sum(),
            'credit' => 0,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $invoice->id,
            'narration' => 'Consultation Booking Invoice Payment'
        ]);
            }


   
            
            return redirect()->route('invoice-settlemnt.index')->with('success', 'Invoice created successfully');
        }
}
