<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use App\Models\Mst_Patient;
use App\Models\Mst_Staff_Timeslot;
use App\Models\Trn_Consultation_Booking;
use App\Models\Trn_Patient_Family_Member;
use Carbon\Carbon;

class PatientHelper
{

    // get week day using date 
    public static function getWeekDay($date)
    {
        $carbon_date = Carbon::parse($date);
        $day_of_week = $carbon_date->format('l');
        return  $day_of_week;
    }

    // get date as how it saving in DB 
    public static function dateFormatDb($date)
    {
        $formattedDate = Carbon::parse($date)->format('Y-m-d');
        return $formattedDate;
    }

    // get date as how user seeing 
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
        $i = 0;
        $time = [];
        while (strtotime($startTime) <= strtotime($endTime)) {
            $start = $startTime;
            $end = date('H:i', strtotime('+' . $interval . ' minutes', strtotime($startTime)));
            $startTime = date('H:i', strtotime('+' . $interval . ' minutes', strtotime($startTime)));
            $i++;
            if (strtotime($startTime) <= strtotime($endTime)) {
                $time[$i]['slot_start_time'] = $start;
                $time[$i]['slot_end_time'] = $end;
            }
        }
        return $time;
    }

    // rechecking whether the slot is available or not 

    public static function recheckAvailability($booking_date, $slot_id, $doctor_id)
    {

        $booking_date = self::dateFormatDb($booking_date);
        $timeSlot = Mst_Staff_Timeslot::join('mst_timeslots', 'mst__staff__timeslots.timeslot', 'mst_timeslots.id')
        ->where('mst__staff__timeslots.id', $slot_id)->where('mst__staff__timeslots.is_active', 1)->first();

        $booked_tokens = Trn_Consultation_Booking::where('booking_date', $booking_date)->where('time_slot_id', $slot_id)
            ->whereIn('booking_status_id', [87, 88])->count();

        $available_slots = $timeSlot->no_tokens - $booked_tokens;
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

    // get all family members array using patient_id 
    public static function getFamilyDetails($patient_id)
    {
        $family_details = array();
        $accountHolder = Mst_Patient::join('mst_master_values', 'mst_patients.patient_gender', '=', 'mst_master_values.id')
            ->where('mst_patients.id', $patient_id)
            ->select('mst_patients.*', 'mst_master_values.master_value as gender_name')
            ->first();

        $members = Trn_Patient_Family_Member::join('mst_patients', 'trn_patient_family_member.patient_id', 'mst_patients.id')
            ->join('mst_master_values as gender', 'trn_patient_family_member.gender_id', '=', 'gender.id')
            ->join('mst_master_values as relationship', 'trn_patient_family_member.relationship_id', '=', 'relationship.id')
            ->select('trn_patient_family_member.id', 'trn_patient_family_member.family_member_name', 'trn_patient_family_member.email_address', 'trn_patient_family_member.mobile_number', 'gender.master_value as gender_name', 'trn_patient_family_member.date_of_birth', 'relationship.master_value as relationship')
            ->where('trn_patient_family_member.patient_id', $patient_id)
            ->where('trn_patient_family_member.is_active', 1)
            ->get();


        $currentYear = Carbon::now()->year;
        $carbonDate = Carbon::parse($accountHolder->patient_dob);
        $year = $carbonDate->year;

        $family_details[] = [
            'member_id' => $accountHolder->id,
            'family_member_id' => 0,
            'member_name' => $accountHolder->patient_name,
            'relationship' => "Yourself",
            'age' => $currentYear - $year,
            'dob' => Carbon::parse($accountHolder->patient_dob)->format('d-m-Y'),
            'gender' => $accountHolder->gender_name,
            'mobile_number' => $accountHolder->patient_mobile,
            'email_address' => $accountHolder->patient_email,
        ];

        foreach ($members as $member) {
            $carbonDate = Carbon::parse($member->date_of_birth);
            $year = $carbonDate->year;

            $family_details[] = [
                'member_id' => $accountHolder->id,
                'family_member_id' => $member->id,
                'member_name' => $member->family_member_name,
                'relationship' => $member->relationship,
                'age' => $currentYear - $year,
                'dob' => Carbon::parse($member->date_of_birth)->format('d-m-Y'),
                'gender' => $member->gender_name,
                'mobile_number' => $member->mobile_number,
                'email_address' => $member->email_address,
            ];
        }
        return $family_details;
    }
}