<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// without authorization accessible urls
Route::post('register', [AuthController::class, 'register']);
Route::post('generateotp', [AuthController::class, 'generateOtp']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

// SANCTUM Routes Starts
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('/currentuser', function (Request $request) {
    return response()->json([
      'status' => true,
      'message' => 'User details get successfully...!',
      'data' => $request->user()
    ], 201);
  });

  // USERAPICRUD routes
  Route::get('user', [\App\Http\Controllers\Api\UserController::class, 'index'])->middleware('ability:user:list'); // SANCTUM:ABILITY Route Middleware 
  Route::get('user/search', [\App\Http\Controllers\Api\UserController::class, 'filterUser'])->middleware('ability:user:search'); // USERFILTER, SANCTUM:ABILITY Route Middleware 
  Route::post('user/store', [\App\Http\Controllers\Api\UserController::class, 'storeUser'])->middleware('ability:user:create'); // SANCTUM:ABILITY Route Middleware
  Route::put('user/update/{id?}', [\App\Http\Controllers\Api\UserController::class, 'updateUser'])->middleware('ability:user:edit');
  Route::get('user/{id?}', [\App\Http\Controllers\Api\UserController::class, 'showUser'])->middleware('ability:user:show'); // SANCTUM:ABILITY Route Middleware
  Route::delete('user/delete/{id?}', [\App\Http\Controllers\Api\UserController::class, 'deleteUser'])->middleware('ability:user:delete'); // SANCTUM:ABILITY Route Middleware;

  // Sanctum Auth routes
  Route::get('logout', [AuthController::class, 'logout']);
  Route::get('refresh-token', [AuthController::class, 'refreshToken']);
});
// SANCTUM Routes Ends