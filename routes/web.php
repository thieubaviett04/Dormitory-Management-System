<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomRegistrationController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BedController;

Route::get('/', function () {
    return view('welcome');
});

// ==========================================
// MODULE 2: ĐĂNG KÝ CHỖ Ở (ROUTES)
// ==========================================
Route::prefix('registration')->name('registration.')->group(function () {

    // 1. Sinh viên đăng ký phòng (Phương thức POST)
    Route::post('/store', [RoomRegistrationController::class, 'store'])->name('store');

    // 2. Hủy đăng ký (Phương thức DELETE)
    Route::delete('/cancel/{roomRegistration}', [RoomRegistrationController::class, 'cancel'])->name('cancel');

    // 3. Xem trạng thái đơn của sinh viên (Phương thức GET)
    Route::get('/status/{student}', [RoomRegistrationController::class, 'showStatus'])->name('status');

    // 4. Cán bộ duyệt / từ chối đơn (Phương thức PUT)
    Route::put('/update/{roomRegistration}', [RoomRegistrationController::class, 'updateStatus'])->name('update');

    // 5. Xem danh sách đơn chờ duyệt và danh sách chờ xếp phòng
    Route::get('/pending', [RoomRegistrationController::class, 'pending'])->name('pending');
    Route::get('/waitlist', [RoomRegistrationController::class, 'waitlist'])->name('waitlist');

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
