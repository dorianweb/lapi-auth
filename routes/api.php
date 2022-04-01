<?php

use App\Http\Controllers\API\AuthController;
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

Route::get('users', [AuthController::class, 'mi']);

Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::get('validate/{token}', [AuthController::class, 'validat']);

    Route::post('token', [AuthController::class, 'login']);

    Route::post('logout',  [AuthController::class, 'logout']);
    Route::get('access_token/refresh',  [AuthController::class, 'refresh']);
    Route::post('user',  [AuthController::class, 'me']);
});
