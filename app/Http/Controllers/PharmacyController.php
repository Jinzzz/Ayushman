<?php

namespace App\Http\Controllers;
use App\Models\Mst_Pharmacy;
use App\Models\Mst_Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pharmacies = Mst_Pharmacy::query()
            ->join('mst_branches', 'mst_pharmacies.branch', '=', 'mst_branches.branch_id');
    
        if ($request->filled('pharmacy_name')) {
            $pharmacies->where('mst_pharmacies.pharmacy_name', $request->input('pharmacy_name'));
        }
    
        $pharmacies = $pharmacies->get(['mst_pharmacies.*', 'mst_branches.*']);
    
        return view('pharmacy.index', compact('pharmacies'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = "Create Supplier";
        $branch = Mst_Branch::pluck('branch_name', 'branch_id');
        return view('pharmacy.create', compact('pageTitle','branch'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
 
        $request->validate([
            'pharmacy_name' => 'required|string|max:255',
            'branch' => 'required',
        ]);
    
        try {
            DB::beginTransaction();
            $existingPharmacy = Mst_Pharmacy::where('pharmacy_name', $request->input('pharmacy_name'))
                                ->where('branch', $request->input('branch'))
                                ->first();
    
            if ($existingPharmacy) {
                DB::rollback();
                return redirect()->route('pharmacy.index')->with('error', 'Pharmacy  already exists.');
            }
            $is_active = $request->input('status') ? 1 : 0;
            $pharmacy = new Mst_Pharmacy([
                'pharmacy_name' => $request->input('pharmacy_name'),
                'branch' => $request->input('branch'),
                'status' => $is_active,
            ]);
            $pharmacy->save();
    
            DB::commit();
            return redirect()->route('pharmacy.index')->with('success', 'Pharmacy Created Successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pharmacy.create')->with('error', 'An error occurred while saving pharmacy information. Please try again.');
        }
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = "Edit Pharmacy";
        $pharmacy = Mst_Pharmacy::where('id', $id)
                ->with('branch') 
                ->first();
    
        $branchs = Mst_Branch::get();
            
        return view('pharmacy.edit',compact('pageTitle','pharmacy','branchs'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'pharmacy_name' => 'required|string|max:255',
            'branch' => 'required',
        ]);

        $pharmacy = Mst_Pharmacy::findOrFail($id);
        $pharmacy->update([
        'pharmacy_name' => $request->input('pharmacy_name'),
        'branch' => $request->input('branch'),
        'status' => $request->input('status'),
        ]);
        return redirect()->route('pharmacy.index')->with('success', 'Pharmacy updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pharmacy = Mst_Pharmacy::find($id);
    
        if (!$pharmacy) {
            return response()->json(['message' => 'Record not found'], 404);
        }
    
        $pharmacy->delete();
        return 1;
    }
}
