<?php

use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/createNewEmail', [EmailController::class, 'createNewEmail']);
Route::get('/getTokenForEmail', [EmailController::class, 'getTokenForEmail']);
Route::get('/getLatestMessage', [EmailController::class, 'getLatestMessage']);
