<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

// GET request ke /api/login - redirect ke home dengan pesan
Route::get('/login', function () {
    return response()->json([
        'success' => false,
        'message' => 'POST only endpoint',
        'note' => 'Gunakan POST /api/login dengan body: {"email": "test@example.com", "password": "password"}',
        'hint' => 'Buka Postman Collection atau cek Dashboard untuk quick start',
    ], 405);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('products', ProductController::class);
    Route::apiResource('purchases', PurchaseController::class)->only(['index', 'store', 'show']);

    Route::get('/report/purchases', [ReportController::class, 'purchases']);
});
