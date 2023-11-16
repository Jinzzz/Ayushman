<?php

namespace App\Http\Controllers;

use App\Models\Mst_Supplier;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

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
        try {
            return view('supplier.create');
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([

                'supplier_type_id' => 'required',
                'supplier_name' => 'required',
                'supplier_address' => 'required',
                'supplier_city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'pincode' => 'required',
                'business_name' => 'required',
                'phone_1' => 'required|numeric',
                'phone_2' => 'required|numeric',
                'email' => 'required|email',
                'website' => 'required',
                'GSTNO' => 'required',
                'credit_period' => 'required',
                'credit_limit' => 'required',
                'opening_balance' => 'required',
                'opening_balance_type' => 'required',
                'is_active' => 'required',
                'account_ledger_id' => 'required',
                'terms_and_conditions' => 'required',
                'opening_balance_date' => 'required',
            ]);
            $is_active = $request->input('is_active') ? 1 : 0;

            $lastInsertedId = Mst_Supplier::insertGetId([

                'supplier_code' => rand(50, 100),
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

            $leadingZeros = str_pad('', 3 - strlen($lastInsertedId), '0', STR_PAD_LEFT);
            $supplierCode = 'SUP' . $leadingZeros . $lastInsertedId;

            Mst_Supplier::where('supplier_id', $lastInsertedId)->update([
                'supplier_code' => $supplierCode
            ]);

            return redirect()->route('supplier.index')->with('success', 'Supplier added successfully');
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }


    public function edit($id)
    {
        try {
            $pageTitle = "Edit Supplier";
            $supplier = Mst_Supplier::findOrFail($id);
            return view('supplier.edit', compact('pageTitle', 'supplier'));
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'supplier_type_id' => 'required',
                'supplier_name' => 'required',
                'supplier_address' => 'required',
                'supplier_city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'pincode' => 'required',
                'business_name' => 'required',
                'phone_1' => 'required|numeric',
                'phone_2' => 'required|numeric',
                'email' => 'required|email',
                'website' => 'required',
                'GSTNO' => 'required',
                'credit_period' => 'required',
                'credit_limit' => 'required',
                'opening_balance' => 'required',
                'opening_balance_type' => 'required',
                'account_ledger_id' => 'required',
                'terms_and_conditions' => 'required',
                'opening_balance_date' => 'required',

            ]);
            $is_active = $request->input('is_active') ? 1 : 0;

            $update = Mst_Supplier::find($id);
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
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function show($id)
    {
        try {
            $pageTitle = "View supplier details";
            $show = Mst_Supplier::findOrFail($id);
            return view('supplier.show', compact('pageTitle', 'show'));
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
}
