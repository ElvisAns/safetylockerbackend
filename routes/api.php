<?php

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


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/lines/{line_id?}',function ($line_id=null){
    if($line_id){
        //return state of the current line
    }
    //return state of all lines
});

Route::post('/lines/{line_id}/{state}',function($line_id,$state){
    //Requires the current password in the request
    //put a given line in a given state (on and off)
    //each action should put something in the log
});

Route::post('/lines/maintenance/{state}',function($state){
    //start or stop maintence maintenance
    //Require current password
    //each action should put something in the log
});

Route::get('/auth/password/forgotten',function(){
    //send email to admin with the current password to unclock maintenance mode
    //each action should put something in the log
});

Route::post('/auth/password/get_new',function(){
    //get new password from the backend
    //must submit the old password though post
    //each action should put something in the log
});

Route::get('/lines/logs',function(){
    //get list of logs and timestamp per entry
});