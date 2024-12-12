<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

$allowedOrigins = ['https://www.globemergelogistics.com', 'https://globemergelogistics.com', 'http://localhost:3000', 'http://localhost:3001'];
$requestOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;

if ($requestOrigin && in_array($requestOrigin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $requestOrigin);
}
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');
header('Access-Control-Allow-Credentials: true');

Route::group([

    'middleware' => 'api'

], function ($router) {
    //auth
    Route::post('/signup', [UserController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/me', [AuthController::class, 'me']);

    //package
    Route::post('/create-package', [PackageController::class, 'createPackage']);
    Route::post('/create-history', [PackageController::class, 'createHistory']);
    Route::post('/change-status', [PackageController::class, 'changeStatus']);
    Route::get('/all-packages', [PackageController::class, 'getAllPAckages']);
    Route::post('/delete-package', [PackageController::class, 'deletePackage']);

    //user
    Route::post('/reset-password', [UserController::class, 'resetPassword']);
});

//package
Route::get('/get-package/{tracking_id}', [PackageController::class, 'getPackage']);
