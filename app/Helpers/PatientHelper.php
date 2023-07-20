<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use App\Models\Mst_Patient;
use App\Models\Mst_TimeSlot;
use App\Models\Trn_Consultation_Booking;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PatientHelper
{
    public static function getWeekDay($date)
    {
        $carbon_date = Carbon::parse($date);
        $day_of_week = $carbon_date->format('l');
        return  $day_of_week;
    }

    public static function dateFormatDb($date)
    {
        $formattedDate = Carbon::parse($date)->format('Y-m-d');
        return $formattedDate;
    }

    public static function dateFormatUser($date)
    {
        $formattedDate = Carbon::parse($date)->format('d-m-Y');
        return $formattedDate;
    }

    // to get timeslots
    public static function getTimeSlot($interval, $start_time, $end_time)
    {
        $start = new \DateTime($start_time);
        $end = new \DateTime($end_time);
        $startTime = $start->format('H:i');
        $endTime = $end->format('H:i');
        $i=0;
        $time = [];
        while(strtotime($startTime) <= strtotime($endTime)){
            $start = $startTime;
            $end = date('H:i',strtotime('+'.$interval.' minutes',strtotime($startTime)));
            $startTime = date('H:i',strtotime('+'.$interval.' minutes',strtotime($startTime)));
            $i++;
            if(strtotime($startTime) <= strtotime($endTime)){
                $time[$i]['slot_start_time'] = $start;
                $time[$i]['slot_end_time'] = $end;
            }
        }
        return $time;
    }

    // rechecking whether the slot is available or not 
    public static function recheckAvailability($booking_date, $slot_id, $doctor_id){

        $timeSlot = Mst_TimeSlot::where('id', $slot_id)->where('is_active', 1)->first();

        $booked_tokens = Trn_Consultation_Booking::where('booking_date', $booking_date)->where('time_slot_id', $slot_id)
                        ->whereIn('booking_status_id', [1, 2])->count();

        $available_slots = $timeSlot->no_tockens - $booked_tokens;

        $currentDate = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i:s');

        if ($available_slots <= 0 || ($timeSlot->time_to <= $currentTime && $booking_date == $currentDate)) {
            $available_slots = 0;

        } elseif ($booking_date == $currentDate && $timeSlot->time_from <= $currentTime && $timeSlot->time_to >= $currentTime) {
            
            $slots = self::getTimeSlot($timeSlot->avg_time_patient, $timeSlot->time_from, $timeSlot->time_to);
            $slots = array_slice($slots, $booked_tokens);

            $available_slots = 0;
            foreach ($slots as $slot) {
                if ($slot['slot_start_time'] > $currentTime) {
                    $available_slots++;
                }
            }

        } else {
            $available_slots = ($available_slots < 0) ? 0 : $available_slots;
        }
        return $available_slots;
    }
    
}
