<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CurriculumSetupController;

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

Route::get('fetch-curriculum-setup', [CurriculumSetupController::class, 'fetchCurriculumSetup']);

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register'])->middleware('permission:create-users');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('user', [AuthController::class, 'user']); //->middleware('permission:read-users');
    });
});

//////////////////////////////// APP APIS //////////////////////////////////////////////
Route::group(['middleware' => 'auth:sanctum'], function () {
    // Protected routes for authenticated users
    Route::get('fetch-necessary-params', [Controller::class, 'fetchNecessayParams']);
    Route::get('user-notifications', [UsersController::class, 'userNotifications']);

    Route::group(['prefix' => 'curriculum'], function () {
        Route::post('level/save', [CurriculumSetupController::class, 'storeCurriculumLevel']);
        Route::get('level/all', [CurriculumSetupController::class, 'allCurriculumLevels']);
        Route::put('level/update/{curriculum_level}', [CurriculumSetupController::class, 'updateCurriculumLevel']);
    });
});
