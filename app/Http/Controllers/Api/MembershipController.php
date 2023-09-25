<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Mst_Patient;
use App\Models\Mst_Membership_Package;
use App\Models\Mst_Membership_Benefit;
use App\Models\Mst_Membership_Package_Wellness;
use App\Models\Mst_Patient_Membership_Booking;
use App\Models\Trn_Patient_Wellness_Sessions;
use App\Models\Mst_Wellness;
use App\Helpers\PatientHelper;

class MembershipController extends Controller
{
    public function membershipPackages()
    {
        $data = array();
        try {
            $patient_id = Auth::id();
            $accountHolder = Mst_Patient::find($patient_id);
            $joined_membership_package_id = "";

            if ($accountHolder->available_membership !=  0) {
                $booked_details = Mst_Patient_Membership_Booking::where('patient_id', Auth::id())
                    ->where('membership_expiry_date', '>=', Carbon::now())
                    ->where('is_active', 1)
                    ->first();
                $joined_membership_package_id = $booked_details ? $booked_details->membership_patient_id : null;
            }

            $memberships = Mst_Membership_Package::where('is_active', 1)->get();
            $membership_packages = [];

            if ($memberships->isNotEmpty()) {
                foreach ($memberships as $membership) {

                    $benefits = Mst_Membership_Benefit::where('package_id', $membership->membership_package_id)
                        ->where('is_active', 1)
                        ->pluck('title')
                        ->map(function ($benefit) {
                            preg_match_all('/<li>(.*?)<\/li>/', $benefit, $matches);
                            return $matches[1];
                        });

                    $is_joined = $joined_membership_package_id && $membership->membership_package_id === $joined_membership_package_id ? 1 : 0;

                    $membership_packages[] = [
                        'membership_package_id' => $membership->membership_package_id,
                        'package_title' => $membership->package_title,
                        'package_duration' => $membership->package_duration . " days",
                        'package_price' => $membership->package_price,
                        'is_joined' => $is_joined,
                        'benefits' => $benefits,
                    ];
                }

                $data['status'] = 1;
                $data['message'] = "Data Fetched";
                $data['data'] = $membership_packages;
            } else {
                $data['status'] = 0;
                $data['message'] = "Currently, no memberships are available";
            }

            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }

    public function membershipPackageDetails(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'membership_package_id' => ['required'],
                ],
                [
                    'membership_package_id.required' => 'Membership package required',
                ]
            );

            if (!$validator->fails()) {
                if (isset($request->membership_package_id)) {

                    $package_details = Mst_Membership_Package::where('membership_package_id', $request->membership_package_id)->where('is_active', 1)->first();

                    $membership_package_details[] = [
                        'membership_package_id' => $package_details->membership_package_id,
                        'package_title' => $package_details->package_title,
                        'package_duration' => $package_details->package_duration . " days",
                        'package_price' => $package_details->package_price,
                        'package_description' => $package_details->package_description,
                    ];
                    $benefits = Mst_Membership_Benefit::where('package_id', $request->membership_package_id)->where('is_active', 1)->pluck('title');

                    $membership__package__wellnesses = Mst_Membership_Package_Wellness::join('mst_wellness', 'mst__membership__package__wellnesses.wellness_id', '=', 'mst_wellness.wellness_id')
                        ->where('mst__membership__package__wellnesses.package_id', $request->membership_package_id)
                        ->where('mst__membership__package__wellnesses.is_active', 1)
                        ->selectRaw('mst_wellness.wellness_id, mst_wellness.wellness_name, CONCAT(mst_wellness.wellness_duration, " minutes") as wellness_duration, mst__membership__package__wellnesses.maximum_usage_limit, mst_wellness.wellness_inclusions')
                        ->get();



                    $data['status'] = 1;
                    $data['message'] = "Data Fetched";
                    $data['package_details'] = $membership_package_details;
                    $data['package__wellnesses'] = $membership__package__wellnesses;
                    $data['package_benefits'] = $benefits;
                    return response()->json($data);
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Please fill mandatory fields";
                    return response()->json($data);
                }
            } else {
                $data['status'] = 0;
                $data['errors'] = $validator->errors();
                $data['message'] = "Validation errors";
                return response($data);
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }

