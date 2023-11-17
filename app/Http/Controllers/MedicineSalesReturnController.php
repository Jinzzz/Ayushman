<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Trn_Medicine_Sales_Return;
use App\Models\Mst_Branch;
use App\Models\Trn_Medicine_Sales_Return_Details;
use App\Models\Mst_Medicine;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Patient;
use App\Models\Trn_Medicine_Sales_Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Mst_Staff;
use App\Models\Mst_Tax_Group_Included_Taxes;
use App\Models\Mst_Tax;
use App\Models\Trn_Medicine_Stock;
use Dompdf\Dompdf;
use View;
use Dompdf\Options;

class MedicineSalesReturnController extends Controller
{
    public function index()
    {
        try {
            $pageTitle = "Medicine Sales Return";
            $purchaseReturn = Trn_Medicine_Sales_Return::with('Staff', 'Branch')->orderBy('created_at', 'desc')->get();
            return view('medicine_sales_return.index', compact('pageTitle', 'purchaseReturn'));
        } catch (QueryException $e) {
            dd('Something went wrong.');
        }
    }

    public function create(Request $request)
    {
        try {
            $pageTitle = "Create Medicine sales return";
            $patients = Mst_Patient::where('is_active', 1)->get();
            $medicines = Mst_Medicine::where('item_type', 8)->get();
            $paymentType = Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id');
            $branches = Mst_Branch::where('is_active', 1)->get();
            return view('medicine_sales_return.create', compact('pageTitle', 'paymentType', 'medicines', 'patients', 'branches'));
        } catch (QueryException $e) {
            return redirect()->route('medicine.sales.return.index')->with('error', 'Something went wrong');
        }
    }

    public function getPatientInvoiceIds($id)
    {
        try {
            $all_med_invoices = Trn_Medicine_Sales_Invoice::where('patient_id', $id)->select('sales_invoice_number', 'sales_invoice_id')->get();
            $data = [];
            foreach ($all_med_invoices as $med_invoice) {
                $data[$med_invoice->sales_invoice_id] = $med_invoice->sales_invoice_number;
            }
            return response()->json($data);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
            return redirect()->route('medicine.sales.return.index')->with('error', 'Something went wrong');
        }
    }

