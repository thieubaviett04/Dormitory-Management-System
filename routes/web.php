<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BedController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('buildings', BuildingController::class);
Route::resource('rooms', RoomController::class);
Route::resource('beds', BedController::class);
