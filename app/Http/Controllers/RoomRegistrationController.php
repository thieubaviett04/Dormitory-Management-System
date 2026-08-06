<?php

namespace App\Http\Controllers;

use App\Enums\RoomRegistrationStatus;
use App\Http\Requests\CancelRoomRegistrationRequest;
use App\Http\Requests\StoreRoomRegistrationRequest;
use App\Http\Requests\UpdateRoomRegistrationStatusRequest;
use App\Models\RoomRegistration;
use App\Models\Student;
use App\Services\RoomRegistrationService;
use Illuminate\Http\JsonResponse;

class RoomRegistrationController extends Controller
{
    public function __construct(private readonly RoomRegistrationService $service) {}

    public function store(StoreRoomRegistrationRequest $request): JsonResponse
    {
        $registration = $this->service->register($request->validated());

        return response()->json([
            'message' => 'Đăng ký phòng thành công! Vui lòng chờ cán bộ duyệt.',
            'data' => $registration->load(['student', 'room.building']),
        ], 201);
    }

    public function cancel(
        CancelRoomRegistrationRequest $request,
        RoomRegistration $roomRegistration,
    ): JsonResponse {
        $registration = $this->service->cancel(
            $roomRegistration,
            $request->validated('cancellation_reason'),
        );

        return response()->json([
            'message' => 'Hủy đơn đăng ký thành công!',
            'data' => $registration,
        ]);
    }

    public function showStatus(Student $student): JsonResponse
    {
        $registrations = $student->roomRegistrations()
            ->with('room.building')
            ->latest('registered_at')
            ->get();

        if ($registrations->isEmpty()) {
            return response()->json([
                'message' => 'Sinh viên này chưa có đơn đăng ký nào.',
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin trạng thái thành công.',
            'data' => $registrations,
        ]);
    }

    public function updateStatus(
        UpdateRoomRegistrationStatusRequest $request,
        RoomRegistration $roomRegistration,
    ): JsonResponse {
        $registration = $this->service->transition(
            $roomRegistration,
            RoomRegistrationStatus::from($request->validated('status')),
            $request->user()?->id,
            $request->validated('rejected_reason'),
        );

        return response()->json([
            'message' => 'Cập nhật trạng thái đơn đăng ký thành công.',
            'data' => $registration->load(['student', 'room.building', 'reviewer']),
        ]);
    }

    public function pending(): JsonResponse
    {
        return $this->registrationList(
            RoomRegistrationStatus::Pending,
            'Lấy danh sách đơn chờ duyệt thành công.',
        );
    }

    public function waitlist(): JsonResponse
    {
        return $this->registrationList(
            RoomRegistrationStatus::Waitlist,
            'Lấy danh sách chờ thành công.',
        );
    }

    private function registrationList(
        RoomRegistrationStatus $status,
        string $message,
    ): JsonResponse {
        $registrations = RoomRegistration::query()
            ->with(['student', 'room.building'])
            ->where('status', $status->value)
            ->oldest('registered_at')
            ->get();

        return response()->json([
            'message' => $message,
            'data' => $registrations,
        ]);
    }
}
