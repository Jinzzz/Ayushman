<?php

namespace App\Http\Controllers;

use App\Models\Mst_Master_Value;
use Illuminate\Database\QueryException;
use App\Models\Mst_TimeSlot;
use Illuminate\Http\Request;
use carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class MstTimeSlotController extends Controller
{
    public function index(Request $request)
    {
        try {
            $pageTitle = "Timeslots";
            $timeslots = Mst_TimeSlot::orderBy('created_at', 'desc')->get();
            return view('timeslot.index', compact('pageTitle', 'timeslots'));
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'slot_name' => ['required'],
                    'time_from' => ['required'],
                    'time_to' => ['required'],
                    'is_active' => ['required'],
                ],
                [
                    'slot_name.required' => 'Slot name field is required',
                    'time_from.required' => 'Time from field is required',
                    'time_to.required' => 'Time to is required',
                    'is_active.required' => 'Timeslot status is required',
                ]
            );

            if (!$validator->fails()) {
                if (isset($request->hidden_id)) {
                    Mst_TimeSlot::where('id', $request->hidden_id)->update([
                        'slot_name' => $request->slot_name,
                        'time_from' => $request->time_from,
                        'time_to' => $request->time_to,
                        'is_active' => $request->is_active,
                        'updated_by' => 1,
                        'updated_at' => Carbon::now(),
                    ]);
                    $message = 'Timeslot updated successfully';
                } else {
                    $checkExists = Mst_TimeSlot::where('slot_name', $request->slot_name)->where('time_from', $request->time_from)->where('time_to', $request->time_to)->first();
                    if ($checkExists) {
                        return redirect()->route('timeslot.index')->with('failed', 'This timeslot is already exists.');
                    } else {
                        Mst_TimeSlot::create([
                            'slot_name' => $request->slot_name,
                            'time_from' => $request->time_from,
                            'time_to' => $request->time_to,
                            'is_active' => $request->is_active,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                        $message = 'Timeslot added successfully';
                    }
                }
                return redirect()->route('timeslot.index')->with('success', $message);
            } else {
                $messages = $validator->errors();
                return redirect()->route('timeslot.index')->with('error', $messages);
            }
        } catch (QueryException $e) {
            return redirect()->route('timeslot.index')->with('failed', 'Something went wrong');
        }
    }

    public function edit($id)
    {
        try {
            $pageTitle = "Edit Timeslot";
            $edit_timeslot = Mst_TimeSlot::findOrFail($id);
            $timeslots = Mst_TimeSlot::orderBy('created_at', 'desc')->get();
            return view('timeslot.index', compact('pageTitle', 'timeslots', 'edit_timeslot'));
        } catch (QueryException $e) {
            return redirect()->route('timeslot.index')->with('failed', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'slot_name' => 'required|unique:mst_master_values,master_value',
        ]);
        $slot = Mst_Master_Value::findOrFail($id);
        $slot->master_id = 24;
        $slot->master_value = $request->input('slot_name');
        $slot->group_id = 0;
        $slot->is_active = 1;
        $slot->created_by = 1;
        $slot->update();

        return redirect()->route('timeslot.index')->with('success', 'Timeslot updated successfully');
    }

    public function destroy($id)
    {
        try {
            $timeslot = Mst_TimeSlot::findOrFail($id);
            $timeslot->is_active = 0;
            $timeslot->save();
            $timeslot->delete();
            return 1;
            return redirect()->route('timeslot.index')->with('success', 'Timeslot deleted successfully');
        } catch (QueryException $e) {
            return redirect()->route('timeslot.index')->with('failed', 'Something went wrong');
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $timeslot = Mst_TimeSlot::findOrFail($id);

            $timeslot->is_active = !$timeslot->is_active;
            $timeslot->save();
            return 1;
            return redirect()->back()->with('success', 'Status changed successfully');
        } catch (QueryException $e) {
            return redirect()->route('timeslot.index')->with('failed', 'Something went wrong');
        }
    }

    //Adding timeslot for a particular staff:

    public function slotIndex($id)
    {
        $pageTitle = "Timeslots";
        $timeslot = Mst_TimeSlot::where('staff_id', $id)->with('weekDay', 'timeSlot')->latest()->get();
        $weekday = Mst_Master_Value::where('master_id', 3)->pluck('master_value', 'id');
        $slot = Mst_Master_Value::where('master_id', 24)->pluck('master_value', 'id');

        return view('timeslot.slot', compact('pageTitle', 'timeslot', 'weekday', 'slot', 'id'));
    }

    public function slotStore(Request $request)
    {

        $request->validate([
            'staff_id' => 'required',
            'week_day' => 'required',
            'slot' => 'required',
            'tokens' => 'required',

        ]);
        $is_exists = Mst_TimeSlot::where('week_day', $request->input('week_day'))->where('time_slot', $request->input('slot'))->first();
        if ($is_exists) {
            return redirect()->back()->with('error', 'This timeslot is already assigned.');
        } else {

            $staffslot = new Mst_TimeSlot();

            $staffslot->staff_id = $request->input('staff_id');
            $staffslot->week_day = $request->input('week_day');
            $staffslot->time_slot = $request->input('slot');
            $staffslot->max_tokens = $request->input('tokens');
            $staffslot->is_available = 1;
            $staffslot->is_active = 1;
            $staffslot->created_by  = 1;
            $staffslot->save();


            return redirect()->route('staff.slot', ['id' => $request->input('staff_id')])->with('success', 'Timeslot added successfully');
        }
    }

    public function slotDelete(Request $request, $id)
    {
        $delete = Mst_TimeSlot::findOrFail($id);
        $delete->delete();
        return redirect()->back()->with('success', 'Timeslot deleted successfully');
    }
}
