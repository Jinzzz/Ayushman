<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PatientAuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DoctorBookingController;
use App\Http\Controllers\Api\FamilyController;
use App\Http\Controllers\Api\MyBookingsController;
use App\Http\Controllers\Api\BookingHistoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('patient/register', [PatientAuthController::class,'patientRegister']);
Route::post('patient/login', [PatientAuthController::class,'patientLogin']);
Route::post('patient/otp_verification', [PatientAuthController::class,'otpVerification']);
Route::post('patient/resend_otp', [PatientAuthController::class,'reSendOtp']);
Route::post('patient/forgot_password', [PatientAuthController::class,'forgotPassword']);
Route::post('patient/reset_password', [PatientAuthController::class,'resetPassword']);

Route::get('branches', [DoctorBookingController::class,'getBranches']);


Route::middleware(['auth:api'])->group(function () {   
// Consultation
    Route::get('patient/home', [DashboardController::class,'homePage']);
    Route::post('patient/consultation/doctors_list', [DoctorBookingController::class,'doctorsList']);
    Route::post('patient/consultation/doctors_details', [DoctorBookingController::class,'doctorsDetails']);
    Route::post('patient/consultation/doctor_availability', [DoctorBookingController::class,'doctorsAvailability']);
    Route::post('patient/consultation/booking_details', [DoctorBookingController::class,'bookingDetails']);
    Route::post('patient/consultation/booking_summary', [DoctorBookingController::class,'bookingSummary']);
    Route::post('patient/consultation/booking_confirmation', [DoctorBookingController::class,'bookingConfirmation']);

    // Add family member
    Route::get('patient/my_family', [FamilyController::class,'myFamily']);
    Route::post('patient/add_member', [FamilyController::class,'addMember']);
    Route::post('patient/member/otp_verification', [FamilyController::class,'otpVerification']);
    Route::post('patient/member/resend_otp', [FamilyController::class,'reSendOtp']);

    // Manage consultation Bookings 
    Route::get('patient/my_bookings', [MyBookingsController::class,'myBookings']);
    Route::post('patient/my_booking_details', [MyBookingsController::class,'myBookingDetails']);
    Route::post('patient/cancel_booking', [MyBookingsController::class,'cancelBooking']);

    // Consultation booking history
    Route::get('patient/my_booking_history', [BookingHistoryController::class,'myBookingHistory']);
    Route::post('patient/booking_history_details', [BookingHistoryController::class,'bookingHistoryDetails']);

    // Profile 
    Route::post('patient/update_details', [PatientAuthController::class,'updateDetails']);
    Route::post('patient/change_password', [PatientAuthController::class,'changePassword']);
    Route::get('patient/logout', [PatientAuthController::class,'logout']);
    
}); 
