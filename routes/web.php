<?php

use App\Http\Controllers\BedController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomRegistrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ViolationRecordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Các route Auth nằm ngoài middleware auth
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Các route nghiệp vụ yêu cầu đăng nhập và phân quyền cho STUDENT
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    })->name('student.dashboard');

    // ==========================================
    // MODULE 2: ĐĂNG KÝ CHỖ Ở (ROUTES) - STUDENT
    // ==========================================
    Route::prefix('registration')->name('registration.')->group(function () {
        // 1. Sinh viên đăng ký phòng (Phương thức POST)
        Route::post('/store', [RoomRegistrationController::class, 'store'])->name('store');

        // 2. Hủy đăng ký (Phương thức DELETE)
        Route::delete('/cancel/{roomRegistration}', [RoomRegistrationController::class, 'cancel'])->name('cancel');

        // 3. Xem trạng thái đơn của sinh viên (Phương thức GET)
        Route::get('/status/{student}', [RoomRegistrationController::class, 'showStatus'])->name('status');
    });
});

// Các route nghiệp vụ yêu cầu đăng nhập và phân quyền cho ADMIN
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        $stats = [
            'buildings' => \App\Models\Building::count(),
            'rooms'     => \App\Models\Room::count(),
            'beds'      => \App\Models\Bed::count(),
            'beds_available' => \App\Models\Bed::where('status', 'available')->count(),
        ];
        return view('admin.dashboard', compact('stats'));
    })->name('admin.dashboard');

    // ==========================================
    // MODULE 2: ĐĂNG KÝ CHỖ Ở (ROUTES) - ADMIN
    // ==========================================
    Route::prefix('registration')->name('registration.')->group(function () {
        // 4. Cán bộ duyệt / từ chối đơn (Phương thức PUT)
        Route::put('/update/{roomRegistration}', [RoomRegistrationController::class, 'updateStatus'])->name('update');

        // 5. Xem danh sách đơn chờ duyệt và danh sách chờ xếp phòng
        Route::get('/pending', [RoomRegistrationController::class, 'pending'])->name('pending');
        Route::get('/waitlist', [RoomRegistrationController::class, 'waitlist'])->name('waitlist');
    });
    // Resource routes
    Route::resource('buildings', BuildingController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('beds', BedController::class);

    // ==========================================
    // MODULE 3: PHÂN GIƯỜNG & HỢP ĐỒNG
    // ==========================================
    Route::prefix('contracts')->name('contracts.')->group(function () {
        Route::get('/', [ContractController::class, 'index'])->name('index');
        Route::get('/create', [ContractController::class, 'create'])->name('create');
        Route::get('/eligible-registrations', [ContractController::class, 'eligibleRegistrations'])
            ->name('eligible-registrations');
        Route::post('/', [ContractController::class, 'store'])->name('store');
        Route::get('/{contract}', [ContractController::class, 'show'])->name('show');
        Route::post('/{contract}/transfers', [ContractController::class, 'transfer'])->name('transfer');
        Route::post('/{contract}/renewals', [ContractController::class, 'renew'])->name('renew');
        Route::patch('/{contract}/terminate', [ContractController::class, 'terminate'])->name('terminate');
    });

    // Invoices routes
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::patch('/invoices/{id}/pay', [InvoiceController::class, 'pay'])->name('invoice.pay');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoice.show');

    // Violations routes
    Route::get('/violations', [ViolationRecordController::class, 'index'])->name('violation.index');
    Route::get('/violations/create', [ViolationRecordController::class, 'create'])->name('violation.create');
    Route::post('/violations', [ViolationRecordController::class, 'store'])->name('violation.store');
    Route::get('/violations/{id}', [ViolationRecordController::class, 'show'])->name('violation.show');
    Route::patch('/violations/{id}/resolve', [ViolationRecordController::class, 'resolve'])->name('violation.resolve');
});
