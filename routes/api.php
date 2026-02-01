<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TrackedProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\CorrectionController;
use App\Http\Controllers\Api\NotificationController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/{product}/price-history', [ProductController::class, 'priceHistory']);
Route::post('/products/compare', [ProductController::class, 'compare']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::get('/stores', [StoreController::class, 'index']);
Route::get('/stores/{store}', [StoreController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);

    Route::get('/tracked-products', [TrackedProductController::class, 'index']);
    Route::post('/tracked-products', [TrackedProductController::class, 'store']);
    Route::put('/tracked-products/{product}', [TrackedProductController::class, 'update']);
    Route::delete('/tracked-products/{product}', [TrackedProductController::class, 'destroy']);

    Route::get('/corrections', [CorrectionController::class, 'index']);
    Route::post('/corrections', [CorrectionController::class, 'store']);
    Route::get('/corrections/{correction}', [CorrectionController::class, 'show']);
    Route::put('/corrections/{correction}/approve', [CorrectionController::class, 'approve'])
        ->middleware('can:approve,correction');
    Route::put('/corrections/{correction}/reject', [CorrectionController::class, 'reject'])
        ->middleware('can:approve,correction');

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);

    Route::middleware('can:admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
        Route::post('/stores', [StoreController::class, 'store']);
        Route::put('/stores/{store}', [StoreController::class, 'update']);
        Route::delete('/stores/{store}', [StoreController::class, 'destroy']);
    });
});

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route not found'
    ], 404);
});
