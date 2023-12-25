<?php

namespace App\Http\Controllers;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Patient;
use App\Models\Mst_Medicine;
use App\Models\Trn_Prescription;
use App\Models\Trn_Prescription_Details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
   public function wellnessBooking()
   {
 
        $pageTitle = "Wellness Booking";
        $query = Trn_Consultation_Booking::query();

        $consultations = $query->join('mst_Patients', 'trn_consultation_bookings.patient_id', '=', 'mst_Patients.id')
            ->join('mst_master_values', 'mst_Patients.id', '=', 'mst_master_values.id')
            ->where('trn_consultation_bookings.booking_type_id', '=', 85)
            ->select(
                'trn_consultation_bookings.id as consultation_id', // Alias trn_consultation_bookings.id
                'trn_consultation_bookings.*',
                'mst_Patients.id as patient_id', // Alias mst_Patients.id
                'mst_Patients.*',
                'mst_master_values.*'
            )
            ->get();
        return view('patientbookings.wellness', compact('pageTitle', 'consultations'));

   }
   public function consultationBooking()
   {
 
        $pageTitle = "Consultations Booking";
        $query = Trn_Consultation_Booking::query();

        $consultations = $query->join('mst_Patients', 'trn_consultation_bookings.patient_id', '=', 'mst_Patients.id')
            ->join('mst_master_values', 'mst_Patients.id', '=', 'mst_master_values.id')
            ->where('trn_consultation_bookings.booking_type_id', '=', 84)
            ->select(
                'trn_consultation_bookings.id as consultation_id', // Alias trn_consultation_bookings.id
                'trn_consultation_bookings.*',
                'mst_Patients.id as patient_id', // Alias mst_Patients.id
                'mst_Patients.*',
                'mst_master_values.*'
            )
            ->get();
        return view('patientbookings.consultation', compact('pageTitle', 'consultations'));

   }
   
   public function viewWellnessBooking($id)
   {
    $pageTitle = "Wellness Booking";
    $query = Trn_Consultation_Booking::query();

    $consultations = $query
        ->join('mst_Patients', 'trn_consultation_bookings.patient_id', '=', 'mst_Patients.id')
        ->join('mst_master_values', 'mst_Patients.id', '=', 'mst_master_values.id')
        ->join('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
        ->where('trn_consultation_bookings.booking_type_id', '=', 85)
        ->where('trn_consultation_bookings.id', $id)
        ->select('trn_consultation_bookings.id as consultation_id', // Alias trn_consultation_bookings.id
        'trn_consultation_bookings.*',
        'mst_Patients.id as patient_id', // Alias mst_Patients.id
        'mst_Patients.*',
        'mst_master_values.*',
        'mst_timeslots.*')
        ->first();
        

        $prescriptions = DB::table('trn__prescriptions')
        ->join('trn__prescription__details', 'trn__prescriptions.prescription_id', '=', 'trn__prescription__details.priscription_id')
        ->join('mst_medicines', 'trn__prescription__details.medicine_id', '=', 'mst_medicines.id')
        ->where('trn__prescriptions.Booking_Id', $id)
        ->select(
            'trn__prescriptions.*',
            'trn__prescription__details.*',
            'mst_medicines.medicine_name' // Add other columns from mst_medicines as needed
        )
        ->get();

    $medicines = Mst_Medicine::pluck('medicine_name','id');
    return view('patientbookings.viewwellness', compact('pageTitle', 'consultations','prescriptions','medicines'));
   }

   public function viewConsultationBooking($id)
   {
    $pageTitle = "Consultations Booking";
    $query = Trn_Consultation_Booking::query();

    $consultations = $query
        ->join('mst_Patients', 'trn_consultation_bookings.patient_id', '=', 'mst_Patients.id')
        ->join('mst_master_values', 'mst_Patients.id', '=', 'mst_master_values.id')
        ->join('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
        ->where('trn_consultation_bookings.booking_type_id', '=', 84)
        ->where('trn_consultation_bookings.id', $id)
        ->select('trn_consultation_bookings.id as consultation_id', // Alias trn_consultation_bookings.id
        'trn_consultation_bookings.*',
        'mst_Patients.id as patient_id', // Alias mst_Patients.id
        'mst_Patients.*',
        'mst_master_values.*',
        'mst_timeslots.*')
        ->first();
       
        

        $prescriptions = DB::table('trn__prescriptions')
                        ->join('trn__prescription__details', 'trn__prescriptions.prescription_id', '=', 'trn__prescription__details.priscription_id')
                        ->join('mst_medicines', 'trn__prescription__details.medicine_id', '=', 'mst_medicines.id')
                        ->where('trn__prescriptions.Booking_Id', $id)
                        ->select(
                            'trn__prescriptions.*',
                            'trn__prescription__details.*',
                            'mst_medicines.medicine_name' // Add other columns from mst_medicines as needed
                        )
                        ->get();
    
        $medicines = Mst_Medicine::pluck('medicine_name','id');
      
    return view('patientbookings.viewconsultation', compact('pageTitle', 'consultations','medicines','prescriptions'));
   }

   public function addMedicineConsultation(Request $request, $id)
{
    
    // Validate the form data
    $validatedData = $request->validate([
        'medicine' => 'required',
        'medicine_dosage' => 'required',
        'duration' => 'required',
        'doctor_id' => 'required',
        'consultation_id' => 'required',
        // Add more validation rules as needed
    ]);
    
    // Assuming you have a Medicine model
    $medicine = new Trn_Prescription([
        'Booking_Id' => $id,
        'doctor_id' => $request->input('doctor_id'),
        'duration' => $request->input('duration'),
    ]);
    $medicine->save();
 
    $medicineDetails = new Trn_Prescription_Details([
        'priscription_id' => $medicine->prescription_id, 
        'medicine_id' => $request->input('medicine'), 
        'duration' => $request->input('duration'),
        'medicine_dosage' => $request->input('medicine_dosage'),
        'remarks' => $request->input('remarks'),
    ]);
    $medicineDetails->save();
     
    // Redirect or perform any other action after saving
    return redirect()->back();
}


public function addMedicineWellness(Request $request, $id)
{
    
    // Validate the form data
    $validatedData = $request->validate([
        'medicine' => 'required',
        'medicine_dosage' => 'required',
        'duration' => 'required',
        'doctor_id' => 'required',
        'consultation_id' => 'required',
        // Add more validation rules as needed
    ]);
    
    // Assuming you have a Medicine model
    $medicine = new Trn_Prescription([
        'Booking_Id' => $id,
        'doctor_id' => $request->input('doctor_id'),
        'duration' => $request->input('duration'),
    ]);
    $medicine->save();
 
    $medicineDetails = new Trn_Prescription_Details([
        'priscription_id' => $medicine->prescription_id, 
        'medicine_id' => $request->input('medicine'), 
        'duration' => $request->input('duration'),
        'medicine_dosage' => $request->input('medicine_dosage'),
        'remarks' => $request->input('remarks'),
    ]);
    $medicineDetails->save();
     
    // Redirect or perform any other action after saving
    return redirect()->back();
}

}
