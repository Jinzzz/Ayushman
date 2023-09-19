<?php

namespace App\Http\Controllers;
use App\Models\Mst_External_Doctor;
use App\Models\Mst_Branch;
use Illuminate\Http\Request;

class MstExternalDoctorController extends Controller
{
    public function index(Request $request)
    {
    $pageTitle = "External Doctors";
    $externaldoctor =  Mst_External_Doctor::latest()->get();
    return view('externalDoctors.index', compact('pageTitle','externaldoctor'));
    }

    public function create()
    {
        $pageTitle = "Create External Doctor";
        return view('externalDoctors.create',compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_name' => 'required',
            'contact_no' => 'required|numeric|digits:10',
            // 'contact_email' => 'required|email',
            'address' => 'required',
            'remarks' => 'required',
            'is_active' => 'required',
        ]);
        
    $is_active = $request->input('is_active')? 1 : 0;
       
        $doctor = new Mst_External_Doctor();
        $doctor->doctor_name = $request->input('doctor_name');
        $doctor->contact_no = $request->input('contact_no');
        $doctor->contact_email = $request->input('contact_email');
        $doctor->address = $request->input('address');
        $doctor->remarks = $request->input('remarks');
        $doctor->is_active = $is_active; 
        $doctor->save();
    
        return redirect()->route('externaldoctors.index')->with('success','External doctor added successfully');
    }

    public function edit($id)
    {
        $pageTitle = "Edit External Doctor";
        $doctor = Mst_External_Doctor::findOrFail($id);
        return view('externalDoctors.edit', compact('pageTitle','doctor'));
    }

    public function update(Request $request ,$id)
    {
        $request->validate([
            'doctor_name' => 'required',
            'contact_no' => 'required',
            // 'contact_email' => 'required',
            'address' => 'required',
           
        ]);
        
        $is_active = $request->input('is_active')? 1 : 0;
       
        $doctor =  Mst_External_Doctor::findOrFail($id);
        $doctor->doctor_name = $request->input('doctor_name');
        $doctor->contact_no = $request->input('contact_no');
        $doctor->contact_email = $request->input('contact_email');
        $doctor->address = $request->input('address');
        $doctor->remarks = $request->input('remarks');
        $doctor->is_active = $is_active; 
        $doctor->save();
    
        return redirect()->route('externaldoctors.index')->with('success','External doctor updated successfully');
    }

    public function show($id)
    {
        $pageTitle = "External doctor details";
        $show =  Mst_External_Doctor::findOrFail($id);
        return view('externalDoctors.show',compact('pageTitle','show'));
    }

    public function destroy($id)
    {
        $doctor =  Mst_External_Doctor::findOrFail($id);
        $doctor->delete();

        return redirect()->route('externaldoctors.index')->with('success','External doctor deleted successfully');
    }

    public function changeStatus(Request $request, $id)
{
    $doctor = Mst_External_Doctor::findOrFail($id);

    $doctor->is_active = !$doctor->is_active;
    $doctor->save();

    return redirect()->back()->with('success','Status changed successfully');
}

}
