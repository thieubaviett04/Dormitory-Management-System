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

Route::get('/invoices', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoice.index');

Route::get('/invoices/create', [App\Http\Controllers\InvoiceController::class, 'create'])->name('invoice.create');

Route::post('/invoices', [App\Http\Controllers\InvoiceController::class, 'store'])->name('invoice.store');

Route::patch('/invoices/{id}/pay', [App\Http\Controllers\InvoiceController::class, 'pay'])->name('invoice.pay');

Route::get('/invoices/{id}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoice.show');

Route::get('/violations', [App\Http\Controllers\ViolationRecordController::class, 'index'])->name('violation.index');
