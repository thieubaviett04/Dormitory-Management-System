<?php

namespace App\Services;

use App\Enums\RoomRegistrationStatus;
use App\Models\RoomRegistration;
use App\Models\Student;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoomRegistrationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function register(array $data): RoomRegistration
    {
        try {
            return DB::transaction(function () use ($data): RoomRegistration {
                $student = Student::query()
                    ->where('student_code', $data['student_code'])
                    ->lockForUpdate()
                    ->first();

                $studentData = [
                    'full_name' => $data['full_name'],
                    'email' => $data['email'],
                    'phone_number' => $data['phone_number'] ?? null,
                    'gender' => $data['gender'],
                    'date_of_birth' => $data['date_of_birth'],
                ];

                if ($student) {
                    $student->update($studentData);
                } else {
                    $student = Student::create([
                        'student_code' => $data['student_code'],
                        ...$studentData,
                    ]);
                }

                if ($student->roomRegistrations()->active()->exists()) {
                    throw ValidationException::withMessages([
                        'student_code' => 'Sinh viên đã có một đơn đăng ký đang hoạt động.',
                    ]);
                }

                $registration = $student->roomRegistrations()->create([
                    'room_id' => $data['room_id'],
                    'status' => RoomRegistrationStatus::Pending,
                    'registered_at' => now(),
                ]);

                return $registration->load(['student', 'room']);
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages([
                'student_code' => 'Thông tin sinh viên hoặc đơn đăng ký đang bị trùng.',
            ]);
        }
    }

    public function transition(
        RoomRegistration $registration,
        RoomRegistrationStatus $target,
        ?int $reviewedBy,
        ?string $rejectedReason,
    ): RoomRegistration {
        return DB::transaction(function () use ($registration, $target, $rejectedReason, $reviewedBy): RoomRegistration {
            $lockedRegistration = RoomRegistration::query()
                ->whereKey($registration->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedRegistration->status->canTransitionTo($target)) {
                throw ValidationException::withMessages([
                    'status' => "Không thể chuyển trạng thái từ {$lockedRegistration->status->value} sang {$target->value}.",
                ]);
            }

            $isReviewed = in_array($target, [
                RoomRegistrationStatus::Approved,
                RoomRegistrationStatus::Rejected,
            ], true);

            $lockedRegistration->update([
                'status' => $target,
                'reviewed_at' => $isReviewed ? now() : null,
                'reviewed_by' => $isReviewed ? $reviewedBy : null,
                'rejected_reason' => $target === RoomRegistrationStatus::Rejected ? $rejectedReason : null,
            ]);

            return $lockedRegistration->load(['student', 'room', 'reviewer']);
        });
    }

    public function cancel(RoomRegistration $registration, ?string $reason): RoomRegistration
    {
        return DB::transaction(function () use ($registration, $reason): RoomRegistration {
            $lockedRegistration = RoomRegistration::query()
                ->whereKey($registration->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedRegistration->status->canTransitionTo(RoomRegistrationStatus::Cancelled)) {
                throw ValidationException::withMessages([
                    'status' => 'Chỉ có thể hủy đơn đang chờ duyệt hoặc trong danh sách chờ.',
                ]);
            }

            $lockedRegistration->update([
                'status' => RoomRegistrationStatus::Cancelled,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            return $lockedRegistration->load(['student', 'room']);
        });
    }
}
