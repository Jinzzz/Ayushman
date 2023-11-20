<?php

namespace App\Http\Controllers;

use App\Models\Mst_Branch;
use App\Models\Mst_Wellness;
use App\Models\Trn_Wellness_Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class MstWellnessController extends Controller
{
    public function index(Request $request)
    {
        try {
            $pageTitle = "Wellness";
            $branches = Mst_Branch::pluck('branch_name', 'branch_id');
            $query = Mst_Wellness::query();
            if ($request->has('wellness_name')) {
                $query->where('wellness_name', 'LIKE', "%{$request->wellness_name}%");
            }

            if ($request->has('branch_id')) {
                $query->where('branch_id', 'LIKE', "%{$request->branch_id}%");
            }

            $wellness = $query->orderBy('updated_at', 'desc')->get();
            return view('wellness.index', compact('pageTitle', 'wellness', 'branches'));
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function create()
    {
        try {
            $pageTitle = "Create Wellness";
            $branch = Mst_Branch::pluck('branch_name', 'branch_id');
            return view('wellness.create', compact('pageTitle', 'branch'));
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function store(Request $request)
    {
        dd($request->all());

        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'wellness_name' => 'required',
                    'wellness_description' => 'required',
                    'wellness_inclusions' => 'required',
                    'wellness_terms_conditions' => 'required',
                    'branch' => 'required|array',
                    'wellness_cost' => 'required|numeric',
                    'wellness_duration' => 'required',
                    'is_active' => 'required',
                    'wellness_image' => 'required',
                    'wellness_offer_price' => 'required|numeric',
                ],
                [
                    'wellness_name.required' => 'The wellness name is required.',
                    'wellness_description.required' => 'The wellness description is required.',
                    'wellness_inclusions.required' => 'The wellness inclusions field is required.',
                    'wellness_terms_conditions.required' => 'The wellness terms and conditions are required.',
                    'branch.required' => 'The branch field is required and must be an array.',
                    'wellness_cost.required' => 'The wellness cost is required.',
                    'wellness_cost.numeric' => 'The wellness cost must be a numeric value.',
                    'wellness_duration.required' => 'The wellness duration field is required.',
                    'is_active.required' => 'The is active field is required.',
                    'wellness_image.required' => 'The wellness image is required.',
                    'wellness_offer_price.required' => 'The wellness offer price is required.',
                    'wellness_offer_price.numeric' => 'The wellness offer price must be a numeric value.',
                ]
            );

            if (!$validator->fails()) {
                $is_active = $request->input('is_active') ? 1 : 0;

                if ($request->hasFile('wellness_image')) {
                    $filename = $request->wellness_image->getClientOriginalName();
                    $filename_without_ext = pathinfo($filename, PATHINFO_FILENAME);

                    $new_file_name = $filename_without_ext . time() . '.' . $request->wellness_image->getClientOriginalExtension();

                    $move = $request->wellness_image->move(public_path('assets/uploads/wellness_image'), $new_file_name);

                    // $wellness_image = url("assets/uploads/wellness_image/{$new_file_name}");
                }

                $wellness = Mst_Wellness::create([
                    'wellness_name' => $request->input('wellness_name'),
                    'wellness_description' => $request->input('wellness_description'),
                    'wellness_inclusions' => $request->input('wellness_inclusions'),
                    'wellness_terms_conditions' => $request->input('wellness_terms_conditions'),
                    'wellness_cost' => $request->input('wellness_cost'),
                    'wellness_duration' => $request->input('wellness_duration'),
                    'remarks' => $request->input('remarks'),
                    'is_active' => $is_active,
                    'wellness_image' => $new_file_name,
                    'offer_price' => $request->input('wellness_offer_price'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // Check if 'branch' is an array 
                if (is_array($request->input('branch'))) {
                    // Iterate through the selected branches and store them in trn_wellness_branches
                    foreach ($request->input('branch') as $branchId) {
                        Trn_Wellness_Branch::create([
                            'wellness_id' => $wellness->wellness_id, // Link to the newly created wellness record
                            'branch_id' => $branchId,
                        ]);
                    }
                } else {
                    // If 'branch' is a single value, store it in Mst_Wellness table 
                    $wellness->branch_id = $request->input('branch');
                    $wellness->save();
                }
                return redirect()->route('wellness.index')->with('success', 'Wellness added successfully');
            } else {
                $messages = $validator->errors();
                return redirect()->route('wellness.create')->with('errors', $messages);
            }
        } catch (QueryException $e) {
            dd($e->getMessage());
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function edit($wellness_id)
    {
        try {
            $pageTitle = "Edit Wellness";
            $wellness = Mst_Wellness::findOrFail($wellness_id);
            // $wellness->load('branches');
            $branch = Mst_Branch::pluck('branch_name', 'branch_id');
            return view('wellness.edit', compact('pageTitle', 'wellness', 'branch'));
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        dd($request->all());
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'wellness_name' => 'required',
                    'wellness_description' => 'required',
                    'wellness_inclusions' => 'required',
                    'wellness_terms_conditions' => 'required',
                    'branch' => 'required|array',
                    'wellness_cost' => 'required|numeric',
                    'wellness_duration' => 'required',
                    'is_active' => 'required',
                    'wellness_offer_price' => 'required|numeric',
                ],
                [
                    'wellness_name.required' => 'The wellness name is required.',
                    'wellness_description.required' => 'The wellness description is required.',
                    'wellness_inclusions.required' => 'The wellness inclusions field is required.',
                    'wellness_terms_conditions.required' => 'The wellness terms and conditions are required.',
                    'branch.required' => 'The branch field is required and must be an array.',
                    'wellness_cost.required' => 'The wellness cost is required.',
                    'wellness_cost.numeric' => 'The wellness cost must be a numeric value.',
                    'wellness_duration.required' => 'The wellness duration field is required.',
                    'is_active.required' => 'The is active field is required.',
                    'wellness_offer_price.required' => 'The wellness offer price is required.',
                    'wellness_offer_price.numeric' => 'The wellness offer price must be a numeric value.',
                ]
            );

            if (!$validator->fails()) {
                $is_active = $request->input('is_active') ? 1 : 0;

                $new_file_name = $request->saved_img;
                if ($request->hasFile('wellness_image')) {
                    $filename = $request->wellness_image->getClientOriginalName();
                    $filename_without_ext = pathinfo($filename, PATHINFO_FILENAME);

                    $new_file_name = $filename_without_ext . time() . '.' . $request->wellness_image->getClientOriginalExtension();

                    $move = $request->wellness_image->move(public_path('assets/uploads/wellness_image'), $new_file_name);

                    // $wellness_image = url("assets/uploads/wellness_image/{$new_file_name}");
                }
                
                $wellness = Mst_Wellness::find($id);
                // Update the wellness record with the new values
                $wellness->wellness_name = $request->input('wellness_name');
                $wellness->wellness_description = $request->input('wellness_description');
                $wellness->wellness_inclusions = $request->input('wellness_inclusions');
                $wellness->wellness_terms_conditions = $request->input('wellness_terms_conditions');
                $wellness->wellness_cost = $request->input('wellness_cost');
                $wellness->wellness_duration = $request->input('wellness_duration');
                $wellness->remarks = $request->input('remarks');
                $wellness->is_active = $is_active;
                $wellness->wellness_image = $new_file_name;
                $wellness->offer_price = $request->input('wellness_offer_price');
                $wellness->updated_at = Carbon::now();
                $wellness->save();

                // Delete existing records in trn_wellness_branches for this wellness
                Trn_Wellness_Branch::where('wellness_id', $wellness->wellness_id)->delete();

                // Iterate through the selected branches and store them in trn_wellness_branches
                foreach ($request->input('branch') as $branchId) {
                    Trn_Wellness_Branch::create([
                        'wellness_id' => $wellness->wellness_id,
                        'branch_id' => $branchId,
                    ]);
                }
                return redirect()->route('wellness.index')->with('success', 'Wellness updated successfully');
            } else {
                $messages = $validator->errors();
                return redirect()->route('wellness.edit',$id)->with('errors', $messages);
            }
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function show($id)
    {
        try {
            $pageTitle = "View wellness details";
            $show = Mst_Wellness::findOrFail($id);
            $branch = Mst_Branch::pluck('branch_name', 'branch_id');
            $branch_ids = Trn_Wellness_Branch::where('wellness_id',$id)->pluck('branch_id');
            return view('wellness.show', compact('pageTitle', 'show', 'branch','branch_ids'));
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function destroy($wellness_id)
    {
        try {
            $wellness = Mst_Wellness::findOrFail($wellness_id);
            $wellness->delete();
            return 1;
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }


    public function changeStatus(Request $request, $wellness_id)
    {
        try {
            $wellness = Mst_Wellness::findOrFail($wellness_id);
            $wellness->is_active = !$wellness->is_active;
            $wellness->save();
            return 1;
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }
}
