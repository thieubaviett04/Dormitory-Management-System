<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomRegistrationController;

Route::get('/', function () {
    return view('welcome');
});

// ==========================================
// MODULE 2: ĐĂNG KÝ CHỖ Ở (ROUTES)
// ==========================================
Route::prefix('registration')->group(function () {

    // 1. Sinh viên đăng ký phòng (Phương thức POST)
    Route::post('/store', [RoomRegistrationController::class, 'store']);

    // 2. Hủy đăng ký (Phương thức DELETE)
    Route::delete('/cancel/{id}', [RoomRegistrationController::class, 'cancel']);

    // 3. Xem trạng thái đơn của sinh viên (Phương thức GET)
    Route::get('/status/{student_id}', [RoomRegistrationController::class, 'showStatus']);

    // 4. Cán bộ duyệt / từ chối đơn (Phương thức PUT)
    Route::put('/update/{id}', [RoomRegistrationController::class, 'updateStatus']);

    // 5. Xem danh sách chờ (Phương thức GET)
    Route::get('/waitlist', [RoomRegistrationController::class, 'waitlist']);

});