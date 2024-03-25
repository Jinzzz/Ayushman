<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mst_TimeSlot;
use App\Models\Mst_Master_Value;
use Illuminate\Support\Facades\Auth;
use App\Models\Mst_Therapy_Room_Slot;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class MstTherapyRoomSlotController extends Controller
{
    public function index($id)
    {
        try {
            $pageTitle = "Assigning Timeslots to Therapy Room";
            $timeslots = Mst_Therapy_Room_Slot::where('therapy_room_id', $id)->with('weekDay', 'slot')->latest()->get();
            $weekdays = Mst_Master_Value::where('master_id', 3)->pluck('master_value', 'id');
            $slots = Mst_TimeSlot::where('is_active', 1)->get();
            return view('therapyrooms.slot', compact('pageTitle', 'timeslots', 'weekdays', 'slots', 'id'));
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'week_day' => 'required',
                'time_slot' => 'required',
            ]);

            $checkExists = Mst_Therapy_Room_Slot::where('therapy_room_id', $request->input('therapy_room_id'))
            ->where('week_day', $request->input('week_day'))
            ->where('timeslot', $request->input('time_slot'))
            ->first();

            if ($checkExists) {
                return redirect()->route('slot_assigning.index', $request->therapy_room_id)->with('exists', 'This timeslot is already assigned to this therapy room.');
            } else {
                $therapySlot = new Mst_Therapy_Room_Slot();
                $therapySlot->therapy_room_id = $request->input('therapy_room_id');
                $therapySlot->week_day = $request->input('week_day');
                $therapySlot->timeslot = $request->input('time_slot');
                $therapySlot->is_active = 1;
                $therapySlot->created_by = Auth::id();
                $therapySlot->updated_by = Auth::id();
                $therapySlot->created_at = Carbon::now();
                $therapySlot->updated_at = Carbon::now();
                $therapySlot->save();
                return redirect()->route('slot_assigning.index', $request->therapy_room_id)->with('success', 'Timeslot assigned successfully');
            }
        } catch (QueryException $e) {
            return redirect()->route('slot_assigning.index', $request->therapy_room_id)->with('error', 'Something went wrong.');
        }
    }

    public function destroy($id)
    {
        try {
            $therapy_room_slot = Mst_Therapy_Room_Slot::findOrFail($id);
            $therapy_room_slot->deleted_by = 1;
            $therapy_room_slot->is_active = 0;
            $therapy_room_slot->save();
            $therapy_room_slot->delete();
            return 1;
        } catch (QueryException $e) {
           return redirect()->route('slot_assigning.index')->with('error', 'Something went wrong');
        }
    }

    public function changeStatus($id)
    {
        try {
            $therapy_room_slot = Mst_Therapy_Room_Slot::findOrFail($id);

            $therapy_room_slot->is_active = !$therapy_room_slot->is_active;
            $therapy_room_slot->save();
            return 1;
            return redirect()->back()->with('success', 'Status changed successfully');
        } catch (QueryException $e) {
            return redirect()->route('slot_assigning.index')->with('error', 'Something went wrong');
        }
    }
}
