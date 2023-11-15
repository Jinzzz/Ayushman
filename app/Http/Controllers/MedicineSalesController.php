<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Mst_Branch;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Medicine;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Patient;
use App\Models\Mst_Tax_Group_Included_Taxes;
use App\Models\Mst_Tax;
use App\Models\Trn_Medicine_Sales_Invoice;
use App\Models\Trn_Medicine_Sales_Invoice_Details;
use App\Models\Mst_Account_Ledger;
use App\Models\Mst_Staff;
use App\Models\Mst_User;
use App\Models\Trn_Medicine_Stock;
use Dompdf\Dompdf;
use View;
use Dompdf\Options;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;



class MedicineSalesController extends Controller
{
    public function index()
    {
        try {
            $pageTitle = "Medicine Sales Invoice";
            $medicineSalesInvoice = Trn_Medicine_Sales_Invoice::with('Staff', 'Branch')->orderBy('created_at', 'desc')->get();
            return view('medicine_sales_invoice.index', compact('pageTitle', 'medicineSalesInvoice'));
        } catch (QueryException $e) {
            dd($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            $pageTitle = "Create Medicine Sales Invoice";
            $patients = Mst_Patient::where('is_active', 1)->get();
            $medicines = Mst_Medicine::where('item_type', 8)->get();
            $paymentType = Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id');
            $user_id = 1;
            $staff_id = Mst_User::where('user_id', $user_id)->pluck('staff_id');
            $discount_percentage = Mst_Staff::where('staff_id', $staff_id)->pluck('max_discount_value');
            $branches = Mst_Branch::where('is_active', 1)->get();
            return view('medicine_sales_invoice.create', compact('pageTitle', 'paymentType', 'discount_percentage', 'medicines', 'patients', 'branches'));
        } catch (QueryException $e) {
            return redirect()->route('medicine.sales.invoices.index')->with('error', 'Something went wrong');
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
            return response()->json($data);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
            return redirect()->route('medicine.sales.invoices.index')->with('error', 'Something went wrong');
        }
    }

    public function getMedicineBatches($id)
    {
        try {
            $medicine_batch_details = Mst_Medicine::join('trn_medicine_stocks', 'mst_medicines.id', '=', 'trn_medicine_stocks.medicine_id')
                ->join('mst_units', 'mst_medicines.unit_id', '=', 'mst_units.id')
                ->join('mst_master_values as med_type', 'mst_medicines.medicine_type', '=', 'med_type.id')
                ->select(
                    'trn_medicine_stocks.stock_id',
                    'trn_medicine_stocks.current_stock',
                    'trn_medicine_stocks.medicine_id',
                    'trn_medicine_stocks.batch_no',
                    'trn_medicine_stocks.mfd',
                    'trn_medicine_stocks.expd',
                    'mst_medicines.reorder_limit',
                    'mst_medicines.unit_id',
                    'mst_medicines.unit_price',
                    'mst_units.unit_name',
                    'mst_units.unit_short_name',
                    'med_type.master_value as medicine_type'
                )
                ->where('trn_medicine_stocks.medicine_id', $id)
                ->where('trn_medicine_stocks.expd', '>', Carbon::now())
                ->where('trn_medicine_stocks.current_stock', '!=', 0)
                ->where('mst_medicines.id', $id)
                ->where('mst_medicines.item_type', 8)
                ->where('mst_medicines.is_active', 1)
                ->where('mst_units.is_active', 1)
                ->get();

            // Calculate the total tax rate using SQL query
            $medicine_details = Mst_Medicine::where('id', $id)->first();
            $included_tax_ids = Mst_Tax_Group_Included_Taxes::where('tax_group_id', $medicine_details->tax_id)->pluck('included_tax')->toArray();
            $rate = Mst_Tax::whereIn('id', $included_tax_ids)->pluck('tax_rate')->toArray();
            $total_tax_rate = array_sum($rate);

            $data = [];
            foreach ($medicine_batch_details as $medicine_batch) {
                $data[] = [
                    'id' => $medicine_batch->stock_id,
                    'medicine_id' => $id,
                    'medicine_batch_number' => $medicine_batch->batch_no,
                    'medicine_type' => $medicine_batch->medicine_type,
                    'medicine_mfd' => $medicine_batch->mfd,
                    'medicine_expd' => $medicine_batch->expd,
                    'medicine_current_stock' => $medicine_batch->current_stock,
                    'medicine_reorder_limit' => $medicine_batch->reorder_limit,
                    'medicine_unit' => $medicine_batch->unit_name,
                    'medicine_unit_price' => $medicine_batch->unit_price,
                    'medicine_tax_rate' => $total_tax_rate,
                ];
            }
            // dd($data);

            return response()->json(['data' => $data]);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    // ledgers 
    public function getLedgerNames(Request $request)
    {
        // Retrieve the selected payment mode from the request
        $paymentMode = $request->input('payment_mode');

        // Set the default sub group id to 44 (for Card or Bank)
        $subGroupId = 44;

        // If the payment mode is 'Cash', set the sub group id to 45
        if ($paymentMode == '117') {
            $subGroupId = 45;
        }

        //  if the payment mode is through  Card or Bank set the sub group id to 44
        if ($paymentMode == '118') {
            $subGroupId = 44;
        }

        if ($paymentMode == '119') {
            // If the payment mode is UPI, set the sub group id to 51
            $subGroupId = 51;
        }

        // Perform a query to fetch ledger names based on the selected payment mode
        $ledgerNames = Mst_Account_Ledger::where('account_sub_group_id', $subGroupId)
            ->pluck('ledger_name', 'id');

        return response()->json($ledgerNames);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'patient_id' => ['required'],
                    'medicine_id' => ['required'],
                    'batch_no' => ['required'],
                    'quantity' => ['required'],
                    'unit_id' => ['required'],
                    'rate' => ['required'],
                    'amount' => ['required'],
                    'mfd' => ['required'],
                    'expd' => ['required'],
                    'sub_total_amount' => ['required'],
                    'total_tax_amount' => ['required'],
                    'total_amount' => ['required'],
                    'discount_amount' => ['required'],
                    'paid_amount' => ['required'],
                    'payment_mode' => ['required'],
                    'deposit_to' => ['required'],
                ],
                [
                    'patient_id.required' => 'Patient name is required',
                    'medicine_id.required' => 'Medicine is required',
                    'batch_no.required' => 'Batch is required',
                    'quantity.required' => 'Quantity is required',
                    'unit_id.required' => 'Unit is required',
                    'rate.required' => 'Rate is required',
                    'amount.required' => 'Amount is required',
                    'mfd.required' => 'Mfd is required',
                    'expd.required' => 'Exd is required',
                    'sub_total_amount.required' => 'Sub total is required',
                    'total_tax_amount.required' => 'Tax amount is required',
                    'total_amount.required' => 'Total amount is required',
                    'discount_amount.required' => 'Discount amount is required',
                    'paid_amount.required' => 'Paid amount is required',
                    'payment_mode.required' => 'Payment mode is required',
                    'deposit_to.required' => ' Ledger is required',

                ]
            );

            if (!$validator->fails()) {
                $medicines = $request->medicine_id;
                $count = count($medicines);
                if ($count <= 1) {
                    return redirect()->route('medicine.sales.invoices.create')->with('error', 'Please add atleast one medicine');
                }

                $user_id = 1;
                $user_details = Mst_Staff::where('staff_id', $user_id)->first();
                $branch_id = $user_details->branch_id;
                $financial_year_id = 1;
                $lastInsertedId = Trn_Medicine_Sales_Invoice::insertGetId([
                    'sales_invoice_number' => "MSI00GP",
                    'patient_id' => $request->patient_id,
                    'booking_id' => $request->patient_booking_id,
                    'invoice_date' => Carbon::now(),
                    'branch_id' => $branch_id,
                    'sales_person_id' => $user_id,
                    'notes' => $request->notes,
                    'terms_and_conditions' => $request->terms_condition,
                    'sub_total' => $request->sub_total_amount,
                    'total_tax_amount' => $request->total_tax_amount,
                    'total_amount' => $request->total_amount,
                    'discount_amount' => $request->discount_amount,
                    'payable_amount' => $request->paid_amount,
                    'financial_year_id' => $financial_year_id,
                    'deposit_to' => $request->deposit_to,
                    'payment_mode' => $request->payment_mode,
                    'is_deleted' => 0,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // updating with invoice number 
                $leadingZeros = str_pad('', 3 - strlen($lastInsertedId), '0', STR_PAD_LEFT);
                $newMedSaleInvoiceNo = 'MSI' . $leadingZeros . $lastInsertedId;

                // Update reference(invoice number) code
                Trn_Medicine_Sales_Invoice::where('sales_invoice_id', $lastInsertedId)->update([
                    'updated_at' => Carbon::now(),
                    'sales_invoice_number' => $newMedSaleInvoiceNo
                ]);

                $medicines = $request->medicine_id;
                $batches = $request->batch_no;
                $quantities = $request->quantity;
                $rates = $request->rate;
                $amounts = $request->amount;

                $mf_dates = $request->mfd;
                $exp_dates = $request->mfd;
                $single_tax_amounts = $request->single_tax_amount;
                // $current_stocks = $request->current - stock;
                $count = count($medicines);
                // dd($count);

                for ($i = 1; $i < $count; $i++) {
                    $mf_date = Carbon::parse($mf_dates[$i])->format('Y-m-d');
                    $exp_date = Carbon::parse($exp_dates[$i])->format('Y-m-d');
                    $unit_id = Mst_Medicine::where('id', $medicines[$i])->first();
                    if ($batches[$i] != null) {
                        Trn_Medicine_Sales_Invoice_Details::create([
                            'sales_invoice_id' => $lastInsertedId,
                            'medicine_id' => $medicines[$i],
                            'medicine_unit_id' => $unit_id->unit_id,
                            'batch_id' => $batches[$i],
                            'quantity' => $quantities[$i],
                            'rate' => $rates[$i],
                            'amount' => $amounts[$i],
                            'expiry_date' => $exp_date,
                            'manufactured_date' => $mf_date,
                            'med_quantity_tax_amount' => $single_tax_amounts[$i],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }


                    // $remaining_stock = $current_stocks[$i] - $quantities[$i];
                    // Trn_Medicine_Stock::where('batch_no', $batches[$i])->where('medicine_id', $medicines[$i])->where('branch_id', $branch_id)->update([
                    //     'updated_at' => Carbon::now(),
                    //     'current_stock' => $remaining_stock
                    // ]);
                }
                $message = 'Medicine sales invoice details added successfully';
                return redirect()->route('medicine.sales.invoices.index')->with('success', $message);
            } else {
                $messages = $validator->errors();
                return redirect()->route('medicine.sales.invoices.create')->with('errors', $messages);
            }
        } catch (QueryException $e) {
            return redirect()->route('medicine.sales.invoices.index')->with('success', 'Exception error');
        }
    }

    public function generatePDF($id)
    {
        $data = [];
        $medicine_sale_invoices = Trn_Medicine_Sales_Invoice::where('sales_invoice_id', $id)->first();
        $medicine_sale_details = Trn_Medicine_Sales_Invoice_Details::where('sales_invoice_id', $id)->with('Unit', 'Medicine')->get();
        // Create a Dompdf instance
        $data['sales_invoice_number'] = $medicine_sale_invoices->sales_invoice_number;
        $data['invoice_date'] = $medicine_sale_invoices->invoice_date;
        $data['sub_total'] = $medicine_sale_invoices->sub_total;
        $data['tax_amount'] = $medicine_sale_invoices->total_tax_amount;
        $data['total_amount'] = $medicine_sale_invoices->total_amount;
        $data['discount_amount'] = $medicine_sale_invoices->discount_amount;
        $data['payable_amount'] = $medicine_sale_invoices->payable_amount;

        if ($medicine_sale_invoices->patient_id == 0) {
            $data['patient_name'] = "Guest Patient";
            $data['patient_code'] = "Guest Patient";
        } else {
            $patient_details = Mst_Patient::where('id', $medicine_sale_invoices->patient_id)->first();
            $data['patient_name'] = $patient_details->patient_name;
            $data['patient_code'] = $patient_details->patient_code;
        }
        $dompdf = new Dompdf();
        $view = View::make('medicine_sales_invoice.print_invoice', ['data' => $data, 'medicine_sale_details' => $medicine_sale_details]);
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
    }

    // delete function 
    public function destroy($id)
    {
        try {
            $med_sales_invoice = Trn_Medicine_Sales_Invoice::where('sales_invoice_id', $id)->first();
            if ($med_sales_invoice) {
                $med_sales_invoice->deleted_by = 1;
                $med_sales_invoice->save(); // Update the 'deleted_by' attribute
                $med_sales_invoice->delete(); // Delete the record
            }
            return 1;
        } catch (QueryException $e) {
            return redirect()->route('medicine.sales.invoices.index')->with('error', 'Something went wrong');
        }
    }
    public function show($id)
    {
        try {
            $pageTitle = "View Medicine Sales Details";
            $patients = Mst_Patient::where('is_active', 1)->get();
            $medicines = Mst_Medicine::where('item_type', 8)->get();
            $paymentType = Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id');
            $medicine_sale_invoices = Trn_Medicine_Sales_Invoice::where('sales_invoice_id', $id)->first();
            $medicine_sale_details = Trn_Medicine_Sales_Invoice_Details::where('sales_invoice_id', $id)->get();
            if (!$medicine_sale_invoices) {
                // Handle the case where the ledger with the given ID doesn't exist
                return redirect()->route('medicine.sales.invoices.view')->with('error', 'Data not found');
            }
            $count_details_row = count($medicine_sale_details);
            $booking_id = 0;
            if ($medicine_sale_invoices->patient_id == 0) {
                $booking_id = "No booking ID";
            } else {
                // dd($medicine_sale_invoices->patient_id);
                $booking_details = Trn_Consultation_Booking::where('id', $medicine_sale_invoices->booking_id)->first();
                $booking_id = $booking_details->booking_reference_number;
            }

            $ledgerNames = Mst_Account_Ledger::find($medicine_sale_invoices->deposit_to);
            // dd($ledgerNames);
            $deposit_to = $ledgerNames->ledger_name;
            return view('medicine_sales_invoice.view', compact('deposit_to', 'pageTitle', 'patients', 'medicines', 'paymentType', 'medicine_sale_invoices', 'medicine_sale_details', 'count_details_row', 'booking_id'));
        } catch (QueryException $e) {
            return redirect()->route('medicine.sales.invoices.index')->with('error', 'Something went wrong');
        }
    }
    public function edit($id)
    {
        try {
            $pageTitle = "Edit Medicine Sales Details";
            $patients = Mst_Patient::where('is_active', 1)->get();
            $medicines = Mst_Medicine::where('item_type', 8)->get();
            $paymentType = Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id');
            $medicine_sale_invoices = Trn_Medicine_Sales_Invoice::where('sales_invoice_id', $id)->first();
            $medicine_sale_details = Trn_Medicine_Sales_Invoice_Details::where('sales_invoice_id', $id)->with('Unit')->get();
            if (!$medicine_sale_invoices) {
                // Handle the case where the ledger with the given ID doesn't exist
                return redirect()->route('medicine.sales.invoices.view')->with('error', 'Data not found');
            }
            $patient_booking_ids = Trn_Consultation_Booking::where('patient_id', $medicine_sale_invoices->patient_id)->select('booking_reference_number', 'id')->get();

            $ledgerNames = Mst_Account_Ledger::find($medicine_sale_invoices->deposit_to);
            $all_medicine_sale_details = [];
            foreach ($medicine_sale_details as $sale_details) {
                $batch_details = Trn_Medicine_Stock::where('batch_no', $sale_details->batch_id)->first();
                $medicine_details = Mst_Medicine::where('item_type', 8)->where('id', $sale_details->medicine_id)->first();
                $included_tax_ids = Mst_Tax_Group_Included_Taxes::where('tax_group_id', $medicine_details->tax_id)->pluck('included_tax')->toArray();
                $rate = Mst_Tax::whereIn('id', $included_tax_ids)->pluck('tax_rate')->toArray();
                $total_tax_rate = array_sum($rate);
                $single_tax_amount = ($medicine_details->unit_price * $total_tax_rate) / 100;

                $details = [
                    'medicine_id' => $sale_details->medicine_id,
                    'batch_id' => $sale_details->batch_id,
                    'quantity' => $sale_details->quantity,
                    'unit_name' => $sale_details->unit->unit_name,
                    'rate' => $sale_details->rate,
                    'amount' => $sale_details->amount,
                    'stock_id' => $batch_details->stock_id,
                    'current_stock' => $batch_details->current_stock,
                    'reorder_limit' => $medicine_details->reorder_limit,
                    'single_tax_rate' => $total_tax_rate,
                    'single_tax_amount' => $single_tax_amount,
                    'manufactured_date' => $sale_details->manufactured_date,
                    'expiry_date' => $sale_details->expiry_date,
                ];

                $all_medicine_sale_details[] = $details;
            }
            // dd($all_medicine_sale_details);
            $deposit_to = $ledgerNames->ledger_name;
            return view('medicine_sales_invoice.edit', compact('patient_booking_ids', 'pageTitle', 'patients', 'medicines', 'paymentType', 'medicine_sale_invoices', 'all_medicine_sale_details', 'medicine_sale_details', 'deposit_to'));
        } catch (QueryException $e) {
            dd($e->getMessage());
            return redirect()->route('medicine.sales.invoices.index')->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request)
    {
        try {

            $user_id = 1;
            $user_details = Mst_Staff::where('staff_id', $user_id)->first();
            $branch_id = $user_details->branch_id;
            $financial_year_id = 1;
            $get_deposite_to = Trn_Medicine_Sales_Invoice::where('sales_invoice_id', $request->hdn_id)->first();
            Trn_Medicine_Sales_Invoice::where('sales_invoice_id', $request->hdn_id)->update([
                'sales_invoice_number' => "MSI00GP",
                'patient_id' => $request->patient_id,
                'booking_id' => $request->patient_booking_id,
                'invoice_date' => Carbon::now(),
                'branch_id' => $branch_id,
                'sales_person_id' => $user_id,
                'notes' => $request->notes,
                'terms_and_conditions' => $request->terms_condition,
                'sub_total' => $request->sub_total_amount,
                'total_tax_amount' => $request->total_tax_amount,
                'total_amount' => $request->total_amount,
                'discount_amount' => $request->discount_amount,
                'payable_amount' => $request->paid_amount,
                'financial_year_id' => $financial_year_id,
                'deposit_to' => $get_deposite_to->deposit_to,
                'payment_mode' => $get_deposite_to->payment_mode,
                'is_deleted' => 0,
                'created_by' => $user_id,
                'updated_by' => $user_id,
                'updated_at' => Carbon::now(),
            ]);


            $medicines = $request->medicine_id;
            $batches = $request->batch_no;
            $quantities = $request->quantity;
            $rates = $request->rate;
            $amounts = $request->amount;

            $mf_dates = $request->mfd;
            $exp_dates = $request->mfd;
            $single_tax_amounts = $request->single_tax_amount;
            // $current_stocks = $request->current - stock;
            $count = count($medicines);

            Trn_Medicine_Sales_Invoice_Details::where('sales_invoice_id', $request->hdn_id)->delete();

            for ($i = 0; $i < $count; $i++) {
                $mf_date = Carbon::parse($mf_dates[$i])->format('Y-m-d');
                $exp_date = Carbon::parse($exp_dates[$i])->format('Y-m-d');
                $unit_id = Mst_Medicine::where('id', $medicines[$i])->first();

                Trn_Medicine_Sales_Invoice_Details::create([
                    'sales_invoice_id' => $request->hdn_id,
                    'medicine_id' => $medicines[$i],
                    'medicine_unit_id' => $unit_id->unit_id,
                    'batch_id' => $batches[$i],
                    'quantity' => $quantities[$i],
                    'rate' => $rates[$i],
                    'amount' => $amounts[$i],
                    'expiry_date' => $exp_date,
                    'manufactured_date' => $mf_date,
                    'med_quantity_tax_amount' => $single_tax_amounts[$i],
                    'updated_at' => Carbon::now(),
                ]);
                // $remaining_stock = $current_stocks[$i] - $quantities[$i];
                // Trn_Medicine_Stock::where('batch_no', $batches[$i])->where('medicine_id', $medicines[$i])->where('branch_id', $branch_id)->update([
                //     'updated_at' => Carbon::now(),
                //     'current_stock' => $remaining_stock
                // ]);
            }

            $message = 'Medicine sales invoice details added successfully';
            return redirect()->route('medicine.sales.invoices.index')->with('success', $message);
        } catch (QueryException $e) {
            dd($e->getMessage());
            return redirect()->route('medicine.sales.invoices.index')->with('success', 'Exception error');
        }
    }
}
