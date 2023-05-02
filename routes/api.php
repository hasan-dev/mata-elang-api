<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationMemberController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\UserController;
use App\Models\OrganizationMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('sensors')->group(function () {
    Route::post('/login', [SensorController::class, 'login']);
    Route::patch('/update/{id}', [SensorController::class, 'edit']);
    Route::post('/register', [SensorController::class, 'register']);
    Route::post('/heartbeat', [SensorController::class, 'heartbeat']);
    Route::get('/uuid', [SensorController::class, 'login2']);
    Route::delete('/delete/{id}', [SensorController::class, 'delete']);
    Route::get('/{id}', [SensorController::class, 'detail']);
    Route::patch('/update_status/{id}', [SensorController::class, 'updateStatus']);
});

Route::prefix('users')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::patch('/update/{id}', [UserController::class, 'update']);
    Route::get('/{id}', [UserController::class, 'profile']);
    Route::delete('/delete/{id}', [UserController::class, 'delete']);
});


Route::prefix('assets')->group(function () {
    Route::post('/register', [AssetController::class, 'register']);
    Route::patch('/update/{id}', [AssetController::class, 'edit']);
    Route::get('/{id}', [AssetController::class, 'detail']);
    Route::delete('/delete/{id}', [AssetController::class, 'delete']);
});

Route::prefix('permissions')->group(function () {
    Route::get('/all', [PermissionController::class, 'get']);
});

Route::prefix('organizations')->group(function () {
    Route::post('/create', [OrganizationController::class, 'create']);
    Route::patch('/update/{id}', [OrganizationController::class, 'edit']);
    Route::get('{id}/all', [OrganizationController::class, 'getAll']);
    Route::get('/{id}/sensors/all', [OrganizationController::class, 'getSensor']);
    Route::get('/{id}/assets/all', [OrganizationController::class, 'getAsset']);
    Route::get('/{id}/roles/all', [OrganizationController::class, 'getRole']);
    Route::get('/{id}/users/all', [OrganizationController::class, 'getUser']);
    Route::get('/{id}', [OrganizationController::class, 'profile']);
    Route::post('{id}/users/register', [UserController::class, 'register']);
    Route::delete('/{id}/delete', [OrganizationController::class, 'delete']);
});

Route::prefix('roles')->group(function () {
    Route::post('/create', [RoleController::class, 'create']);
    Route::patch('/update/{id}', [RoleController::class, 'edit']);
    Route::delete('/delete/{id}', [RoleController::class, 'delete']);
    Route::get('/{id}', [RoleController::class, 'detail']);
});