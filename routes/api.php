<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BorrowingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

Route::get('/hello', function () {
    return response()->json(['message' => 'WELCOME TO THE API']);
});

// Auth routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Resources
    Route::apiResource('users', UserController::class);
    Route::apiResource('books', BookController::class);

    // Borrowings
    Route::get('borrowings', [BorrowingController::class, 'index']);
    Route::post('borrowings', [BorrowingController::class, 'store']);
    Route::put('borrowings/return/{id}', [BorrowingController::class, 'returnBook']);
});