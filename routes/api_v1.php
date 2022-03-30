<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthenticationController;
use App\Http\Controllers\API\V1\TaskController;


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

Route::post('/register', [AuthenticationController::class, 'register']);

Route::post('/login', [AuthenticationController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum', 'throttle:20,1']], function () { 

    Route::resource('tasks', '\\'.TaskController::class);
    Route::post('tasks/{task}/complete', [TaskController::class,'complete']);
    Route::post('tasks/{task}/incomplete', [TaskController::class,'incomplete']);
    Route::post('tasks/{task}/archive', [TaskController::class,'archive']);
    Route::post('tasks/{task}/unarchive', [TaskController::class,'unarchive']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);

});

