<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuildingController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('buildings', BuildingController::class);
