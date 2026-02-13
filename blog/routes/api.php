<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\ServiceProviderController;
use App\Http\Controllers\API\UserServiceController;
use App\Http\Controllers\Api\VendorController;




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
 Route::post('/vendor/register', [ServiceProviderController::class, 'register']);
    Route::get('/admin/vendors/pending', [ServiceProviderController::class, 'pendingVendors']);
    Route::post('/admin/vendors/{id}/approve', [AdminController::class, 'approve']);
Route::post('/admin/vendors/{id}/reject', [AdminController::class, 'reject']);
Route::get('/admin/vendors/approved', [AdminController::class, 'approvedVendors']);
Route::get('/admin/vendors/rejected', [AdminController::class, 'rejectedVendors']);
Route::delete('/admin/vendors/{id}', [AdminController::class, 'deleteVendor']);
Route::get('/admin/services/pending', [AdminController::class, 'pendingServices']);

// Approve a service
Route::post('/admin/services/{id}/approve', [AdminController::class, 'approveService']);

// Reject a service with reason
Route::post('/admin/services/{id}/reject', [AdminController::class, 'rejectService']);

Route::post('/vendor/login', [ServiceProviderController::class, 'login']);
Route::middleware('auth.vendor')->group(function () {
    // Vendor dashboard info
    Route::get('/vendor/dashboard', [ServiceProviderController::class, 'dashboard']);

    // Vendor services CRUD
    Route::get('/vendor/services', [ServiceProviderController::class, 'listServices']);
    Route::post('/vendor/services', [ServiceProviderController::class, 'addService']);
    Route::put('/vendor/services/{id}', [ServiceProviderController::class, 'updateService']);
    Route::delete('/vendor/services/{id}', [ServiceProviderController::class, 'deleteService']);
     Route::get('/vendor/services/{id}', [ServiceProviderController::class, 'getService']);
});

Route::get('/services', [UserServiceController::class, 'allAvailableServices']);
Route::get('/service/{id}', [UserServiceController::class, 'serviceDetails']);
Route::post('/service/book', [UserServiceController::class, 'bookService'])->middleware('auth:sanctum');
Route::get('/user/approved-vendors', [VendorController::class, 'approvedVendors']);
Route::middleware('auth:sanctum')->post('/vendor/logout', [ServiceProviderController::class, 'logout']);