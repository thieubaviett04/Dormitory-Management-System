<?php

namespace App\Services;

use App\Enums\AllocationReleaseReason;
use App\Enums\ContractStatus;
use App\Enums\RoomRegistrationStatus;
use App\Models\Allocation;
use App\Models\Bed;
use App\Models\Contract;
use App\Models\Room;
use App\Models\RoomRegistration;
use App\Models\Student;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContractService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?int $actorId): Contract
    {
        try {
            return DB::transaction(function () use ($data, $actorId): Contract {
                $registration = RoomRegistration::query()
                    ->whereKey($data['room_registration_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($registration->status !== RoomRegistrationStatus::Approved) {
                    throw ValidationException::withMessages([
                        'room_registration_id' => 'Chỉ có thể lập hợp đồng từ đơn đã được duyệt.',
                    ]);
                }

                if ($registration->contract()->exists()) {
                    throw ValidationException::withMessages([
                        'room_registration_id' => 'Đơn đăng ký này đã được sử dụng để lập hợp đồng.',
                    ]);
                }

                $student = Student::query()
                    ->whereKey($registration->student_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($student->contracts()->active()->exists()) {
                    throw ValidationException::withMessages([
                        'room_registration_id' => 'Sinh viên đang có một hợp đồng còn hiệu lực.',
                    ]);
                }

                $room = Room::query()
                    ->whereKey($registration->room_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $bed = Bed::query()
                    ->whereKey($data['bed_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($bed->room_id !== $room->id) {
                    throw ValidationException::withMessages([
                        'bed_id' => 'Giường phải thuộc đúng phòng nguyện vọng trên đơn đã duyệt.',
                    ]);
                }

                $this->assertRoomCanAccept($room, $bed, $student);

                $contract = Contract::create([
                    'contract_code' => $this->nextContractCode($data['start_date']),
                    'room_registration_id' => $registration->id,
                    'student_id' => $student->id,
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'monthly_rate' => $data['monthly_rate'],
                    'status' => ContractStatus::Active,
                    'signed_at' => now(),
                    'created_by' => $actorId,
                ]);

                Allocation::create([
                    'contract_id' => $contract->id,
                    'bed_id' => $bed->id,
                    'allocated_at' => now(),
                    'allocated_by' => $actorId,
                    'notes' => $data['notes'] ?? null,
                ]);

                $bed->update(['status' => 'occupied']);
                $this->syncRoomStatus($room);

                return $this->loadContract($contract);
            }, 3);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages([
                'contract' => 'Đơn đăng ký, sinh viên hoặc giường vừa được sử dụng bởi một giao dịch khác.',
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function transfer(Contract $contract, array $data, ?int $actorId): Contract
    {
        try {
            return DB::transaction(function () use ($contract, $data, $actorId): Contract {
                $lockedContract = $this->lockActiveContract($contract);
                $currentAllocation = $this->lockCurrentAllocation($lockedContract);

                if ($currentAllocation->bed_id === (int) $data['bed_id']) {
                    throw ValidationException::withMessages([
                        'bed_id' => 'Giường mới phải khác giường hiện tại.',
                    ]);
                }

                $currentBedSnapshot = Bed::query()->findOrFail($currentAllocation->bed_id);
                $targetBedSnapshot = Bed::query()->findOrFail($data['bed_id']);
                $rooms = $this->lockRooms([$currentBedSnapshot->room_id, $targetBedSnapshot->room_id]);
                $beds = $this->lockBeds([$currentBedSnapshot->id, $targetBedSnapshot->id]);

                /** @var Bed $currentBed */
                $currentBed = $beds->get($currentBedSnapshot->id);
                /** @var Bed $targetBed */
                $targetBed = $beds->get($targetBedSnapshot->id);
                /** @var Room $currentRoom */
                $currentRoom = $rooms->get($currentBed->room_id);
                /** @var Room $targetRoom */
                $targetRoom = $rooms->get($targetBed->room_id);
                $student = Student::query()->findOrFail($lockedContract->student_id);

                $this->assertRoomCanAccept(
                    $targetRoom,
                    $targetBed,
                    $student,
                    $currentAllocation->id,
                );

                $transferredAt = $data['transferred_at'] ?? now();
                if (CarbonImmutable::parse($transferredAt)->isBefore($currentAllocation->allocated_at)) {
                    throw ValidationException::withMessages([
                        'transferred_at' => 'Thời điểm chuyển phòng không được trước thời điểm nhận giường.',
                    ]);
                }

                $currentAllocation->update([
                    'released_at' => $transferredAt,
                    'release_reason' => AllocationReleaseReason::Transferred,
                    'released_by' => $actorId,
                    'release_notes' => $data['reason'],
                ]);

                $currentBed->update(['status' => 'available']);

                Allocation::create([
                    'contract_id' => $lockedContract->id,
                    'bed_id' => $targetBed->id,
                    'allocated_at' => $transferredAt,
                    'allocated_by' => $actorId,
                    'notes' => $data['reason'],
                ]);

                $targetBed->update(['status' => 'occupied']);
                $this->syncRoomStatus($currentRoom);
                if ($targetRoom->id !== $currentRoom->id) {
                    $this->syncRoomStatus($targetRoom);
                }

                return $this->loadContract($lockedContract);
            }, 3);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages([
                'bed_id' => 'Giường mới vừa được phân cho một hợp đồng khác.',
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function renew(Contract $contract, array $data, ?int $actorId): Contract
    {
        return DB::transaction(function () use ($contract, $data, $actorId): Contract {
            $lockedContract = $this->lockActiveContract($contract);
            $newEndDate = CarbonImmutable::parse($data['new_end_date'])->startOfDay();

            if (! $newEndDate->isAfter($lockedContract->end_date)) {
                throw ValidationException::withMessages([
                    'new_end_date' => 'Ngày hết hạn mới phải sau ngày hết hạn hiện tại.',
                ]);
            }

            $lockedContract->renewals()->create([
                'previous_end_date' => $lockedContract->end_date->toDateString(),
                'new_end_date' => $newEndDate->toDateString(),
                'renewed_at' => now(),
                'renewed_by' => $actorId,
                'reason' => $data['reason'] ?? null,
            ]);

            $lockedContract->update(['end_date' => $newEndDate->toDateString()]);

            return $this->loadContract($lockedContract);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function terminate(Contract $contract, array $data, ?int $actorId): Contract
    {
        return DB::transaction(function () use ($contract, $data, $actorId): Contract {
            $lockedContract = $this->lockActiveContract($contract);
            $allocation = $this->lockCurrentAllocation($lockedContract);
            $bedSnapshot = Bed::query()->findOrFail($allocation->bed_id);
            $room = Room::query()->whereKey($bedSnapshot->room_id)->lockForUpdate()->firstOrFail();
            $bed = Bed::query()->whereKey($bedSnapshot->id)->lockForUpdate()->firstOrFail();
            $terminatedAt = $data['terminated_at'] ?? now();

            if (CarbonImmutable::parse($terminatedAt)->isBefore($allocation->allocated_at)) {
                throw ValidationException::withMessages([
                    'terminated_at' => 'Thời điểm trả phòng không được trước thời điểm nhận giường.',
                ]);
            }

            $releaseReason = AllocationReleaseReason::from(
                $data['release_reason'] ?? AllocationReleaseReason::CheckedOut->value,
            );

            $allocation->update([
                'released_at' => $terminatedAt,
                'release_reason' => $releaseReason,
                'released_by' => $actorId,
                'release_notes' => $data['reason'],
            ]);

            $bed->update(['status' => 'available']);
            $lockedContract->update([
                'status' => ContractStatus::Terminated,
                'terminated_at' => $terminatedAt,
                'termination_reason' => $data['reason'],
            ]);
            $this->completeRegistration($lockedContract, $actorId, $terminatedAt);
            $this->syncRoomStatus($room);

            return $this->loadContract($lockedContract);
        });
    }

    public function expireDueContracts(): int
    {
        $expiredCount = 0;

        Contract::query()
            ->active()
            ->whereDate('end_date', '<', today())
            ->orderBy('id')
            ->pluck('id')
            ->each(function (int $contractId) use (&$expiredCount): void {
                if ($this->expire(Contract::query()->findOrFail($contractId))) {
                    $expiredCount++;
                }
            });

        return $expiredCount;
    }

    public function expire(Contract $contract): bool
    {
        return DB::transaction(function () use ($contract): bool {
            $lockedContract = Contract::query()
                ->whereKey($contract->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $lockedContract->status !== ContractStatus::Active
                || ! $lockedContract->end_date->isBefore(today())
            ) {
                return false;
            }

            $allocation = Allocation::query()
                ->where('contract_id', $lockedContract->id)
                ->active()
                ->lockForUpdate()
                ->first();

            if ($allocation) {
                $bedSnapshot = Bed::query()->findOrFail($allocation->bed_id);
                $room = Room::query()->whereKey($bedSnapshot->room_id)->lockForUpdate()->firstOrFail();
                $bed = Bed::query()->whereKey($bedSnapshot->id)->lockForUpdate()->firstOrFail();

                $allocation->update([
                    'released_at' => now(),
                    'release_reason' => AllocationReleaseReason::ContractExpired,
                    'release_notes' => 'Hợp đồng hết hạn.',
                ]);
                $bed->update(['status' => 'available']);
                $this->syncRoomStatus($room);
            }

            $lockedContract->update(['status' => ContractStatus::Expired]);
            $this->completeRegistration($lockedContract, null, now());

            return true;
        });
    }

    private function lockActiveContract(Contract $contract): Contract
    {
        $lockedContract = Contract::query()
            ->whereKey($contract->getKey())
            ->lockForUpdate()
            ->firstOrFail();

        if ($lockedContract->status !== ContractStatus::Active) {
            throw ValidationException::withMessages([
                'contract' => 'Chỉ có thể thao tác trên hợp đồng đang hiệu lực.',
            ]);
        }

        return $lockedContract;
    }

    private function lockCurrentAllocation(Contract $contract): Allocation
    {
        $allocation = Allocation::query()
            ->where('contract_id', $contract->id)
            ->active()
            ->lockForUpdate()
            ->first();

        if (! $allocation) {
            throw ValidationException::withMessages([
                'contract' => 'Hợp đồng không có giường đang được phân.',
            ]);
        }

        return $allocation;
    }

    /**
     * @param  list<int>  $roomIds
     * @return Collection<int, Room>
     */
    private function lockRooms(array $roomIds): Collection
    {
        return Room::query()
            ->whereIn('id', array_unique($roomIds))
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    /**
     * @param  list<int>  $bedIds
     * @return Collection<int, Bed>
     */
    private function lockBeds(array $bedIds): Collection
    {
        return Bed::query()
            ->whereIn('id', array_unique($bedIds))
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    private function assertRoomCanAccept(
        Room $room,
        Bed $bed,
        Student $student,
        ?int $excludedAllocationId = null,
    ): void {
        $room->loadMissing('building');

        if ($room->status === 'maintenance') {
            throw ValidationException::withMessages([
                'bed_id' => 'Không thể phân giường trong phòng đang bảo trì.',
            ]);
        }

        if ($bed->status !== 'available') {
            throw ValidationException::withMessages([
                'bed_id' => 'Giường không còn trống hoặc đang bảo trì.',
            ]);
        }

        $targetBedAllocation = Allocation::query()
            ->active()
            ->where('bed_id', $bed->id)
            ->when($excludedAllocationId, fn ($query) => $query->whereKeyNot($excludedAllocationId));

        if ($targetBedAllocation->exists()) {
            throw ValidationException::withMessages([
                'bed_id' => 'Giường đã được phân cho một hợp đồng khác.',
            ]);
        }

        $activeAllocations = Allocation::query()
            ->active()
            ->whereHas('bed', fn ($query) => $query->where('room_id', $room->id))
            ->when($excludedAllocationId, fn ($query) => $query->whereKeyNot($excludedAllocationId));

        if ((clone $activeAllocations)->count() >= $room->capacity) {
            throw ValidationException::withMessages([
                'bed_id' => 'Phòng đã đạt sức chứa tối đa.',
            ]);
        }

        $genderPolicy = $room->building->gender_policy;
        if ($genderPolicy !== 'mixed' && $genderPolicy !== $student->gender) {
            throw ValidationException::withMessages([
                'bed_id' => 'Giới tính sinh viên không phù hợp với chính sách của tòa nhà.',
            ]);
        }

        $hasDifferentGender = (clone $activeAllocations)
            ->whereHas('contract.student', fn ($query) => $query->where('gender', '!=', $student->gender))
            ->exists();

        if ($hasDifferentGender) {
            throw ValidationException::withMessages([
                'bed_id' => 'Không thể xếp sinh viên khác giới tính vào cùng phòng.',
            ]);
        }
    }

    private function syncRoomStatus(Room $room): void
    {
        if ($room->status === 'maintenance') {
            return;
        }

        $activeCount = Allocation::query()
            ->active()
            ->whereHas('bed', fn ($query) => $query->where('room_id', $room->id))
            ->count();
        $hasAvailableBed = Bed::query()
            ->where('room_id', $room->id)
            ->where('status', 'available')
            ->exists();

        $room->update([
            'status' => $activeCount >= $room->capacity || ! $hasAvailableBed
                ? 'full'
                : 'available',
        ]);
    }

    private function completeRegistration(Contract $contract, ?int $actorId, mixed $completedAt): void
    {
        $registration = RoomRegistration::query()
            ->whereKey($contract->room_registration_id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($registration->status === RoomRegistrationStatus::Approved) {
            $registration->update([
                'status' => RoomRegistrationStatus::Completed,
                'completed_at' => $completedAt,
                'completed_by' => $actorId,
            ]);
        }
    }

    private function nextContractCode(mixed $startDate): string
    {
        $year = CarbonImmutable::parse($startDate)->year;

        DB::table('contract_sequences')->insertOrIgnore([
            'year' => $year,
            'last_number' => 0,
        ]);

        $sequence = DB::table('contract_sequences')
            ->where('year', $year)
            ->lockForUpdate()
            ->first();
        $nextNumber = ((int) $sequence->last_number) + 1;

        DB::table('contract_sequences')
            ->where('year', $year)
            ->update(['last_number' => $nextNumber]);

        return sprintf('HD-%d-%04d', $year, $nextNumber);
    }

    private function loadContract(Contract $contract): Contract
    {
        return $contract->load([
            'student',
            'roomRegistration.room.building',
            'creator',
            'currentAllocation.bed.room.building',
            'allocations' => fn ($query) => $query->oldest('allocated_at'),
            'allocations.bed.room.building',
            'allocations.allocator',
            'allocations.releaser',
            'renewals' => fn ($query) => $query->oldest('renewed_at'),
            'renewals.renewer',
        ]);
    }
}
