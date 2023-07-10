<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::post('/doctor-login', [App\Http\Controllers\Auth\DoctorLoginController::class, 'doctorLogin'])->name('doctor.login');
Route::middleware(['auth:doctor'])->group(function () {
   Route::get('/doctor-home', [App\Http\Controllers\Web\DashboardController::class, 'dashboard'])->name('doctor.home');
   Route::get('/doctor-change-password', [App\Http\Controllers\Web\ProfileController::class, 'viewChangePassword'])->name('doctor.profile.changePassword');
   Route::post('/doctor-update-password', [App\Http\Controllers\Web\ProfileController::class, 'updatePassword'])->name('doctor.profile.updatePassword');
   Route::get('/doctor-profile', [App\Http\Controllers\Web\ProfileController::class, 'viewProfile'])->name('doctor.profile.viewProfile');
   Route::post('/doctor-update-profile', [App\Http\Controllers\Web\ProfileController::class, 'updateProfile'])->name('doctor.profile.updateProfile');
   Route::get('/doctor-apply-leave', [App\Http\Controllers\Web\LeaveController::class, 'viewApplyLeave'])->name('doctor.leave.viewApplyLeave');
   Route::post('/doctor-submit-leave', [App\Http\Controllers\Web\LeaveController::class, 'submitLeave'])->name('doctor.leave.submit');
   Route::get('/doctor-leave-history', [App\Http\Controllers\Web\LeaveController::class, 'leaveHistory'])->name('doctor.leave.history');
});