    public function store(Request $request)
    {
        // dd(199);
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
                ]
            );

            if (!$validator->fails()) {

                $medicines = $request->medicine_id;
                $count = count($medicines);
                if ($count <= 1) {
                    return redirect()->route('medicine.sales.return.create')->with('error', 'Please add atleast one medicine');
                }
                $user_id = 1;
                $user_details = Mst_Staff::where('staff_id', $user_id)->first();
                $branch_id = $user_details->branch_id;
                $message = 'Dummy message';
                $lastInsertedId = Trn_Medicine_Sales_Return::insertGetId([
                    'sales_return_no' => "MSR00GP",
                    'sales_invoice_id' => $request->patient_invoice_id,
                    'patient_id' => $request->patient_id,
                    'sales_person_id' => $user_id,
                    'return_date' => Carbon::now(),
                    'branch_id' => $branch_id,
                    'sub_total' => $request->sub_total_amount,
                    'total_tax' => $request->total_tax_amount,
                    'total_amount' => $request->total_amount,
                    'total_discount' => $request->discount_amount,
                    'notes' => $request->notes,
                    'is_deleted' => 0,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                // updating with return reference number 
                $leadingZeros = str_pad('', 3 - strlen($lastInsertedId), '0', STR_PAD_LEFT);
                $newMedSaleInvoiceNo = 'MSR' . $leadingZeros . $lastInsertedId;

                // Update reference code
                Trn_Medicine_Sales_Return::where('sales_return_id', $lastInsertedId)->update([
                    'updated_at' => Carbon::now(),
                    'sales_return_no' => $newMedSaleInvoiceNo
                ]);

                $medicines = $request->medicine_id;
                $batches = $request->batch_no;
                $quantities = $request->quantity;
                $rates = $request->rate;
                $amounts = $request->amount;
                $single_tax_amounts = $request->single_tax_amount;

                $count = count($medicines);
                for ($i = 1; $i < $count; $i++) {
                    $unit_id = Mst_Medicine::where('id', $medicines[$i])->first();
                    // dd($batches[$i]);
                    if ($batches[$i] != null) {
                        Trn_Medicine_Sales_Return_Details::create([
                            'sales_return_id' => $lastInsertedId,
                            'medicine_id' => $medicines[$i],
                            'quantity_unit_id' => $unit_id->unit_id,
                            'batch_id' => $batches[$i],
                            'quantity' => $quantities[$i],
                            'rate' => $rates[$i],
                            'amount' => $amounts[$i],
                            'tax_amount' => $single_tax_amounts[$i],
                            'discount' => $request->discount_amount,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }

                $message = 'Medicine sales return details added successfully';

                return redirect()->route('medicine.sales.return.index')->with('success', $message);
            } else {
                $messages = $validator->errors();
                dd($messages);
            }
        } catch (QueryException $e) {
            return redirect()->route('medicine.sales.return.index')->with('success', $e->getMessage());
        }
    }

    // delete function 
    public function destroy($id)
    {
        try {
            $med_sales_return = Trn_Medicine_Sales_Return::where('sales_return_id', $id)->first();
            if ($med_sales_return) {
                $med_sales_return->deleted_by = 1;
                $med_sales_return->save(); // Update the 'deleted_by' attribute
                $med_sales_return->delete(); // Delete the record
            }
            return 1;
        } catch (QueryException $e) {
            return redirect()->route('medicine.sales.return.index')->with('error', 'Something went wrong');
        }
    }

    public function show($id)
    {
        try {
            $pageTitle = "Edit Medicine Sales Return Details";
            $patients = Mst_Patient::where('is_active', 1)->get();
            $medicines = Mst_Medicine::where('item_type', 8)->get();
            $paymentType = Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id');
            $medicine_sale_invoices = Trn_Medicine_Sales_Return::where('sales_return_id', $id)->first();
            $medicine_sale_details = Trn_Medicine_Sales_Return_Details::where('sales_return_id', $id)->with('Unit')->get();
            if (!$medicine_sale_invoices) {
                return redirect()->route('medicine.sales.invoices.view')->with('error', 'Data not found');
            }
            $sales_invoice_number = Trn_Medicine_Sales_Invoice::where('sales_invoice_id', $medicine_sale_invoices->sales_invoice_id)
                ->first();
            // dd($medicine_sale_invoices->sales_invoice_id);
            return view('medicine_sales_return.view', compact('pageTitle', 'patients', 'medicines', 'paymentType', 'medicine_sale_invoices', 'medicine_sale_details'));
        } catch (QueryException $e) {
            return redirect()->route('medicine.sales.invoices.index')->with('error', 'Something went wrong');
        }
    }

    public function edit($id)
    {
        try {
            $pageTitle = "Edit Medicine Sales Return Details";
            $patients = Mst_Patient::where('is_active', 1)->get();
            $medicines = Mst_Medicine::where('item_type', 8)->get();
            $paymentType = Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id');
            $medicine_sale_invoices = Trn_Medicine_Sales_Return::where('sales_return_id', $id)->first();
            $medicine_sale_details = Trn_Medicine_Sales_Return_Details::where('sales_return_id', $id)->with('Unit')->get();

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
            if (!$medicine_sale_invoices) {
                // Handle the case where the ledger with the given ID doesn't exist
                return redirect()->route('medicine.sales.invoices.view')->with('error', 'Data not found');
            }
            // dd($medicine_sale_invoices);
            return view('medicine_sales_return.edit', compact('all_medicine_sale_details', 'pageTitle', 'patients', 'medicines', 'paymentType', 'medicine_sale_invoices', 'medicine_sale_details'));
        } catch (QueryException $e) {
            return redirect()->route('medicine.sales.invoices.index')->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request)
    {
        // dd($request->all());
        try {
            $user_id = 1;
            $user_details = Mst_Staff::where('staff_id', $user_id)->first();
            $branch_id = $user_details->branch_id;
            $message = 'Dummy message';
            $lastInsertedId = Trn_Medicine_Sales_Return::where('sales_return_id', $request->hdn_id)->update([
                'sales_return_no' => "MSR00GP",
                // 'sales_invoice_id' => $request->patient_invoice_id,
                'sales_invoice_id' => 1,
                'patient_id' => $request->patient_id,
                'sales_person_id' => $user_id,
                'return_date' => Carbon::now(),
                'branch_id' => $branch_id,
                'sub_total' => $request->sub_total_amount,
                'total_tax' => $request->total_tax_amount,
                'total_amount' => $request->total_amount,
                'total_discount' => $request->discount_amount,
                'notes' => $request->notes,
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
            $single_tax_amounts = $request->single_tax_amount;
            // dd($request->hdn_id);

            foreach ($medicines as $medicine) {
                $delete_condition_satisfying_all_rows = Trn_Medicine_Sales_Return_Details::where('sales_return_id', $request->hdn_id)->delete();
            }
            $count = count($medicines);
            for ($i = 0; $i < $count; $i++) {
                // dd($batches[$i]);
                $unit_id = Mst_Medicine::where('id', $medicines[$i])->first();

                Trn_Medicine_Sales_Return_Details::create([
                    'sales_return_id' => $request->hdn_id,
                    'medicine_id' => $medicines[$i],
                    'quantity_unit_id' => $unit_id->unit_id,
                    'batch_id' => $batches[$i],
                    'quantity' => $quantities[$i],
                    'rate' => $rates[$i],
                    'amount' => $amounts[$i],
                    'tax_amount' => $single_tax_amounts[$i],
                    'discount' => $request->discount_amount,
                    'updated_at' => Carbon::now(),
                ]);
            }

            $message = 'Medicine sales return details added successfully';
            return redirect()->route('medicine.sales.return.index')->with('success', $message);
        } catch (QueryException $e) {
            dd($e->getMessage());
            return redirect()->route('medicine.sales.return.index')->with('success', $e->getMessage());
        }
    }

    public function generatePDF($id)
    {
        $data = [];
        $medicine_sale_invoices = Trn_Medicine_Sales_Return::where('sales_return_id', $id)->first();
        $medicine_sale_details = Trn_Medicine_Sales_Return_Details::where('sales_return_id', $id)->with('Unit', 'Medicine')->get();
        // dd($medicine_sale_details);
        // Create a Dompdf instance
        $data['sales_invoice_number'] = $medicine_sale_invoices->sales_return_no;
        $data['invoice_date'] = $medicine_sale_invoices->return_date;
        $data['sub_total'] = $medicine_sale_invoices->sub_total;
        $data['tax_amount'] = $medicine_sale_invoices->total_tax;
        $data['total_amount'] = $medicine_sale_invoices->total_amount;
        $data['discount_amount'] = $medicine_sale_invoices->total_discount;

        if ($medicine_sale_invoices->patient_id == 0) {
            $data['patient_name'] = "Guest Patient";
            $data['patient_code'] = "Guest Patient";
        } else {
            $patient_details = Mst_Patient::where('id', $medicine_sale_invoices->patient_id)->first();
            $data['patient_name'] = $patient_details->patient_name;
            $data['patient_code'] = $patient_details->patient_code;
        }
        $dompdf = new Dompdf();
        $view = View::make('medicine_sales_return.print_invoice', ['data' => $data, 'medicine_sale_details' => $medicine_sale_details]);
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
}
