<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ACTIVE_STUDENT_INDEX = 'contracts_one_active_per_student';

    public function up(): void
    {
        Schema::create('contract_sequences', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedInteger('last_number')->default(0);
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_code', 30)->unique();
            $table->foreignId('room_registration_id')
                ->unique()
                ->constrained('room_registrations')
                ->restrictOnDelete();
            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('monthly_rate', 12, 2);
            $table->enum('status', ['active', 'expired', 'terminated'])->default('active');
            $table->timestamp('signed_at');
            $table->timestamp('terminated_at')->nullable();
            $table->text('termination_reason')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['status', 'end_date']);
        });

        if (in_array(DB::getDriverName(), ['pgsql', 'sqlite'], true)) {
            DB::statement(sprintf(
                "CREATE UNIQUE INDEX %s ON contracts (student_id) WHERE status = 'active'",
                self::ACTIVE_STUDENT_INDEX,
            ));
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE contracts ADD CONSTRAINT contracts_dates_check CHECK (end_date > start_date)');
            DB::statement('ALTER TABLE contracts ADD CONSTRAINT contracts_monthly_rate_check CHECK (monthly_rate >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('contract_sequences');
    }
};
