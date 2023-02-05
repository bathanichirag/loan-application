<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanPaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::group([
    'middleware' => ['auth:api']
], function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    // Routes for Loans
    Route::post('/apply-loan', [LoanController::class, 'applyForLoan']);
    Route::post('/change-loan-status', [LoanController::class, 'changeLoanStatus']);
    Route::get('/get-loans', [LoanController::class, 'getLoansList']);

    // Routes for Loan Payments
    Route::post('/loan-payment', [LoanPaymentController::class, 'payLoan']);
    Route::get('/get-loan-payments', [LoanPaymentController::class, 'getPaymentDetails']);
});
