<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PackageQuotaController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/package-quota', [PackageQuotaController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/transaction/all', [TransactionController::class, 'getAll']);
    Route::post('/transaction/export', [TransactionController::class, 'exportExcel']);
    Route::put('/package-quota/{packageQuota}', [PackageQuotaController::class, 'update']);
});

Route::post('/transaction/create-transaction', [TransactionController::class, 'createTransaction']);
Route::post('/transaction/handle-notification', [TransactionController::class, 'handleNotification']);
Route::get('transaction/{orderId}', [TransactionController::class, 'getTransactionByOrderId']);

