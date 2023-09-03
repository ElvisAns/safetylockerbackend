<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActionLogsController;
use App\Http\Controllers\SecurityLockingCodeController;
use App\Http\Controllers\LinesStatesController;
use App\Http\Controllers\CattleLogsController;
use App\Models\SecurityLockingCode;
use App\Models\LinesStates;

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


Route::get('/iot/sync',[LinesStatesController::class,'get_min']);
Route::get('/lines',[LinesStatesController::class,'get_all']);
Route::post('/lines/state/set/{line_id}',[LinesStatesController::class,'change_line_state']);
Route::get('/lines/state/{line_id}',[LinesStatesController::class,'get_line_state']);
Route::post('/lines/maintenance/{option}',[LinesStatesController::class,'switch_on_or_off_maintance']);
Route::post('/lines',[LinesStatesController::class,'create_line']);
Route::get('/auth/password/forgotten',[SecurityLockingCodeController::class,'password_forgotten']);
Route::post('/auth/password/get_new',[SecurityLockingCodeController::class,'get_new']);
Route::get('/lines/logs/list',[ActionLogsController::class,'get_all']);
Route::post('/lines/logs',[ActionLogsController::class,'save']);
Route::get('/current_status',[LinesStatesController::class,'get_status']);
Route::get('/cattle',[CattleLogsController::class, 'index']);
Route::post('/cattle',[CattleLogsController::class, 'store']);