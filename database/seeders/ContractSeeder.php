<?php

namespace Database\Seeders;

use App\Enums\AllocationReleaseReason;
use App\Enums\ContractStatus;
use App\Enums\RoomRegistrationStatus;
use App\Models\Allocation;
use App\Models\Bed;
use App\Models\Contract;
use App\Models\ContractRenewal;
use App\Models\Room;
use App\Models\RoomRegistration;
use App\Models\Student;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ContractSeeder extends Seeder
{
    /**
     * Seed representative contract-management scenarios.
     *
     * The records are intentionally identified by stable `SEED-` codes and can
     * be safely re-run on the same database. This seeder requires the building,
     * room, and bed seeders to have run first.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $today = CarbonImmutable::today();
            $actorId = User::query()->orderBy('id')->value('id');

            $this->seedActiveContract(
                contractCode: 'SEED-HD-ACTIVE-001',
                student: [
                    'student_code' => 'SEED2026001',
                    'full_name' => 'Nguyễn Minh An',
                    'email' => 'seed.minh.an@example.test',
                    'phone_number' => '0901000001',
                    'gender' => 'male',
                    'date_of_birth' => '2005-03-15',
                ],
                room: $this->room('A1', '101'),
                currentBed: $this->bed('A1', '101', 'G1'),
                startDate: $today->subMonths(2),
                endDate: $today->addMonths(4),
                monthlyRate: 1200000,
                actorId: $actorId,
                notes: 'Dữ liệu mẫu: hợp đồng đang hiệu lực.',
            );

            $this->seedTransferredContract(
                contractCode: 'SEED-HD-TRANSFER-001',
                student: [
                    'student_code' => 'SEED2026002',
                    'full_name' => 'Trần Khánh Linh',
                    'email' => 'seed.khanh.linh@example.test',
                    'phone_number' => '0901000002',
                    'gender' => 'female',
                    'date_of_birth' => '2005-08-21',
                ],
                room: $this->room('T1', '101'),
                previousBed: $this->bed('T1', '101', 'G2'),
                currentBed: $this->bed('T1', '101', 'G1'),
                startDate: $today->subMonths(3),
                endDate: $today->addMonths(3),
                monthlyRate: 1350000,
                transferredAt: $today->subMonth()->setTime(10, 0),
                actorId: $actorId,
            );

            $this->seedRenewedContract(
                contractCode: 'SEED-HD-RENEW-001',
                student: [
                    'student_code' => 'SEED2026003',
                    'full_name' => 'Lê Hoàng Nam',
                    'email' => 'seed.hoang.nam@example.test',
                    'phone_number' => '0901000003',
                    'gender' => 'male',
                    'date_of_birth' => '2004-11-02',
                ],
                room: $this->room('A3', '101'),
                currentBed: $this->bed('A3', '101', 'G1'),
                startDate: $today->subMonths(5),
                previousEndDate: $today->addMonth(),
                endDate: $today->addMonths(7),
                monthlyRate: 1500000,
                actorId: $actorId,
            );

            $this->seedClosedContract(
                contractCode: 'SEED-HD-TERMINATED-001',
                student: [
                    'student_code' => 'SEED2026004',
                    'full_name' => 'Phạm Quốc Bảo',
                    'email' => 'seed.quoc.bao@example.test',
                    'phone_number' => '0901000004',
                    'gender' => 'male',
                    'date_of_birth' => '2005-01-30',
                ],
                room: $this->room('A1', '102'),
                bed: $this->bed('A1', '102', 'G1'),
                startDate: $today->subMonths(4),
                endDate: $today->addMonths(2),
                monthlyRate: 1200000,
                status: ContractStatus::Terminated,
                closedAt: $today->subDays(10)->setTime(16, 30),
                releaseReason: AllocationReleaseReason::CheckedOut,
                reason: 'Dữ liệu mẫu: sinh viên trả phòng trước hạn.',
                actorId: $actorId,
            );

            $this->seedClosedContract(
                contractCode: 'SEED-HD-EXPIRED-001',
                student: [
                    'student_code' => 'SEED2026005',
                    'full_name' => 'Đỗ Thu Hà',
                    'email' => 'seed.thu.ha@example.test',
                    'phone_number' => '0901000005',
                    'gender' => 'female',
                    'date_of_birth' => '2005-06-12',
                ],
                room: $this->room('A3', '102'),
                bed: $this->bed('A3', '102', 'G1'),
                startDate: $today->subMonths(5),
                endDate: $today->subDay(),
                monthlyRate: 1500000,
                status: ContractStatus::Expired,
                closedAt: $today->subDay()->setTime(23, 0),
                releaseReason: AllocationReleaseReason::ContractExpired,
                reason: 'Dữ liệu mẫu: hợp đồng đã hết hạn.',
                actorId: $actorId,
            );

            $this->syncSeededRoomStatuses();
        });
    }

    /**
     * @param  array<string, string>  $student
     */
    private function seedActiveContract(
        string $contractCode,
        array $student,
        Room $room,
        Bed $currentBed,
        CarbonImmutable $startDate,
        CarbonImmutable $endDate,
        int $monthlyRate,
        ?int $actorId,
        string $notes,
    ): void {
        $studentModel = $this->upsertStudent($student);
        $registration = $this->upsertRegistration(
            student: $studentModel,
            room: $room,
            status: RoomRegistrationStatus::Approved,
            registeredAt: $startDate->subDays(7)->setTime(8, 0),
            actorId: $actorId,
        );
        $contract = $this->upsertContract(
            contractCode: $contractCode,
            registration: $registration,
            student: $studentModel,
            startDate: $startDate,
            endDate: $endDate,
            monthlyRate: $monthlyRate,
            status: ContractStatus::Active,
            signedAt: $startDate->setTime(9, 0),
            actorId: $actorId,
        );

        $this->upsertAllocation(
            contract: $contract,
            bed: $currentBed,
            allocatedAt: $startDate->setTime(9, 30),
            actorId: $actorId,
            notes: $notes,
        );
        $this->markBedOccupied($currentBed);
    }

    /**
     * @param  array<string, string>  $student
     */
    private function seedTransferredContract(
        string $contractCode,
        array $student,
        Room $room,
        Bed $previousBed,
        Bed $currentBed,
        CarbonImmutable $startDate,
        CarbonImmutable $endDate,
        int $monthlyRate,
        CarbonImmutable $transferredAt,
        ?int $actorId,
    ): void {
        $studentModel = $this->upsertStudent($student);
        $registration = $this->upsertRegistration(
            student: $studentModel,
            room: $room,
            status: RoomRegistrationStatus::Approved,
            registeredAt: $startDate->subDays(7)->setTime(8, 0),
            actorId: $actorId,
        );
        $contract = $this->upsertContract(
            contractCode: $contractCode,
            registration: $registration,
            student: $studentModel,
            startDate: $startDate,
            endDate: $endDate,
            monthlyRate: $monthlyRate,
            status: ContractStatus::Active,
            signedAt: $startDate->setTime(9, 0),
            actorId: $actorId,
        );

        $this->upsertAllocation(
            contract: $contract,
            bed: $previousBed,
            allocatedAt: $startDate->setTime(9, 30),
            actorId: $actorId,
            notes: 'Dữ liệu mẫu: giường ban đầu trước khi chuyển phòng.',
            releasedAt: $transferredAt,
            releaseReason: AllocationReleaseReason::Transferred,
            releaseNotes: 'Dữ liệu mẫu: chuyển sang giường G1 cùng phòng.',
        );
        $this->upsertAllocation(
            contract: $contract,
            bed: $currentBed,
            allocatedAt: $transferredAt,
            actorId: $actorId,
            notes: 'Dữ liệu mẫu: giường hiện tại sau khi chuyển phòng.',
        );
        $this->markBedAvailable($previousBed);
        $this->markBedOccupied($currentBed);
    }

    /**
     * @param  array<string, string>  $student
     */
    private function seedRenewedContract(
        string $contractCode,
        array $student,
        Room $room,
        Bed $currentBed,
        CarbonImmutable $startDate,
        CarbonImmutable $previousEndDate,
        CarbonImmutable $endDate,
        int $monthlyRate,
        ?int $actorId,
    ): void {
        $studentModel = $this->upsertStudent($student);
        $registration = $this->upsertRegistration(
            student: $studentModel,
            room: $room,
            status: RoomRegistrationStatus::Approved,
            registeredAt: $startDate->subDays(7)->setTime(8, 0),
            actorId: $actorId,
        );
        $contract = $this->upsertContract(
            contractCode: $contractCode,
            registration: $registration,
            student: $studentModel,
            startDate: $startDate,
            endDate: $endDate,
            monthlyRate: $monthlyRate,
            status: ContractStatus::Active,
            signedAt: $startDate->setTime(9, 0),
            actorId: $actorId,
        );

        $this->upsertAllocation(
            contract: $contract,
            bed: $currentBed,
            allocatedAt: $startDate->setTime(9, 30),
            actorId: $actorId,
            notes: 'Dữ liệu mẫu: hợp đồng đã được gia hạn.',
        );
        ContractRenewal::query()->updateOrCreate(
            [
                'contract_id' => $contract->id,
                'reason' => 'Dữ liệu mẫu: gia hạn học kỳ tiếp theo.',
            ],
            [
                'previous_end_date' => $previousEndDate->toDateString(),
                'new_end_date' => $endDate->toDateString(),
                'renewed_at' => $previousEndDate->subDays(7)->setTime(14, 0),
                'renewed_by' => $actorId,
            ],
        );
        $this->markBedOccupied($currentBed);
    }

    /**
     * @param  array<string, string>  $student
     */
    private function seedClosedContract(
        string $contractCode,
        array $student,
        Room $room,
        Bed $bed,
        CarbonImmutable $startDate,
        CarbonImmutable $endDate,
        int $monthlyRate,
        ContractStatus $status,
        CarbonImmutable $closedAt,
        AllocationReleaseReason $releaseReason,
        string $reason,
        ?int $actorId,
    ): void {
        $studentModel = $this->upsertStudent($student);
        $registration = $this->upsertRegistration(
            student: $studentModel,
            room: $room,
            status: RoomRegistrationStatus::Completed,
            registeredAt: $startDate->subDays(7)->setTime(8, 0),
            actorId: $actorId,
            completedAt: $closedAt,
        );
        $contract = $this->upsertContract(
            contractCode: $contractCode,
            registration: $registration,
            student: $studentModel,
            startDate: $startDate,
            endDate: $endDate,
            monthlyRate: $monthlyRate,
            status: $status,
            signedAt: $startDate->setTime(9, 0),
            actorId: $actorId,
            terminatedAt: $status === ContractStatus::Terminated ? $closedAt : null,
            terminationReason: $status === ContractStatus::Terminated ? $reason : null,
        );

        $this->upsertAllocation(
            contract: $contract,
            bed: $bed,
            allocatedAt: $startDate->setTime(9, 30),
            actorId: $actorId,
            notes: 'Dữ liệu mẫu: phân giường đã kết thúc.',
            releasedAt: $closedAt,
            releaseReason: $releaseReason,
            releaseNotes: $reason,
        );
        $this->markBedAvailable($bed);
    }

    /**
     * @param  array<string, string>  $data
     */
    private function upsertStudent(array $data): Student
    {
        return Student::query()->updateOrCreate(
            ['student_code' => $data['student_code']],
            $data,
        );
    }

    private function upsertRegistration(
        Student $student,
        Room $room,
        RoomRegistrationStatus $status,
        CarbonImmutable $registeredAt,
        ?int $actorId,
        ?CarbonImmutable $completedAt = null,
    ): RoomRegistration {
        return RoomRegistration::query()->updateOrCreate(
            [
                'student_id' => $student->id,
                'room_id' => $room->id,
            ],
            [
                'status' => $status,
                'registered_at' => $registeredAt,
                'reviewed_at' => $registeredAt->addDay(),
                'reviewed_by' => $actorId,
                'completed_at' => $completedAt,
                'completed_by' => $completedAt ? $actorId : null,
            ],
        );
    }

    private function upsertContract(
        string $contractCode,
        RoomRegistration $registration,
        Student $student,
        CarbonImmutable $startDate,
        CarbonImmutable $endDate,
        int $monthlyRate,
        ContractStatus $status,
        CarbonImmutable $signedAt,
        ?int $actorId,
        ?CarbonImmutable $terminatedAt = null,
        ?string $terminationReason = null,
    ): Contract {
        return Contract::query()->updateOrCreate(
            ['contract_code' => $contractCode],
            [
                'room_registration_id' => $registration->id,
                'student_id' => $student->id,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'monthly_rate' => $monthlyRate,
                'status' => $status,
                'signed_at' => $signedAt,
                'terminated_at' => $terminatedAt,
                'termination_reason' => $terminationReason,
                'created_by' => $actorId,
            ],
        );
    }

    private function upsertAllocation(
        Contract $contract,
        Bed $bed,
        CarbonImmutable $allocatedAt,
        ?int $actorId,
        string $notes,
        ?CarbonImmutable $releasedAt = null,
        ?AllocationReleaseReason $releaseReason = null,
        ?string $releaseNotes = null,
    ): Allocation {
        $existingAllocation = Allocation::query()
            ->where('contract_id', $contract->id)
            ->where('notes', $notes)
            ->first();

        if ($existingAllocation && $existingAllocation->bed_id !== $bed->id) {
            throw new RuntimeException(
                "Allocation dữ liệu mẫu của hợp đồng {$contract->contract_code} đang tham chiếu đến một giường khác.",
            );
        }

        return Allocation::query()->updateOrCreate(
            [
                'contract_id' => $contract->id,
                'notes' => $notes,
            ],
            [
                'bed_id' => $bed->id,
                'allocated_at' => $allocatedAt,
                'released_at' => $releasedAt,
                'release_reason' => $releaseReason,
                'allocated_by' => $actorId,
                'released_by' => $releasedAt ? $actorId : null,
                'release_notes' => $releaseNotes,
            ],
        );
    }

    private function room(string $buildingCode, string $roomNumber): Room
    {
        return Room::query()
            ->where('room_number', $roomNumber)
            ->whereHas('building', fn ($query) => $query->where('code', $buildingCode))
            ->firstOrFail();
    }

    private function bed(string $buildingCode, string $roomNumber, string $bedNumber): Bed
    {
        return Bed::query()
            ->where('bed_number', $bedNumber)
            ->whereHas(
                'room',
                fn ($query) => $query
                    ->where('room_number', $roomNumber)
                    ->whereHas('building', fn ($buildingQuery) => $buildingQuery->where('code', $buildingCode)),
            )
            ->firstOrFail();
    }

    private function markBedOccupied(Bed $bed): void
    {
        $bed->update(['status' => 'occupied']);
    }

    private function markBedAvailable(Bed $bed): void
    {
        if ($bed->status !== 'maintenance') {
            $bed->update(['status' => 'available']);
        }
    }

    private function syncSeededRoomStatuses(): void
    {
        foreach ([
            ['A1', '101'],
            ['A1', '102'],
            ['A3', '101'],
            ['A3', '102'],
            ['T1', '101'],
        ] as [$buildingCode, $roomNumber]) {
            $room = $this->room($buildingCode, $roomNumber);
            if ($room->status === 'maintenance') {
                continue;
            }

            $hasAvailableBed = $room->beds()->where('status', 'available')->exists();
            $room->update(['status' => $hasAvailableBed ? 'available' : 'full']);
        }
    }
}
