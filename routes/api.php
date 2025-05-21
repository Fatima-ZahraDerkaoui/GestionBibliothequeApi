<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\BookPreviewController;
use App\Http\Controllers\Api\NotificationController;

Route::get('/hello', function () {
    return response()->json(['message' => 'WELCOME TO THE API']);
});

// Auth routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Users & Books
    Route::apiResource('users', UserController::class);
    Route::apiResource('books', BookController::class);

    // Purchases (achats de livres)
    Route::get('purchases', [PurchaseController::class, 'index']);
    Route::post('purchases', [PurchaseController::class, 'store']);
    Route::get('purchases/{id}', [PurchaseController::class, 'show']);
    Route::put('purchases/{id}', [PurchaseController::class, 'update']);
    Route::delete('purchases/{id}', [PurchaseController::class, 'destroy']);

    // Book previews
    Route::get('book-previews', [BookPreviewController::class, 'index']);
    Route::post('book-previews', [BookPreviewController::class, 'store']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);
});
