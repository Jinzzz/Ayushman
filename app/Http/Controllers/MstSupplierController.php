<?php

namespace App\Http\Controllers;

use App\Models\Mst_Supplier;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\TrnLedgerPosting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Mst_Account_Ledger;
use App\Models\Trn_Medicine_Purchase_Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MstSupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        try {
            $pageTitle = "Suppliers";
            $query = Mst_Supplier::query();
            if ($request->has('supplier_type_id')) {
                $supplierTypeId = $request->input('supplier_type_id');
                if (!empty($supplierTypeId)) {
                    $query->where('supplier_type_id', $supplierTypeId);
                }
            }
            if ($request->has('supplier_code')) {
                $query->where('supplier_code', 'LIKE', "%{$request->supplier_code}%");
            }
            if ($request->has('supplier_name')) {
                $query->where('supplier_name', 'LIKE', "%{$request->supplier_name}%");
            }
            if ($request->has('phone_1')) {
                $query->where('phone_1', 'LIKE', "%{$request->phone_1}%");
            }
            $suppliers = $query->latest('supplier_id')->get();
            // dd($suppliers);
            return view('supplier.index', compact('pageTitle', 'suppliers'));
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function create()
    {
  
            $pageTitle = "Create Supplier";
            $ledgers = Mst_Account_Ledger::pluck('ledger_name', 'id');
            $countries = DB::table('sys_countries')->get();
            return view('supplier.create', compact('pageTitle','ledgers','countries'));
    
    }

    public function store(Request $request)
    {
       
      
            $validator = Validator::make(
                $request->all(),
                [
                    'supplier_type_id' => 'required',
                    'supplier_name' => 'required',
                    'supplier_address' => 'required',
                    'supplier_city' => 'required',
                    'state' => 'required',
                    'country' => 'required',
                    'phone_1' => 'required|numeric',
                    'is_active' => 'required',
                ],
                [
                    'supplier_type_id.required' => 'Please select a supplier type.',
                    'supplier_name.required' => 'Supplier name is required.',
                    'supplier_address.required' => 'Address is required.',
                    'supplier_city.required' => 'City is required.',
                    'state.required' => 'State is required.',
                    'country.required' => 'Country is required.',
                    'phone_1.required' => 'Phone number is required.',
                    'is_active.required' => 'Supplier status is required.',
                ]
            );
            if (!$validator->fails()) {
                $is_active = $request->input('is_active') ? 1 : 0;
                $phone1Exists = Mst_Supplier::where('phone_1', $request->phone_1)
                    ->exists();

                // $phone2Exists = Mst_Supplier::where('phone_1', $request->phone_2)
                //     ->orWhere('phone_2', $request->phone_2)
                //     ->where('email', $request->email)
                //     ->exists();
                // $emailExists = Mst_Supplier::where('email', $request->email)->exists();

                if ($phone1Exists) {
                    return redirect()->route('supplier.create')->with('error', 'Failed to create.Supplier already exists with this Contact Number.');
                }
                $lastInsertedId = Mst_Supplier::insertGetId([
                    'supplier_code' => rand(50, 100),
                    'supplier_type_id' => $request->supplier_type_id,
                    'supplier_name' => $request->supplier_name,
                    'supplier_address' => $request->supplier_address,
                    'supplier_city' => $request->supplier_city,
                    'state' => $request->state,
                    'country' => $request->country,
                    'pincode' => $request->pincode,
                    'business_name' => $request->business_name ?? null,
                    'phone_1' => $request->phone_1,
                    'phone_2' => $request->phone_2 ?? null,
                    'email' => $request->email,
                    'website' => $request->website ?? null,
                    'GSTNO' => $request->GSTNO ?? null,
                    'credit_period' => $request->credit_period ?? null,
                    'credit_limit' => $request->credit_limit ?? null,
                    'opening_balance' => $request->opening_balance ?? null,
                    'opening_balance_type' => $request->opening_balance_type ?? null,
                    'account_ledger_id' => $request->account_ledger_id ?? null,
                    'terms_and_conditions' => $request->terms_and_conditions ?? null,
                    'opening_balance_date' => $request->opening_balance_date ?? null,
                    'is_active' =>  $is_active,

                ]);

                $leadingZeros = str_pad('', 3 - strlen($lastInsertedId), '0', STR_PAD_LEFT);
                $supplierCode = 'SUP' . $leadingZeros . $lastInsertedId;

                Mst_Supplier::where('supplier_id', $lastInsertedId)->update([
                    'supplier_code' => $supplierCode
                ]);

                // --------------------Creating Unique Ledger for this supplier -------------
                // Every supplier is assigned a unique ledger for accounting purposes. In cases now this creating supplier has an 
                // opening balance, ledger postings will be generated. It's important to note that, In this case, only one entry 
                // should occur in the ledger posting table based on the opening balance type(credit or debit).
                
                // Note : sys_account_group => 2(Liability), Account_sub_head =>52(Account Payable)
                $ledger_name = $request->supplier_name.$supplierCode;
                $supplier_ledger_id = Mst_Account_Ledger::insertGetId([
                    'account_sub_group_id' => 52,
                    'ledger_name' => $ledger_name,
                    'ledger_code' => 1,
                    'notes' => 'Ledger created on adding supplier',
                    'is_active' => 1,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                if ($request->opening_balance != null && $request->opening_balance_type !=null) {
                    // Account posting of opening balance 
                    if (isset($request->opening_balance_type) && $supplier_ledger_id != null) {
                        // 1=>Debit, 2=>Credit
                        if ($request->opening_balance_type == 1) {
                            $debit = $request->opening_balance;
                            $credit = 0;
                        }
                        if ($request->opening_balance_type == 2) {
                            $debit = 0;
                            $credit = $request->opening_balance;
                        }
                        // There is currently no information available regarding the master ID. 
                        // For the time being, I am saving the primary key of the supplier table as a temporary measure.
                        TrnLedgerPosting::insertGetId([
                            'posting_date' => Carbon::now()->toDateString(),
                            'master_id' => $lastInsertedId,
                            'account_ledger_id' => $supplier_ledger_id,
                            'debit' => $debit,
                            'credit' => $credit,
                            'branch_id' => 1,
                            'reference_no' => null,
                            'transaction_amount' => $request->opening_balance,
                            'narration' => "Supplier entry, if they paying opening balance",
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    } else {
                        return redirect()->route('supplier.create')->with('error', 'Please provide balance type');
                    }
                }
                return redirect()->route('supplier.index')->with('success', 'Supplier added successfully');
            } else {
                $messages = $validator->errors();
                return redirect()->route('supplier.create')->with('errors', $messages);
            }
      
    }


    public function edit($id)
    {
        try {
            $pageTitle = "Edit Supplier";
            $supplier = Mst_Supplier::findOrFail($id);
            $countries = DB::table('sys_countries')->get();
            $states = DB::table('sys_states')->get();
            return view('supplier.edit', compact('pageTitle', 'supplier','countries','states'));
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        // try {
             $update = Mst_Supplier::find($id);
            $validator = Validator::make(
                $request->all(),
                [
                    'supplier_type_id' => 'required',
                    'supplier_name' => 'required',
                    'supplier_address' => 'required',
                    'supplier_city' => 'required',
                    'state' => 'required',
                    'country' => 'required',
                   'phone_1' => 'required|numeric|unique:mst_suppliers,supplier_id,' . $update->id . ',supplier_id',
                    'is_active' => 'required',
                ],
                [
                    'supplier_type_id.required' => 'Please select a supplier type.',
                    'supplier_name.required' => 'Supplier name is required.',
                    'supplier_address.required' => 'Address is required.',
                    'supplier_city.required' => 'City is required.',
                    'state.required' => 'State is required.',
                    'country.required' => 'Country is required.',
                    'phone_1.required' => 'Phone number is required.',
                    'is_active.required' => 'Supplier status is required.',
                ]
            );
            
            if (!$validator->fails()) {
                $is_active = $request->input('is_active') ? 1 : 0;
                $update->update([
                    'supplier_type_id' => $request->supplier_type_id,
                    'supplier_name' => $request->supplier_name,
                    'supplier_address' => $request->supplier_address,
                    'supplier_city' => $request->supplier_city,
                    'state' => $request->state,
                    'country' => $request->country,
                    'pincode' => $request->pincode,
                    'business_name' => $request->business_name,
                    'phone_1' => $request->phone_1,
                    'phone_2' => $request->phone_2,
                    'email' => $request->email,
                    'website' => $request->website,
                    'GSTNO' => $request->GSTNO,
                    'credit_period' => $request->credit_period,
                    'credit_limit' => $request->credit_limit,
                    'opening_balance' => $request->opening_balance,
                    'opening_balance_type' => $request->opening_balance_type,
                    'account_ledger_id' => $request->account_ledger_id,
                    'terms_and_conditions' => $request->terms_and_conditions,
                    'opening_balance_date' => $request->opening_balance_date,
                    'is_active' =>  $is_active,
                ]);

                return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully');
            } else {
                $messages = $validator->errors();
                return redirect()->route('supplier.edit')->with('errors', $messages);
            }
        // } catch (QueryException $e) {
        //     return redirect()->route('supplier.index')->with('error', 'Something went wrong');
        // }
    }

    public function show($id)
    {
        try {
            $pageTitle = "View supplier details";
            $show = Mst_Supplier::findOrFail($id);
            $countries = DB::table('sys_countries')->get();
            $states = DB::table('sys_states')->get();
            $outstanding_sum = Trn_Medicine_Purchase_Invoice::where('supplier_id', $id)
                ->selectRaw('SUM(total_amount - paid_amount) as outstanding_sum')
                ->value('outstanding_sum');
        
                // If no outstanding amount found, set default value to 0
                $outstanding_sum = $outstanding_sum ?? 0;
    
            return view('supplier.show', compact('pageTitle', 'show', 'outstanding_sum','countries','states'));
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }
    
    public function destroy($id)
    {
        try {
            $supplier = Mst_Supplier::findOrFail($id);
            $supplier->delete();
            return 1;
        } catch (QueryException $e) {
            return 0;
        }
    }

    public function changeStatus($id)
    {
        try {
            $supplier = Mst_Supplier::findOrFail($id);
            $supplier->is_active = !$supplier->is_active;
            $supplier->save();
            return 1;
        } catch (QueryException $e) {
            return 0;
        }
    }

    public function getStates($countryId)
    {

        $states = DB::table('sys_states')->where('country_id', $countryId)->pluck('state_name', 'state_id');

        return response()->json($states);
    }
}
