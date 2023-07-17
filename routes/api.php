<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PatientAuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DoctorBookingController;

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
    Route::get('user-details', [PatientAuthController::class,'getUserDetails']);
    Route::get('patient/home', [DashboardController::class,'homePage']);
    Route::post('patient/doctors_list', [DoctorBookingController::class,'doctorsList']);
    Route::post('patient/doctors_details', [DoctorBookingController::class,'doctorsDetails']);
    Route::post('patient/doctor_availability', [DoctorBookingController::class,'doctorsAvailability']);
});
