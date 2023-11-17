<?php

use App\Http\Controllers\Api\AdminProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportsController;
use App\Http\Controllers\Api\StaffLeaveController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/test', function (Request $request) {
       return 'test';
    });
Route::post('admin/login', [AuthController::class,'loginCustomer']);
Route::get('admin/gender', [AdminProfileController::class, 'getGender']);
Route::get('admin/blood-group', [AdminProfileController::class, 'getBloodGroup']);
Route::get('admin/branches', [AdminProfileController::class, 'getBranches']);
Route::get('admin/booking-types', [AdminProfileController::class, 'getPatientBookingTypes']);
Route::get('admin/suppliers', [AdminProfileController::class, 'getSuppliers']);
Route::get('admin/get-accounts', [ReportsController::class, 'getAccountGroups']);
Route::middleware(['auth:api'])->group(function () {
    Route::get('admin/get-admin-details', [AdminProfileController::class,'getAdminDetails']);
    Route::post('admin/change-password', [AdminProfileController::class,'updatePassword']);
    Route::post('admin/update-profile', [AdminProfileController::class,'updateProfile']);
    Route::post('admin/update-image', [AdminProfileController::class,'updateImage']);
    Route::get('admin/dashboard', [DashboardController::class,'home']);
    Route::get('admin/get-graph', [DashboardController::class,'getGraph']);
    //Staff Leave Calendar
    Route::get('admin/staff-leave-calendar', [StaffLeaveController::class,'staffLeaveCalendar']);
    Route::get('admin/staff-leave-count', [StaffLeaveController::class,'leaveCalendarCount']);
    
    //Reports
    Route::get('admin/sale-report', [ReportsController::class,'medicineSalesReport']);
    Route::get('admin/purchase-report', [ReportsController::class,'medicinePurchasesReport']);
    Route::get('admin/purchase-return-report', [ReportsController::class,'medicinePurchasesReturnReport']);
    Route::get('admin/stock-transfer-report', [ReportsController::class,'stockTransferReport']);
    Route::get('admin/current-stock-report', [ReportsController::class,'currentStockReport']);
    Route::get('admin/payment-made-report', [ReportsController::class,'paymentMadeReport']);
    Route::get('admin/payment-received-report', [ReportsController::class,'paymentReceivedReport']);
    Route::get('admin/payable-report', [ReportsController::class,'payableReport']);
    Route::get('admin/receivable-report', [ReportsController::class,'receivableReport']);
    Route::get('admin/ledger-report', [ReportsController::class,'ledgerReport']);
    Route::get('admin/profit-loss-report', [ReportsController::class,'generateProfitAndLoss']);
    Route::get('admin/trial-balance-report', [ReportsController::class,'trialBalanceReport']);
    Route::get('admin/balance-sheet-report', [ReportsController::class,'balanceSheetReport']);
    
});