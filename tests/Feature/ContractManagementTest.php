<?php

namespace Tests\Feature;

use App\Enums\AllocationReleaseReason;
use App\Enums\ContractStatus;
use App\Enums\RoomRegistrationStatus;
use App\Models\Allocation;
use App\Models\Bed;
use App\Models\Building;
use App\Models\Contract;
use App\Models\Room;
use App\Models\RoomRegistration;
use App\Models\Student;
use App\Models\User;
use App\Services\ContractService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_contract_and_initial_allocation_atomically(): void
    {
        [$registration, $bed] = $this->approvedRegistrationWithBed();
        $manager = User::factory()->create();

        $response = $this->actingAs($manager)->postJson(
            route('contracts.store'),
            $this->contractPayload($registration, $bed),
        );

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', ContractStatus::Active->value)
            ->assertJsonPath('data.student_id', $registration->student_id)
            ->assertJsonPath('data.current_allocation.bed_id', $bed->id)
            ->assertJsonStructure(['message', 'data' => ['allocations', 'room_registration']]);

        $contract = Contract::query()->firstOrFail();
        $this->assertStringStartsWith('HD-'.today()->year.'-', $contract->contract_code);
        $this->assertSame($registration->student_id, $contract->student_id);
        $this->assertSame($manager->id, $contract->created_by);
        $this->assertDatabaseHas('allocations', [
            'contract_id' => $contract->id,
            'bed_id' => $bed->id,
            'released_at' => null,
        ]);
        $this->assertSame('occupied', $bed->refresh()->status);
    }

    public function test_contract_requires_an_approved_registration(): void
    {
        [$registration, $bed] = $this->approvedRegistrationWithBed();
        $registration->update(['status' => RoomRegistrationStatus::Pending]);

        $this->postJson(route('contracts.store'), $this->contractPayload($registration, $bed))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('room_registration_id');

        $this->assertDatabaseCount('contracts', 0);
        $this->assertDatabaseCount('allocations', 0);
        $this->assertSame('available', $bed->refresh()->status);
    }

    public function test_initial_bed_must_belong_to_the_requested_room(): void
    {
        [$registration, $bed] = $this->approvedRegistrationWithBed();
        $otherRoom = $this->createRoom($registration->room->building, ['room_number' => '102']);
        $otherBed = $this->createBed($otherRoom);

        $this->postJson(route('contracts.store'), $this->contractPayload($registration, $otherBed))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('bed_id');

        $this->assertDatabaseCount('contracts', 0);
        $this->assertSame('available', $bed->refresh()->status);
        $this->assertSame('available', $otherBed->refresh()->status);
    }

    public function test_it_rejects_a_student_who_violates_the_building_gender_policy(): void
    {
        $building = $this->createBuilding(['gender_policy' => 'male']);
        $room = $this->createRoom($building);
        $bed = $this->createBed($room);
        $student = $this->createStudent(['gender' => 'female']);
        $registration = $this->createApprovedRegistration($student, $room);

        $this->postJson(route('contracts.store'), $this->contractPayload($registration, $bed))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('bed_id');

        $this->assertDatabaseCount('contracts', 0);
    }

    public function test_room_capacity_cannot_be_exceeded_even_when_an_extra_bed_exists(): void
    {
        $building = $this->createBuilding();
        $room = $this->createRoom($building, ['capacity' => 1]);
        $firstBed = $this->createBed($room, ['bed_number' => 'G1']);
        $extraBed = $this->createBed($room, ['bed_number' => 'G2']);
        $firstRegistration = $this->createApprovedRegistration($this->createStudent(), $room);
        $this->createContract($firstRegistration, $firstBed);
        $secondRegistration = $this->createApprovedRegistration($this->createStudent(), $room);

        $this->postJson(
            route('contracts.store'),
            $this->contractPayload($secondRegistration, $extraBed),
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors('bed_id');

        $this->assertDatabaseCount('contracts', 1);
        $this->assertSame('available', $extraBed->refresh()->status);
    }

    public function test_a_student_cannot_have_two_active_contracts(): void
    {
        [$firstRegistration, $firstBed] = $this->approvedRegistrationWithBed();
        $this->createContract($firstRegistration, $firstBed);
        $firstRegistration->update([
            'status' => RoomRegistrationStatus::Completed,
            'completed_at' => now(),
        ]);

        $secondRoom = $this->createRoom($firstRegistration->room->building, ['room_number' => '102']);
        $secondBed = $this->createBed($secondRoom);
        $secondRegistration = $this->createApprovedRegistration($firstRegistration->student, $secondRoom);

        $this->postJson(
            route('contracts.store'),
            $this->contractPayload($secondRegistration, $secondBed),
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors('room_registration_id');

        $this->assertDatabaseCount('contracts', 1);
    }

    public function test_it_transfers_a_contract_and_preserves_allocation_history(): void
    {
        [$registration, $oldBed] = $this->approvedRegistrationWithBed(roomOverrides: ['capacity' => 1]);
        $contract = $this->createContract($registration, $oldBed);
        $newRoom = $this->createRoom($registration->room->building, [
            'room_number' => '202',
            'capacity' => 1,
        ]);
        $newBed = $this->createBed($newRoom);

        $this->postJson(route('contracts.transfer', $contract), [
            'bed_id' => $newBed->id,
            'reason' => 'Chuyển sang phòng yên tĩnh hơn.',
        ])
            ->assertOk()
            ->assertJsonPath('data.current_allocation.bed_id', $newBed->id)
            ->assertJsonCount(2, 'data.allocations');

        $oldAllocation = Allocation::query()->oldest('id')->firstOrFail();
        $this->assertSame(AllocationReleaseReason::Transferred, $oldAllocation->release_reason);
        $this->assertNotNull($oldAllocation->released_at);
        $this->assertSame('available', $oldBed->refresh()->status);
        $this->assertSame('occupied', $newBed->refresh()->status);
        $this->assertSame('available', $registration->room->refresh()->status);
        $this->assertSame('full', $newRoom->refresh()->status);
    }

    public function test_transfer_rejects_mixed_gender_room_and_keeps_current_bed(): void
    {
        $building = $this->createBuilding(['gender_policy' => 'mixed']);
        $femaleRoom = $this->createRoom($building, ['room_number' => '201', 'capacity' => 2]);
        $femaleBed = $this->createBed($femaleRoom, ['bed_number' => 'G1']);
        $targetBed = $this->createBed($femaleRoom, ['bed_number' => 'G2']);
        $femaleStudent = $this->createStudent(['gender' => 'female']);
        $femaleRegistration = $this->createApprovedRegistration($femaleStudent, $femaleRoom);
        $this->createContract($femaleRegistration, $femaleBed);

        $maleRoom = $this->createRoom($building, ['room_number' => '101']);
        $maleBed = $this->createBed($maleRoom);
        $maleStudent = $this->createStudent(['gender' => 'male']);
        $maleRegistration = $this->createApprovedRegistration($maleStudent, $maleRoom);
        $maleContract = $this->createContract($maleRegistration, $maleBed);

        $this->postJson(route('contracts.transfer', $maleContract), [
            'bed_id' => $targetBed->id,
            'reason' => 'Yêu cầu chuyển phòng.',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('bed_id');

        $this->assertDatabaseCount('allocations', 2);
        $this->assertSame('occupied', $maleBed->refresh()->status);
        $this->assertSame('available', $targetBed->refresh()->status);
    }

    public function test_it_renews_a_contract_without_losing_the_previous_end_date(): void
    {
        [$registration, $bed] = $this->approvedRegistrationWithBed();
        $contract = $this->createContract($registration, $bed);
        $previousEndDate = $contract->end_date->toDateString();
        $newEndDate = $contract->end_date->addMonths(5)->toDateString();

        $this->postJson(route('contracts.renew', $contract), [
            'new_end_date' => $newEndDate,
            'reason' => 'Gia hạn thêm một học kỳ.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.end_date', $newEndDate.'T00:00:00.000000Z')
            ->assertJsonCount(1, 'data.renewals');

        $renewal = $contract->renewals()->firstOrFail();
        $this->assertSame($previousEndDate, $renewal->previous_end_date->toDateString());
        $this->assertSame($newEndDate, $renewal->new_end_date->toDateString());
    }

    public function test_renewal_requires_a_later_end_date(): void
    {
        [$registration, $bed] = $this->approvedRegistrationWithBed();
        $contract = $this->createContract($registration, $bed);

        $this->postJson(route('contracts.renew', $contract), [
            'new_end_date' => $contract->end_date->toDateString(),
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('new_end_date');

        $this->assertDatabaseCount('contract_renewals', 0);
    }

    public function test_checkout_terminates_contract_releases_bed_and_completes_registration(): void
    {
        [$registration, $bed] = $this->approvedRegistrationWithBed(roomOverrides: ['capacity' => 1]);
        $contract = $this->createContract($registration, $bed);

        $this->patchJson(route('contracts.terminate', $contract), [
            'reason' => 'Sinh viên hoàn tất thủ tục trả phòng.',
            'release_reason' => AllocationReleaseReason::CheckedOut->value,
        ])
            ->assertOk()
            ->assertJsonPath('data.status', ContractStatus::Terminated->value)
            ->assertJsonPath('data.current_allocation', null);

        $this->assertSame(ContractStatus::Terminated, $contract->refresh()->status);
        $this->assertSame(RoomRegistrationStatus::Completed, $registration->refresh()->status);
        $this->assertNotNull($registration->completed_at);
        $this->assertSame('available', $bed->refresh()->status);
        $this->assertDatabaseHas('allocations', [
            'contract_id' => $contract->id,
            'release_reason' => AllocationReleaseReason::CheckedOut->value,
        ]);
    }

    public function test_completed_registration_allows_the_student_to_apply_again(): void
    {
        [$registration, $bed] = $this->approvedRegistrationWithBed();
        $contract = $this->createContract($registration, $bed);
        app(ContractService::class)->terminate($contract, [
            'reason' => 'Trả phòng.',
            'release_reason' => AllocationReleaseReason::CheckedOut->value,
        ], null);
        $newRoom = $this->createRoom($registration->room->building, ['room_number' => '303']);

        $this->postJson(route('registration.store'), [
            'student_code' => $registration->student->student_code,
            'full_name' => $registration->student->full_name,
            'email' => $registration->student->email,
            'phone_number' => $registration->student->phone_number,
            'gender' => $registration->student->gender,
            'date_of_birth' => $registration->student->date_of_birth->toDateString(),
            'room_id' => $newRoom->id,
        ])->assertCreated();

        $this->assertDatabaseCount('room_registrations', 2);
    }

    public function test_expiration_command_releases_due_contracts(): void
    {
        [$registration, $bed] = $this->approvedRegistrationWithBed();
        $contract = $this->createContract($registration, $bed);
        $contract->update([
            'start_date' => today()->subMonths(2),
            'end_date' => today()->subDay(),
        ]);

        $this->artisan('contracts:expire')
            ->expectsOutput('Đã xử lý 1 hợp đồng hết hạn.')
            ->assertSuccessful();

        $this->assertSame(ContractStatus::Expired, $contract->refresh()->status);
        $this->assertSame(RoomRegistrationStatus::Completed, $registration->refresh()->status);
        $this->assertSame('available', $bed->refresh()->status);
        $this->assertDatabaseHas('allocations', [
            'contract_id' => $contract->id,
            'release_reason' => AllocationReleaseReason::ContractExpired->value,
        ]);
    }

    public function test_eligible_endpoint_only_returns_unconsumed_approved_registrations(): void
    {
        [$eligible, $eligibleBed] = $this->approvedRegistrationWithBed();
        [$consumed, $consumedBed] = $this->approvedRegistrationWithBed();
        $this->createContract($consumed, $consumedBed);
        [$pending] = $this->approvedRegistrationWithBed();
        $pending->update(['status' => RoomRegistrationStatus::Pending]);

        $this->getJson(route('contracts.eligible-registrations'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $eligible->id)
            ->assertJsonPath('data.0.room.beds.0.id', $eligibleBed->id);
    }

    public function test_contract_management_pages_render_successfully(): void
    {
        [$registration, $bed] = $this->approvedRegistrationWithBed();

        $this->get(route('contracts.index'))
            ->assertOk()
            ->assertViewIs('contracts.index');
        $this->get(route('contracts.create'))
            ->assertOk()
            ->assertViewIs('contracts.create')
            ->assertSee($registration->student->full_name);

        $contract = $this->createContract($registration, $bed);

        $this->get(route('contracts.show', $contract))
            ->assertOk()
            ->assertViewIs('contracts.show')
            ->assertSee($contract->contract_code);
    }

    public function test_an_allocated_bed_cannot_be_manually_freed_or_deleted(): void
    {
        [$registration, $bed] = $this->approvedRegistrationWithBed();
        $this->createContract($registration, $bed);

        $this->from(route('beds.edit', $bed))
            ->put(route('beds.update', $bed), [
                'room_id' => $bed->room_id,
                'bed_number' => $bed->bed_number,
                'status' => 'available',
            ])
            ->assertRedirect(route('beds.edit', $bed))
            ->assertSessionHasErrors('status');

        $this->from(route('beds.index'))
            ->delete(route('beds.destroy', $bed))
            ->assertRedirect(route('beds.index'))
            ->assertSessionHasErrors('bed');

        $this->assertSame('occupied', $bed->refresh()->status);
        $this->assertDatabaseHas('beds', ['id' => $bed->id]);
    }

    /**
     * @param  array<string, mixed>  $buildingOverrides
     * @param  array<string, mixed>  $roomOverrides
     * @return array{RoomRegistration, Bed}
     */
    private function approvedRegistrationWithBed(
        array $buildingOverrides = [],
        array $roomOverrides = [],
    ): array {
        $building = $this->createBuilding($buildingOverrides);
        $room = $this->createRoom($building, $roomOverrides);
        $bed = $this->createBed($room);
        $student = $this->createStudent();

        return [$this->createApprovedRegistration($student, $room), $bed];
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createBuilding(array $overrides = []): Building
    {
        return Building::create([
            'code' => 'B'.uniqid(),
            'name' => 'Tòa kiểm thử',
            'floors' => 5,
            'gender_policy' => 'mixed',
            ...$overrides,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createRoom(Building $building, array $overrides = []): Room
    {
        return Room::create([
            'building_id' => $building->id,
            'room_number' => 'R'.uniqid(),
            'floor' => 1,
            'capacity' => 4,
            'status' => 'available',
            ...$overrides,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createBed(Room $room, array $overrides = []): Bed
    {
        return Bed::create([
            'room_id' => $room->id,
            'bed_number' => 'G'.uniqid(),
            'status' => 'available',
            ...$overrides,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createStudent(array $overrides = []): Student
    {
        $unique = uniqid();

        return Student::create([
            'student_code' => 'SV'.$unique,
            'full_name' => 'Sinh viên kiểm thử',
            'email' => $unique.'@example.com',
            'phone_number' => '0900000000',
            'gender' => 'male',
            'date_of_birth' => '2004-01-15',
            ...$overrides,
        ]);
    }

    private function createApprovedRegistration(Student $student, Room $room): RoomRegistration
    {
        return RoomRegistration::create([
            'student_id' => $student->id,
            'room_id' => $room->id,
            'status' => RoomRegistrationStatus::Approved,
            'registered_at' => now()->subDay(),
            'reviewed_at' => now(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function contractPayload(RoomRegistration $registration, Bed $bed): array
    {
        return [
            'room_registration_id' => $registration->id,
            'bed_id' => $bed->id,
            'start_date' => today()->toDateString(),
            'end_date' => today()->addMonths(5)->toDateString(),
            'monthly_rate' => 1200000,
        ];
    }

    private function createContract(RoomRegistration $registration, Bed $bed): Contract
    {
        return app(ContractService::class)->create(
            $this->contractPayload($registration, $bed),
            null,
        );
    }
}
