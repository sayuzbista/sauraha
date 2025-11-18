<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AdminController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserController::class, 'show']);
    Route::put('/profile', [UserController::class, 'update']);
  
});
// Admin routes
Route::post('/admin/login', [AdminController::class, 'login']);

// Admin service management
Route::prefix('admin')->group(function () {
    Route::post('/services', [AdminController::class, 'storeService']);       // Add service
    Route::get('/services', [AdminController::class, 'listServices']);        // List services
    Route::delete('/services/{id}', [AdminController::class, 'deleteService']); // Delete service
    Route::put('/services/{id}', [AdminController::class, 'updateService']);  
});

