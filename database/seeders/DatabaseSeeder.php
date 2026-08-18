<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            UserSeeder::class,
            BuildingSeeder::class,
            RoomSeeder::class,
            BedSeeder::class,
            ContractSeeder::class,
            ServiceTypeSeeder::class,
            ViolationTypeSeeder::class,
            ViolationRecordSeeder::class,
            UtilityReadingSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}
