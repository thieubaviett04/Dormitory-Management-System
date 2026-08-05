<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\RoomRegistration;

class RoomRegistrationController extends Controller
{
    // ---------------------------------------------------
    // Chức năng 1: Sinh viên đăng ký chỗ ở
    // ---------------------------------------------------
    public function store(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validated = $request->validate([
            'student_code' => 'required|string|max:20',
            'full_name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone_number' => 'nullable|string|max:15',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'room_id' => 'required|integer',
        ]);

        // Tìm sinh viên hoặc tạo mới nếu chưa có
        $student = Student::firstOrCreate(
            ['student_code' => $validated['student_code']],
            [
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
            ]
        );

        // Tạo đơn đăng ký mới (Mặc định trạng thái là 'pending')
        $registration = RoomRegistration::create([
            'student_id' => $student->id,
            'room_id' => $validated['room_id'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Đăng ký phòng thành công! Vui lòng chờ cán bộ duyệt.',
            'data' => $registration
        ], 201);
    }

    // ---------------------------------------------------
    // Chức năng 2: Hủy đăng ký
    // ---------------------------------------------------
    public function cancel($id)
    {
        $registration = RoomRegistration::findOrFail($id);

        // Chỉ cho phép hủy nếu đơn vẫn đang ở trạng thái chờ duyệt (pending)
        if ($registration->status !== 'pending') {
            return response()->json([
                'message' => 'Không thể hủy đơn vì đơn đã được cán bộ xử lý.'
            ], 400);
        }

        // Xóa đơn khỏi cơ sở dữ liệu
        $registration->delete();

        return response()->json([
            'message' => 'Đã hủy đăng ký thành công.'
        ]);
    }

    // ---------------------------------------------------
    // Chức năng 3: Xem trạng thái
    // ---------------------------------------------------
    public function showStatus($student_id)
    {
        // Lấy tất cả các đơn đăng ký của một sinh viên cụ thể
        $registrations = RoomRegistration::where('student_id', $student_id)->get();

        if ($registrations->isEmpty()) {
            return response()->json(['message' => 'Sinh viên này chưa có đơn đăng ký nào.'], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin trạng thái thành công.',
            'data' => $registrations
        ]);
    }

    // ---------------------------------------------------
    // Chức năng 4: Cán bộ duyệt / từ chối
    // ---------------------------------------------------
    public function updateStatus(Request $request, $id)
    {
        // Cán bộ chỉ được phép gửi lên 1 trong 3 trạng thái này
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,waitlist'
        ]);

        $registration = RoomRegistration::findOrFail($id);

        // Cập nhật trạng thái mới
        $registration->update([
            'status' => $validated['status']
        ]);

        return response()->json([
            'message' => 'Cập nhật trạng thái đơn đăng ký thành công.',
            'data' => $registration
        ]);
    }

    // ---------------------------------------------------
    // Chức năng 5: Quản lý danh sách chờ
    // ---------------------------------------------------
    public function waitlist()
    {
        // Lấy toàn bộ danh sách các đơn đang bị đưa vào hàng chờ (waitlist)
        $waitlistRegistrations = RoomRegistration::where('status', 'waitlist')->get();

        return response()->json([
            'message' => 'Danh sách sinh viên đang chờ phòng.',
            'total' => $waitlistRegistrations->count(),
            'data' => $waitlistRegistrations
        ]);
    }
}
