<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Mst_Qualification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MstQualificationController extends Controller
{

    public function index()
    {
        try {
            $pageTitle = "Qualifications";
            $qualifications = Mst_Qualification::orderBy('created_at', 'desc')->get();
            return view('qualifications.index', compact('pageTitle', 'qualifications'));
        } catch (QueryException $e) {
            // Handle the exception, you can log it or display an error message
            return redirect()->route('home')->with('error', 'An error occurred while fetching qualifications.');
        }
    }


    public function create()
    {
        try {
            $pageTitle = "Add Qualifications";
            return view('qualifications.create', compact('pageTitle'));
        } catch (QueryException $e) {
            // Handle the exception, you can log it or display an error message
            return redirect()->route('home')->with('error', 'An error occurred while fetching qualifications.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'qualification' => ['required'],
                    'is_active' => ['required'],
                ],
                [
                    'qualification.required' => 'Qualification field is required',
                    'is_active.required' => 'Qualification status is required',
                ]
            );

            if (!$validator->fails()) {
                if (isset($request->hidden_id)) {
                    Mst_Qualification::where('qualification_id', $request->hidden_id)->update([
                        'name' => $request->qualification,
                        'is_active' => $request->is_active,
                        'updated_by' => Auth::id(),
                        'updated_at' => Carbon::now(),
                    ]);
                    $message = 'Qualifications updated successfully';
                } else {
                    $checkExists = Mst_Qualification::where('name',$request->qualification)->first();
                    if($checkExists){
                        return redirect()->route('qualifications.index')->with('exists', 'This qualification is aready exists.');
                    }else{
                        Mst_Qualification::create([
                            'name' => $request->qualification,
                            'is_active' => $request->is_active,
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id(),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                        $message = 'Qualifications added successfully';
                    }
                    
                }
                return redirect()->route('qualifications.index')->with('success', $message);
            } else {
                $messages = $validator->errors();

                // print_r($messages);die();
                return redirect()->route('qualifications.create')->with('error', $messages);
            }
        } catch (QueryException $e) {
            return redirect()->route('qualifications.create')->with('error', 'An error occurred while processing the request.');
        }
    }


    public function destroy($id)
    {
        try {
            $qualification = Mst_Qualification::findOrFail($id);
            $qualification->deleted_by = Auth::id();
            $qualification->save();
            $qualification->delete();
            return 1;
            return redirect()->route('qualifications.index')->with('success', 'Deleted successfully');
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'An error occurred while fetching qualifications.');
        }
    }

    public function edit($id)
    {
        $pageTitle = "Edit Qualifications";
        $qualification = Mst_Qualification::findOrFail($id);
        return view('qualifications.create', compact('pageTitle', 'qualification'));
    }

    public function changeStatus($id)
    {
        $qualification = Mst_Qualification::findOrFail($id);
    
        $qualification->is_active = !$qualification->is_active;
        $qualification->save();
    
        return redirect()->back()->with('success','Status changed successfully');
    }
}
