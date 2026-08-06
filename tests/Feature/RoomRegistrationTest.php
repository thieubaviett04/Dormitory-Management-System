<?php

namespace Tests\Feature;

use App\Enums\RoomRegistrationStatus;
use App\Models\Building;
use App\Models\Room;
use App\Models\RoomRegistration;
use App\Models\Student;
use App\Models\User;
use App\Services\RoomRegistrationService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_student_and_registration_with_valid_data(): void
    {
        $room = $this->createRoom();

        $response = $this->postJson(route('registration.store'), $this->validPayload($room));

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', RoomRegistrationStatus::Pending->value)
            ->assertJsonPath('data.room_id', $room->id)
            ->assertJsonStructure(['message', 'data' => ['student', 'room']]);
        $this->assertDatabaseHas('students', [
            'student_code' => 'SV001',
            'email' => 'student@example.com',
        ]);
        $this->assertDatabaseHas('room_registrations', [
            'room_id' => $room->id,
            'status' => RoomRegistrationStatus::Pending->value,
        ]);
    }

    public function test_it_rejects_a_room_that_does_not_exist(): void
    {
        $response = $this->postJson(
            route('registration.store'),
            $this->validPayload(roomId: 999999),
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('room_id');
        $this->assertDatabaseCount('students', 0);
        $this->assertDatabaseCount('room_registrations', 0);
    }

    public function test_it_rejects_a_room_under_maintenance(): void
    {
        $room = $this->createRoom(['status' => 'maintenance']);

        $response = $this->postJson(route('registration.store'), $this->validPayload($room));

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('room_id');
        $this->assertDatabaseCount('room_registrations', 0);
    }

    public function test_a_student_cannot_have_multiple_active_registrations(): void
    {
        $firstRoom = $this->createRoom(['room_number' => '101']);
        $secondRoom = $this->createRoom(['room_number' => '102']);

        $this->postJson(route('registration.store'), $this->validPayload($firstRoom))
            ->assertCreated();

        $this->postJson(route('registration.store'), $this->validPayload($secondRoom))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('student_code');

        $this->assertDatabaseCount('room_registrations', 1);
    }

    public function test_pending_registration_can_be_approved(): void
    {
        $registration = $this->createRegistration(RoomRegistrationStatus::Pending);
        $reviewer = User::factory()->create();

        $this->actingAs($reviewer)
            ->putJson(route('registration.update', $registration), [
                'status' => RoomRegistrationStatus::Approved->value,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', RoomRegistrationStatus::Approved->value)
            ->assertJsonPath('data.reviewed_by', $reviewer->id);

        $registration->refresh();
        $this->assertSame(RoomRegistrationStatus::Approved, $registration->status);
        $this->assertNotNull($registration->reviewed_at);
        $this->assertSame($reviewer->id, $registration->reviewed_by);
        $this->assertNull($registration->rejected_reason);
    }

    public function test_pending_registration_can_move_to_waitlist(): void
    {
        $registration = $this->createRegistration(RoomRegistrationStatus::Pending);

        $this->putJson(route('registration.update', $registration), [
            'status' => RoomRegistrationStatus::Waitlist->value,
        ])
            ->assertOk()
            ->assertJsonPath('data.status', RoomRegistrationStatus::Waitlist->value);

        $this->assertSame(RoomRegistrationStatus::Waitlist, $registration->refresh()->status);
        $this->assertNull($registration->reviewed_at);
    }

    public function test_waitlist_registration_can_be_approved(): void
    {
        $registration = $this->createRegistration(RoomRegistrationStatus::Waitlist);

        $this->putJson(route('registration.update', $registration), [
            'status' => RoomRegistrationStatus::Approved->value,
        ])
            ->assertOk()
            ->assertJsonPath('data.status', RoomRegistrationStatus::Approved->value);

        $this->assertSame(RoomRegistrationStatus::Approved, $registration->refresh()->status);
        $this->assertNotNull($registration->reviewed_at);
    }

    public function test_approved_registration_cannot_be_rejected(): void
    {
        $registration = $this->createRegistration(RoomRegistrationStatus::Approved);

        $this->putJson(route('registration.update', $registration), [
            'status' => RoomRegistrationStatus::Rejected->value,
            'rejected_reason' => 'Không còn chỗ.',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertSame(RoomRegistrationStatus::Approved, $registration->refresh()->status);
    }

    public function test_rejection_requires_a_reason(): void
    {
        $registration = $this->createRegistration(RoomRegistrationStatus::Pending);

        $this->putJson(route('registration.update', $registration), [
            'status' => RoomRegistrationStatus::Rejected->value,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('rejected_reason');

        $this->assertSame(RoomRegistrationStatus::Pending, $registration->refresh()->status);
    }

    public function test_cancelling_a_registration_preserves_the_record(): void
    {
        $registration = $this->createRegistration(RoomRegistrationStatus::Pending);

        $this->deleteJson(route('registration.cancel', $registration), [
            'cancellation_reason' => 'Thay đổi kế hoạch.',
        ])
            ->assertOk()
            ->assertJsonPath('data.status', RoomRegistrationStatus::Cancelled->value)
            ->assertJsonPath('data.cancellation_reason', 'Thay đổi kế hoạch.');

        $this->assertDatabaseCount('room_registrations', 1);
        $registration->refresh();
        $this->assertSame(RoomRegistrationStatus::Cancelled, $registration->status);
        $this->assertNotNull($registration->cancelled_at);
    }

    public function test_approved_registration_cannot_be_cancelled(): void
    {
        $registration = $this->createRegistration(RoomRegistrationStatus::Approved);

        $this->deleteJson(route('registration.cancel', $registration))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertSame(RoomRegistrationStatus::Approved, $registration->refresh()->status);
        $this->assertNull($registration->cancelled_at);
    }

    public function test_waitlist_endpoint_only_returns_waitlisted_registrations(): void
    {
        $waitlisted = $this->createRegistration(RoomRegistrationStatus::Waitlist);
        $pending = $this->createRegistration(RoomRegistrationStatus::Pending);

        $response = $this->getJson(route('registration.waitlist'));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $waitlisted->id)
            ->assertJsonPath('data.0.status', RoomRegistrationStatus::Waitlist->value)
            ->assertJsonMissing(['id' => $pending->id]);
    }

    public function test_student_registration_room_and_reviewer_relationships_work(): void
    {
        $reviewer = User::factory()->create();
        $registration = $this->createRegistration(RoomRegistrationStatus::Approved, [
            'reviewed_at' => now(),
            'reviewed_by' => $reviewer->id,
        ]);
        $student = $registration->student;
        $room = $registration->room;

        $this->assertTrue($student->roomRegistrations->contains($registration));
        $this->assertTrue($room->roomRegistrations->contains($registration));
        $this->assertTrue($registration->student->is($student));
        $this->assertTrue($registration->room->is($room));
        $this->assertTrue($registration->reviewer->is($reviewer));
        $this->assertNotNull($student->date_of_birth);
        $this->assertNotNull($registration->registered_at);
        $this->assertNotNull($registration->reviewed_at);
    }

    public function test_registration_transaction_rolls_back_student_when_registration_insert_fails(): void
    {
        try {
            app(RoomRegistrationService::class)->register(
                $this->validPayload(roomId: 999999),
            );
            $this->fail('The invalid room foreign key should reject the registration insert.');
        } catch (QueryException) {
            // The expected database error proves the service reached the insert.
        }

        $this->assertDatabaseMissing('students', ['student_code' => 'SV001']);
        $this->assertDatabaseCount('room_registrations', 0);
    }

    public function test_api_returns_consistent_json_and_status_for_missing_bound_models(): void
    {
        $this->getJson(route('registration.status', 999999))
            ->assertNotFound()
            ->assertJsonStructure(['message']);

        $this->putJson(route('registration.update', 999999), [
            'status' => RoomRegistrationStatus::Approved->value,
        ])
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    }

    public function test_existing_student_profile_is_updated_without_creating_a_duplicate(): void
    {
        $student = $this->createStudent([
            'full_name' => 'Tên cũ',
            'phone_number' => null,
        ]);
        $room = $this->createRoom();
        $payload = $this->validPayload($room, [
            'full_name' => 'Tên mới',
            'phone_number' => '0900000000',
        ]);

        $this->postJson(route('registration.store'), $payload)->assertCreated();

        $this->assertDatabaseCount('students', 1);
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'full_name' => 'Tên mới',
            'phone_number' => '0900000000',
        ]);
    }

    public function test_email_conflict_is_returned_as_validation_error_instead_of_server_error(): void
    {
        $this->createStudent([
            'student_code' => 'SV002',
            'email' => 'used@example.com',
        ]);
        $room = $this->createRoom();

        $this->postJson(route('registration.store'), $this->validPayload($room, [
            'email' => 'used@example.com',
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseMissing('students', ['student_code' => 'SV001']);
    }

    public function test_future_birth_date_is_rejected(): void
    {
        $room = $this->createRoom();

        $this->postJson(route('registration.store'), $this->validPayload($room, [
            'date_of_birth' => now()->addDay()->toDateString(),
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('date_of_birth');
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function validPayload(Room|int|null $room = null, array $overrides = [], ?int $roomId = null): array
    {
        return [
            'student_code' => 'SV001',
            'full_name' => 'Nguyễn Văn A',
            'email' => 'student@example.com',
            'phone_number' => '0912345678',
            'gender' => 'male',
            'date_of_birth' => '2004-01-15',
            'room_id' => $roomId ?? ($room instanceof Room ? $room->id : $room),
            ...$overrides,
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createRoom(array $overrides = []): Room
    {
        $building = Building::create([
            'code' => 'A'.uniqid(),
            'name' => 'Tòa A',
            'floors' => 5,
        ]);

        return Room::create([
            'building_id' => $building->id,
            'room_number' => '101',
            'floor' => 1,
            'capacity' => 4,
            'status' => 'available',
            ...$overrides,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createStudent(array $overrides = []): Student
    {
        return Student::create([
            'student_code' => 'SV001',
            'full_name' => 'Nguyễn Văn A',
            'email' => 'student@example.com',
            'phone_number' => '0912345678',
            'gender' => 'male',
            'date_of_birth' => '2004-01-15',
            ...$overrides,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createRegistration(
        RoomRegistrationStatus $status,
        array $overrides = [],
    ): RoomRegistration {
        return RoomRegistration::create([
            'student_id' => $this->createStudent([
                'student_code' => 'SV'.uniqid(),
                'email' => uniqid().'@example.com',
            ])->id,
            'room_id' => $this->createRoom()->id,
            'status' => $status,
            'registered_at' => now(),
            ...$overrides,
        ]);
    }
}