    public function currentMembershipDetails()
    {
        $data = array();
        try {
            $is_membership = Mst_Patient::where('id', Auth::id())->value('available_membership');

            if ($is_membership != 0) {
                $latest_membership_booking = Mst_Patient_Membership_Booking::where('patient_id', Auth::id())
                    ->where('membership_expiry_date', '>=', Carbon::now())
                    ->where('is_active', 1)
                    ->first();

                $package_details = Mst_Membership_Package::where('membership_package_id', $latest_membership_booking->membership_package_id)
                    ->first();

                $targetDate = Carbon::parse($latest_membership_booking->membership_expiry_date);
                $membership_booking_date = $latest_membership_booking->start_date->format('d-m-Y');
                $membership_expiry_date = $targetDate->format('d-m-Y');
                $days_left = Carbon::now()->diffInDays($targetDate) . " days";

                $membership_wellnesses = Trn_Patient_Wellness_Sessions::where('membership_patient_id', $latest_membership_booking->membership_patient_id)
                    ->distinct()
                    ->pluck('wellness_id');

                // If you want to convert the result to an array, you can use ->toArray()
                $membership_wellnesses = $membership_wellnesses->toArray();

                $completedSessions = [];
                $remainingSessions = [];

                foreach ($membership_wellnesses as $membership_wellness) {
                    $sessionDetails = Trn_Patient_Wellness_Sessions::where('membership_patient_id', $latest_membership_booking->membership_patient_id)
                        ->where('wellness_id', $membership_wellness)
                        ->where('status', 0)
                        ->first();

                    $wellness_name = Mst_Wellness::where('wellness_id', $membership_wellness)->value('wellness_name');

                    if (!empty($sessionDetails)) {
                        $remainingSessions[] = $wellness_name;
                    } else {
                        $completedSessions[] = $wellness_name;
                    }
                }

                $membership_data = [
                    'package_id' => $package_details->membership_package_id,
                    'package_title' => $package_details->package_title,
                    'membership_booking_date' => $membership_booking_date,
                    'membership_expiry_date' => $membership_expiry_date,
                    'days_left' => $days_left,
                    'completed_sessions' => $completedSessions,
                    'remaining_sessions' => $remainingSessions,
                ];

                $data[] = $membership_data;

                if (!empty($data)) {
                    $response = [
                        'status' => 1,
                        'message' => "Data Fetched",
                        'current_membership_details' => $data,
                    ];
                } else {
                    $response = [
                        'status' => 0,
                        'message' => "No active membership bookings found",
                    ];
                }
            } else {
                $response = [
                    'status' => 0,
                    'message' => "Currently not a member of any membership packages",
                ];
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
        }

        return response()->json($response);
    }


    public function purchaseMembershipPackage(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'membership_package_id' => ['required'],
                    'start_date' => ['required'],
                ],
                [
                    'membership_package_id.required' => 'Membership package required',
                    'start_date.required' => 'Start date required',
                ]
            );

            if (!$validator->fails()) {
                if (isset($request->membership_package_id) && isset($request->start_date)) {

                    $start_date = Carbon::parse($request->start_date);
                    $currentDate = Carbon::now();
                    $currentYear = Carbon::now()->year;

                    if ($start_date->year > $currentYear + 1) { 
                        $data['status'] = 0;
                        $data['message'] = "Starting date cannot be more than 1 year in the future.";
                        return response($data);
                    }

                    if (!$start_date->isSameDay($currentDate) && $start_date->isPast()) {
                        $data['status'] = 0;
                        $data['message'] = "Starting date is older than the current date.";
                        return response($data);
                    }

                    $is_membership = Mst_Patient::where('id', Auth::id())->value('available_membership');
                    $package_details = Mst_Membership_Package::where('membership_package_id', $request->membership_package_id)
                        ->where('is_active', 1)
                        ->first();

                    $package_duration = $package_details->package_duration; // Number of days to add

                    if ($is_membership == 1) {
                        // Checking all membership booking in this package_id? if yes taking last inserted row 
                        $last_membership_booking = Mst_Patient_Membership_Booking::where('patient_id', Auth::id())
                            ->where('membership_expiry_date', '>=', Carbon::now())
                            ->orderBy('created_at', 'desc')
                            ->first();

                        if (!empty($last_membership_booking)) {
                            // print_r($last_membership_booking->membership_expiry_date);die();
                            $is_active = 0;
                            $expiry_date = Carbon::parse($last_membership_booking->membership_expiry_date)->addDays($package_duration);
                        } else {
                            // If no previous membership booking is found, set the expiry_date based on the current date
                            $is_active = 1;
                            $expiry_date = Carbon::parse($request->start_date)->addDays($package_duration);
                        }
                    } else {
                        // fresh booking updating patients table and find expiry date 
                        $updatePatientCode = Mst_Patient::where('id', Auth::id())->update([
                            'updated_at' => Carbon::now(),
                            'available_membership' => 1
                        ]);
                        $expiry_date =Carbon::parse($request->start_date)->addDays($package_duration);
                        $is_active = 1;
                    }

                    $membership_wellnesses = Mst_Membership_Package_Wellness::where('package_id', $request->membership_package_id)
                        ->where('is_active', 1)
                        ->select('wellness_id', 'maximum_usage_limit')
                        ->get()
                        ->map->toArray()
                        ->values()
                        ->all();

                    $starting_date = PatientHelper::dateFormatDb($request->start_date);
                    $lastInsertedId = Mst_Patient_Membership_Booking::insertGetId([
                        'patient_id' => Auth::id(),
                        'membership_package_id' => $request->membership_package_id,
                        'start_date' => $starting_date,
                        'membership_expiry_date' => $expiry_date,
                        'payment_type' => 1,
                        'details' => "test",
                        'is_active' => $is_active,
                        'payment_amount' => $package_details->package_price,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    foreach ($membership_wellnesses as $membership_wellness) {
                        for ($i = 0; $i < $membership_wellness['maximum_usage_limit']; $i++) {
                            $createRecord = Trn_Patient_Wellness_Sessions::create([
                                'membership_patient_id' => $lastInsertedId,
                                'wellness_id' => $membership_wellness['wellness_id'],
                                'status' => 0,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]);
                        }
                    }

                    $data = [
                        'status' => 1,
                        'message' => "Membership added successfully",
                    ];
                    return response()->json($data);
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Please fill mandatory fields";
                    return response()->json($data);
                }
            } else {
                $data['status'] = 0;
                $data['errors'] = $validator->errors();
                $data['message'] = "Validation errors";
                return response($data);
            }
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
}
