<?php

use App\Events\NewAccident;
use App\Http\Controllers\AccidentController;
use App\Models\Accident;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/accident',[AccidentController::class,'index']);

Route::post('/accident',[AccidentController::class,'store']);

Route::get('/accident/statistics',[AccidentController::class,'statistics']);

Route::post('/{accident}/complete',[AccidentController::class,'complete']);

Route::delete('/{accident}/delete',[AccidentController::class,'destroy']);