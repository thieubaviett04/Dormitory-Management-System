<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ACTIVE_BED_INDEX = 'allocations_one_active_per_bed';

    private const ACTIVE_CONTRACT_INDEX = 'allocations_one_active_per_contract';

    public function up(): void
    {
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')
                ->constrained('contracts')
                ->restrictOnDelete();
            $table->foreignId('bed_id')
                ->constrained('beds')
                ->restrictOnDelete();
            $table->timestamp('allocated_at');
            $table->timestamp('released_at')->nullable();
            $table->enum('release_reason', [
                'transferred',
                'checked_out',
                'contract_expired',
                'contract_terminated',
            ])->nullable();
            $table->foreignId('allocated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('released_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('notes')->nullable();
            $table->text('release_notes')->nullable();
            $table->timestamps();

            $table->index(['contract_id', 'released_at']);
            $table->index(['bed_id', 'released_at']);
        });

        if (in_array(DB::getDriverName(), ['pgsql', 'sqlite'], true)) {
            DB::statement(sprintf(
                'CREATE UNIQUE INDEX %s ON allocations (bed_id) WHERE released_at IS NULL',
                self::ACTIVE_BED_INDEX,
            ));
            DB::statement(sprintf(
                'CREATE UNIQUE INDEX %s ON allocations (contract_id) WHERE released_at IS NULL',
                self::ACTIVE_CONTRACT_INDEX,
            ));
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE allocations ADD CONSTRAINT allocations_dates_check CHECK (released_at IS NULL OR released_at >= allocated_at)');
            DB::statement('ALTER TABLE allocations ADD CONSTRAINT allocations_release_check CHECK ((released_at IS NULL AND release_reason IS NULL) OR (released_at IS NOT NULL AND release_reason IS NOT NULL))');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('allocations');
    }
};
