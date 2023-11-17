<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trn_Prescription;
use App\Models\Trn_Prescription_Details;
use App\Models\Mst_Patient;
use App\Models\Trn_Patient_Family_Member;
use Dompdf\Dompdf;
use View;
use Dompdf\Options;
use Carbon\Carbon;
use App\Models\Trn_Consultation_Booking;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;


class TrnPrescriptionController extends Controller
{
    public function index()
    {
        try {
            $pageTitle = "Prescriptions";
            $patients = Mst_Patient::where('is_active', 1)->get();
            return view('prescription.index', compact('pageTitle', 'patients'));
        } catch (QueryException $e) {
            dd('Something went wrong.');
        }
    }

    public function list(Request $request)
    {
        try {
            // dd(2);
            // dd($request->all());
            $pageTitle = "Prescriptions";
            $patients = Mst_Patient::where('is_active', 1)->get();
            $prescriptions = Trn_Prescription::where('booking_id', $request->patient_booking_id)->with('Staff')->orderBy('created_at', 'desc')->get();
            $patient_id = $request->patient_id;
            $booking_id = $request->patient_booking_id;
            // dd($prescriptions[0]->staff->staff_code.'-'.$prescriptions[0]->created_at);
            return view('prescription.index', compact('pageTitle', 'prescriptions', 'patients', 'patient_id', 'booking_id'));
        } catch (QueryException $e) {
            dd('Something went wrong.');
        }
    }

    public function show($id)
    {
        try {
            // dd($id);
            $pageTitle = "Prescription Details";
            $basic_details = Trn_Prescription::join('trn_consultation_bookings', 'trn__prescriptions.Booking_Id', '=', 'trn_consultation_bookings.id')
                ->where('trn__prescriptions.prescription_id', $id)
                ->first();
            if ($basic_details->is_for_family_member == 0) {
                $patient_name = Mst_Patient::where('id', $basic_details->patient_id)->value('patient_name');
            } else {
                $patient_name = Trn_Patient_Family_Member::where('id', $basic_details->family_member_id)->value('family_member_name');
            }

            $medicine_details = Trn_Prescription_Details::where('trn__prescription__details.priscription_id', $id)
                ->join('mst_medicines', 'trn__prescription__details.medicine_id', '=', 'mst_medicines.id')
                ->join('mst_master_values as med_type_medicine', 'mst_medicines.medicine_type', '=', 'med_type_medicine.id')
                ->join('mst_medicine_dosages', 'trn__prescription__details.medicine_dosage', '=', 'mst_medicine_dosages.medicine_dosage_id')
                ->join('mst_master_values as master_values', 'mst_medicines.medicine_type', '=', 'master_values.id')
                ->select(
                    'mst_medicines.medicine_name',
                    'med_type_medicine.master_value as medicine_type',
                    'mst_medicine_dosages.name as medicine_dosage',
                    'trn__prescription__details.remarks',
                    'trn__prescription__details.duration',
                    'trn__prescription__details.priscription_id as id',
                    'trn__prescription__details.prescription_details_id',
                )
                ->get();
            // dd($basic_details);
            return view('prescription.view', compact('pageTitle', 'basic_details','id', 'patient_name', 'medicine_details'));
        } catch (QueryException $e) {
            dd($e->getMessage());
            dd('Something went wrong.');
        }
    }

    public function print($id)
    {
        try {
            $pageTitle = "Prescription Details";
            $basic_details = Trn_Prescription::join('trn_consultation_bookings', 'trn__prescriptions.Booking_Id', '=', 'trn_consultation_bookings.id')
                ->where('trn__prescriptions.prescription_id', $id)
                ->first();
            if ($basic_details->is_for_family_member == 0) {
                $patient_name = Mst_Patient::where('id', $basic_details->patient_id)->value('patient_name');
            } else {
                $patient_name = Trn_Patient_Family_Member::where('id', $basic_details->family_member_id)->value('family_member_name');
            }

            $medicine_details = Trn_Prescription_Details::where('trn__prescription__details.priscription_id', $id)
                ->join('mst_medicines', 'trn__prescription__details.medicine_id', '=', 'mst_medicines.id')
                ->join('mst_master_values as med_type_medicine', 'mst_medicines.medicine_type', '=', 'med_type_medicine.id')
                ->join('mst_medicine_dosages', 'trn__prescription__details.medicine_dosage', '=', 'mst_medicine_dosages.medicine_dosage_id')
                ->join('mst_master_values as master_values', 'mst_medicines.medicine_type', '=', 'master_values.id')
                ->select(
                    'mst_medicines.medicine_name',
                    'med_type_medicine.master_value as medicine_type',
                    'mst_medicine_dosages.name as medicine_dosage',
                    'trn__prescription__details.remarks',
                    'trn__prescription__details.duration',
                    'trn__prescription__details.priscription_id as id',
                    'trn__prescription__details.prescription_details_id',
                )
                ->get();
            $dompdf = new Dompdf();
            $view = View::make('prescription.print_prescription', ['pageTitle' => $pageTitle,'basic_details' => $basic_details, 'patient_name' => $patient_name, 'medicine_details' => $medicine_details]);
            $html = $view->render();
            // Load HTML content from a template or dynamically generate it based on $data
            // $html = '<html>HIKSLQW OIDJQ WOIJ D UHWEN</html>'; // You can generate HTML content here based on $data

            // Set PDF options if needed
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true); // Enable PHP code within the HTML (optional)
            $dompdf->setOptions($options);

            // Load HTML into Dompdf
            $dompdf->loadHtml($html);

            // Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Return the PDF content
            $pdfContent = $dompdf->output();
            // Pass your data as needed

            // You can also save the PDF to a file or store it in the database for future reference
            // For example, to save it to a file
            $pdfFilename = 'invoice.pdf';
            file_put_contents($pdfFilename, $pdfContent);

            // Return a response to the user for immediate viewing or downloading
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $pdfFilename . '"');
        } catch (QueryException $e) {
            dd($e->getMessage());
            dd('Something went wrong.');
        }
    }

    public function getPatientBookingIds($id)
    {
        try {
            $allBookings = Trn_Consultation_Booking::where('patient_id', $id)->select('booking_reference_number', 'id')->get();
            $data = [];
            foreach ($allBookings as $bookings) {
                $data[$bookings->id] = $bookings->booking_reference_number;
            }
            // dd($data);
            return response()->json($data);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
            return redirect()->route('medicine.sales.invoices.index')->with('error', 'Something went wrong');
        }
    }

    // public function listCancel($id)
    // {
    //     try {
    //         // dd(2);
    //         // dd($request->all());
    //         $pageTitle = "Prescriptions";
    //         $patients = Mst_Patient::where('is_active', 1)->get();
    //         $prescriptions = Trn_Prescription::where('booking_id', $id)->with('Staff')->orderBy('created_at', 'desc')->get();
    //         $patient_id = $request->patient_id;
    //         $booking_id = $id;
    //         // dd($prescriptions[0]->staff->staff_code.'-'.$prescriptions[0]->created_at);
    //         return view('prescription.index', compact('pageTitle', 'prescriptions', 'patients', 'patient_id', 'booking_id'));
    //     } catch (QueryException $e) {
    //         dd('Something went wrong.');
    //     }
    // }
}
