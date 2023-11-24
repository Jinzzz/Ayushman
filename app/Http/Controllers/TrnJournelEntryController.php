<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mst_Journel_Entry_Type;
use App\Models\Trn_Journel_Entry;
use App\Models\Trn_Journel_Entry_Details;
use App\Models\Mst_Staff;
use App\Models\Mst_Account_Ledger;
use App\Models\Mst_Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class TrnJournelEntryController extends Controller
{
    public function index(Request $request)
    {
        try {
            // dd($request->all());
            $pageTitle = "Journel Entries";
            $journel_entry_types = Mst_Journel_Entry_Type::get();
            $branches = Mst_Branch::get();
            // $query = Trn_Journel_Entry::join('trn__journel__entry__details', 'trn__journel__entries.journal_entry_id', 'trn__journel__entry__details.journal_entry_id')->orderBy('trn__journel__entries.created_at', 'desc')->with('journel_entry_type', 'branch');

            $query = Trn_Journel_Entry::orderBy('trn__journel__entries.created_at', 'desc')->with('journel_entry_type', 'branch');
            $branch_id = "0";
            $journel_entry_type_id = "0";
            $journel_number = "0";
            $from_date = "0";
            $to_date = "0";
            if (isset($request->branch_id) && !is_null($request->branch_id) && $request->branch_id != "null" && $request->branch_id != null) {
                $branch_id = $request->branch_id;
                $query->where('trn__journel__entries.branch_id', $request->branch_id);
            }

            if (isset($request->journel_entry_type_id) && !is_null($request->journel_entry_type_id) && $request->journel_entry_type_id != "null" && $request->journel_entry_type_id != null) {
                $journel_entry_type_id = $request->journel_entry_type_id;
                $query->where('trn__journel__entries.journel_entry_type_id', $request->journel_entry_type_id);
            }

            if (isset($request->journel_number) && !is_null($request->journel_number) && $request->journel_number != "null" && $request->journel_number != null) {
                $journel_number = $request->journel_number;
                $query->where('trn__journel__entries.journel_number', 'LIKE', "%{$request->journel_number}%");
            }

            if (isset($request->from_date) && !is_null($request->from_date) && $request->from_date != "null" && $request->from_date != null) {
                $from_date = $request->from_date;
                $journel_date_from = Carbon::parse($from_date)->format('Y-m-d');
            }

            if (isset($request->to_date) && !is_null($request->to_date) && $request->to_date != "null" && $request->to_date != null) {
                $to_date = $request->to_date;
                $journel_date_to = Carbon::parse($to_date)->format('Y-m-d');
            }

            // Apply the date range filter
            if (isset($journel_date_from) && isset($journel_date_to)) {
                $query->whereBetween('trn__journel__entries.journel_date', [$journel_date_from, $journel_date_to]);
            } elseif (isset($journel_date_from)) {
                $query->where('trn__journel__entries.journel_date', '>=', $journel_date_from);
            } elseif (isset($journel_date_to)) {
                $query->where('trn__journel__entries.journel_date', '<=', $journel_date_to);
            }


            $journel_entries = $query->get();
            return view('journel_entry.index', compact('branch_id', 'journel_entry_type_id', 'journel_number', 'pageTitle', 'from_date', 'to_date', 'journel_entries', 'branches', 'journel_entry_types'));
        } catch (QueryException $e) {
            dd('Something went wrong.');
        }
    }

    public function destroy($id)
    {
        try {
            $journel_entry = Trn_Journel_Entry::findOrFail($id);
            $journel_entry->is_deleted = 1;
            $journel_entry->deleted_by = 1;
            $journel_entry->deleted_at = Carbon::now();
            $journel_entry->save();
            $journel_entry->delete();
            return 1;
        } catch (QueryException $e) {
            dd($e->getMessage());
            return redirect()->route('medicine.dosage.index')->with('error', 'Something went wrong');
        }
    }

    public function create(Request $request)
    {
        try {
            $pageTitle = "Create Journel Entries";
            $journel_entry_types = Mst_Journel_Entry_Type::get();
            $ledgers = Mst_Account_Ledger::where('is_active',1)->get();
            return view('journel_entry.create', compact('pageTitle', 'ledgers', 'journel_entry_types'));
        } catch (QueryException $e) {
            return redirect()->route('journel.entry.index')->with('error', 'Something went wrong');
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'journel_entry_type_id' => ['required'],
                    'total_debit' => ['required'],
                    'ledger_id' => ['required'],
                    'total_credit' => ['required'],
                    'debit' => ['required'],
                    'credit' => ['required'],
                ],
                [
                    'journel_entry_type_id.required' => 'Journel entry type is required',
                    'total_debit.required' => 'Total debit is required',
                    'ledger_id.required' => 'Ledger is required',
                    'total_credit.required' => 'Total credit is required',
                    'debit.required' => 'Debit is required',
                    'credit.required' => 'Credit is required',
                ]
            );

            if (!$validator->fails()) {
                $user_id = 1;
                $user_details = Mst_Staff::where('staff_id', $user_id)->first();
                $branch_id = $user_details->branch_id;
                $financial_year_id = 1;
                $lastInsertedId = Trn_Journel_Entry::insertGetId([
                    'journel_entry_type_id' => $request->journel_entry_type_id,
                    'journel_number' => "JR00",
                    'journel_date' => Carbon::now(),
                    'financial_year_id' => $financial_year_id,
                    'branch_id' => $branch_id,
                    'notes' => $request->notes,
                    'total_debit' => $request->total_debit,
                    'total_credit' => $request->total_credit,
                    'is_deleted' => 0,
                    'created_by' => $user_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // updating with invoice number 
                $leadingZeros = str_pad('', 3 - strlen($lastInsertedId), '0', STR_PAD_LEFT);
                $journel_number = 'JR' . $leadingZeros . $lastInsertedId;

                // Update reference(invoice number) code
                Trn_Journel_Entry::where('journal_entry_id', $lastInsertedId)->update([
                    'updated_at' => Carbon::now(),
                    'journel_number' => $journel_number
                ]);

                $ledger_ids = $request->ledger_id;
                $descriptions = $request->description;
                $debits = $request->debit;
                $credits = $request->credit;
                $count = count($ledger_ids);
                // dd($count);

                for ($i = 1; $i < $count; $i++) {

                    Trn_Journel_Entry_Details::create([
                        'journal_entry_id' => $lastInsertedId,
                        'account_ledger_id' => $ledger_ids[$i],
                        'debit' => $debits[$i],
                        'credit' => $credits[$i],
                        'description' => $descriptions[$i],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
                $message = 'Journal entry has been successfully added.';
                return redirect()->route('journel.entry.index')->with('success', $message);
            } else {
                $messages = $validator->errors();
                return redirect()->route('journel.entry.create')->with('errors', $messages);
            }
        } catch (QueryException $e) {
            dd($e->getmessage());
            return redirect()->route('journel.entry.index')->with('success', 'Exception error');
        }
    }

    public function edit($id)
    {
        try {
            $pageTitle = "Edit Journel Entry";
            $journel_entry_types = Mst_Journel_Entry_Type::get();
            $ledgers = Mst_Account_Ledger::where('is_active',1)->get();
            $all_entry_details = Trn_Journel_Entry_Details::where('journal_entry_id', $id)->get();
            $journel_entries = Trn_Journel_Entry::leftJoin('trn__journel__entry__details', 'trn__journel__entries.journal_entry_id', 'trn__journel__entry__details.journal_entry_id')
                ->where('trn__journel__entries.journal_entry_id', $id)
                ->with('journel_entry_type', 'branch')
                ->first();
            // dd($journel_entries);
            return view('journel_entry.edit', compact('pageTitle','id', 'all_entry_details', 'journel_entries', 'journel_entry_types', 'ledgers'));
        } catch (QueryException $e) {
            return redirect()->route('journel.entry.index')->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'journel_entry_type_id' => ['required'],
                    'total_debit' => ['required'],
                    'ledger_id' => ['required'],
                    'total_credit' => ['required'],
                    'debit' => ['required'],
                    'credit' => ['required'],
                ],
                [
                    'journel_entry_type_id.required' => 'Journel entry type is required',
                    'total_debit.required' => 'Total debit is required',
                    'ledger_id.required' => 'Ledger is required',
                    'total_credit.required' => 'Total credit is required',
                    'debit.required' => 'Debit is required',
                    'credit.required' => 'Credit is required',
                ]
            );

            if (!$validator->fails()) {
                $user_id = 1;
                $user_details = Mst_Staff::where('staff_id', $user_id)->first();
                $branch_id = $user_details->branch_id;
                $financial_year_id = 1;
                $lastInsertedId = Trn_Journel_Entry::where('journal_entry_id', $request->hidden_id)->update([
                    'journel_entry_type_id' => $request->journel_entry_type_id,
                    'journel_date' => Carbon::now(),
                    'financial_year_id' => $financial_year_id,
                    'branch_id' => $branch_id,
                    'notes' => $request->notes,
                    'total_debit' => $request->total_debit,
                    'total_credit' => $request->total_credit,
                    'is_deleted' => 0,
                    'created_by' => $user_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                Trn_Journel_Entry_Details::where('journal_entry_id', $request->hidden_id)->delete();
                if (isset($request->ledger_id)) {
                    $ledger_ids = $request->ledger_id;
                    $descriptions = $request->description;
                    $debits = $request->debit;
                    $credits = $request->credit;
                    $count = count($ledger_ids);

                    for ($i = 0; $i < $count; $i++) {

                        Trn_Journel_Entry_Details::create([
                            'journal_entry_id' => $request->hidden_id,
                            'account_ledger_id' => $ledger_ids[$i],
                            'debit' => $debits[$i],
                            'credit' => $credits[$i],
                            'description' => $descriptions[$i],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }
                $message = 'Journal entry has been successfully updated.';
                return redirect()->route('journel.entry.index')->with('success', $message);
            } else {
                $messages = $validator->errors();
                dd($messages);
            }
        } catch (QueryException $e) {
            dd($e->getmessage());
            return redirect()->route('journel.entry.index')->with('success', 'Exception error');
        }
    }

    public function show($id)
    {
        try {
            $pageTitle = "Edit Journel Entry";
            $journel_entry_types = Mst_Journel_Entry_Type::get();
            $ledgers = Mst_Account_Ledger::get();
            $all_entry_details = Trn_Journel_Entry_Details::where('journal_entry_id', $id)->get();
            $journel_entries = Trn_Journel_Entry::join('trn__journel__entry__details', 'trn__journel__entries.journal_entry_id', 'trn__journel__entry__details.journal_entry_id')
                ->where('trn__journel__entries.journal_entry_id', $id)
                ->orderBy('trn__journel__entries.created_at', 'desc')
                ->with('journel_entry_type', 'branch')
                ->first();
            // dd($all_entry_details);
            return view('journel_entry.show', compact('pageTitle', 'all_entry_details', 'journel_entries', 'journel_entry_types', 'ledgers'));
        } catch (QueryException $e) {
            return redirect()->route('journel.entry.index')->with('error', 'Something went wrong');
        }
    }
}
