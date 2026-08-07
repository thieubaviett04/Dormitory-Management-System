<?php

namespace Tests\Feature;

use App\Enums\AllocationReleaseReason;
use App\Enums\ContractStatus;
use App\Enums\RoomRegistrationStatus;
use App\Models\Allocation;
use App\Models\Bed;
use App\Models\Contract;
use Database\Seeders\BedSeeder;
use Database\Seeders\BuildingSeeder;
use Database\Seeders\ContractSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RoomSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_repeatable_contract_management_scenarios(): void
    {
        $this->seed([
            BuildingSeeder::class,
            RoomSeeder::class,
            BedSeeder::class,
        ]);

        $this->seed(ContractSeeder::class);
        $this->seed(ContractSeeder::class);

        $this->assertDatabaseCount('students', 5);
        $this->assertDatabaseCount('room_registrations', 5);
        $this->assertDatabaseCount('contracts', 5);
        $this->assertDatabaseCount('allocations', 6);
        $this->assertDatabaseCount('contract_renewals', 1);

        $this->assertSame(3, Contract::query()->where('status', ContractStatus::Active)->count());
        $this->assertDatabaseHas('contracts', [
            'contract_code' => 'SEED-HD-TERMINATED-001',
            'status' => ContractStatus::Terminated->value,
        ]);
        $this->assertDatabaseHas('contracts', [
            'contract_code' => 'SEED-HD-EXPIRED-001',
            'status' => ContractStatus::Expired->value,
        ]);

        $transferredContract = Contract::query()
            ->where('contract_code', 'SEED-HD-TRANSFER-001')
            ->firstOrFail();
        $this->assertSame(2, $transferredContract->allocations()->count());
        $this->assertDatabaseHas('allocations', [
            'contract_id' => $transferredContract->id,
            'release_reason' => AllocationReleaseReason::Transferred->value,
        ]);

        $renewedContract = Contract::query()
            ->where('contract_code', 'SEED-HD-RENEW-001')
            ->firstOrFail();
        $this->assertSame(1, $renewedContract->renewals()->count());

        $closedRegistrations = Contract::query()
            ->whereIn('status', [ContractStatus::Terminated, ContractStatus::Expired])
            ->with('roomRegistration')
            ->get()
            ->pluck('roomRegistration');
        $this->assertTrue(
            $closedRegistrations->every(
                fn ($registration) => $registration->status === RoomRegistrationStatus::Completed,
            ),
        );

        $this->assertSame('occupied', $this->bed('A1', '101', 'G1')->status);
        $this->assertSame('occupied', $this->bed('T1', '101', 'G1')->status);
        $this->assertSame('available', $this->bed('T1', '101', 'G2')->status);
        $this->assertSame('available', $this->bed('A1', '102', 'G1')->status);

        $this->assertSame(3, Allocation::query()->whereNull('released_at')->count());
    }

    public function test_database_seeder_includes_contract_management_scenarios(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('contracts', 5);
        $this->assertDatabaseCount('allocations', 6);
        $this->assertDatabaseCount('contract_renewals', 1);
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
}
