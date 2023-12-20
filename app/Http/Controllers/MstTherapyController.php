<?php

namespace App\Http\Controllers;
use App\Models\Mst_Therapy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MstTherapyController extends Controller
{
    public function index()
    {
        $pageTitle = "Therapies";
        $therapies = Mst_Therapy::latest()->get();
        return view('therapy.index' , compact('pageTitle','therapies'));
    }

    public function create()
    {
        $pageTitle = "Create Therapy";
        return view('therapy.create',compact('pageTitle'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'therapy_name' => 'required|unique:mst_therapies',
            'therapy_cost' => 'required|numeric',
            'is_active' => 'required',
        ]);
    
        $is_active = $request->input('is_active') ? 1 : 0;
    
        // Check for duplicate entry
        $existingTherapy = Mst_Therapy::where('therapy_name', $request->input('therapy_name'))->first();
    
        if ($existingTherapy) {
            return redirect()->route('therapy.index')->with('error', 'Therapy with the same name already exists');
        }
    
        // If no duplicate, proceed with saving the new record
        $therapy = new Mst_Therapy();
        $therapy->therapy_name = $request->input('therapy_name');
        $therapy->therapy_cost = $request->input('therapy_cost');
        $therapy->remarks = $request->input('remarks');
        $therapy->is_active = $is_active;
        $therapy->save();
    
        return redirect()->route('therapy.index')->with('success', 'Therapy added successfully');
    }
    

    public function edit($id)
    {
        $pageTitle = "Edit Therapy";
        $therapy = Mst_Therapy::findOrFail($id);
        return view('therapy.edit',compact('pageTitle','therapy'));

    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'therapy_name' => 'required|unique:mst_therapies,therapy_name,' . $id,
            'therapy_cost' => 'required|numeric',
            'remarks' => 'required',
        ]);
    
        $is_active = $request->input('is_active') ? 1 : 0;
    
        $therapy = Mst_Therapy::findOrFail($id);
        $therapy->therapy_name = $request->input('therapy_name');
        $therapy->therapy_cost = $request->input('therapy_cost');
        $therapy->remarks = $request->input('remarks');
        $therapy->is_active = $is_active;
        $therapy->save();
    
        return redirect()->route('therapy.index')->with('success', 'Therapy updated successfully');
    }
    

    public function destroy($id)
    {
        $therapy = Mst_Therapy::findOrFail($id);
        $therapy->delete();

        return 1;

        return redirect()->route('therapy.index')->with('success','Therapy deleted successfully'); 

    }

    public function changeStatus(Request $request, $id)
    {
        $therapy = Mst_Therapy::findOrFail($id);

        $therapy->is_active = !$therapy->is_active;
        $therapy->save();
        return 1;

        return redirect()->back()->with('success','Status changed successfully'); 
    }
}
