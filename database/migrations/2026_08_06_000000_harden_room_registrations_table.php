<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ACTIVE_INDEX = 'room_registrations_one_active_per_student';

    /**
     * @var list<string>
     */
    private const ORIGINAL_STATUSES = ['pending', 'approved', 'rejected', 'waitlist'];

    /**
     * @var list<string>
     */
    private const STATUSES = ['pending', 'approved', 'rejected', 'waitlist', 'cancelled'];

    public function up(): void
    {
        $this->replaceStatusConstraint(self::STATUSES);

        Schema::table('room_registrations', function (Blueprint $table) {
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->restrictOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('rejected_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
        });

        DB::statement(sprintf(
            "CREATE UNIQUE INDEX %s ON room_registrations (student_id) WHERE status IN ('pending', 'waitlist', 'approved')",
            self::ACTIVE_INDEX,
        ));
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS '.self::ACTIVE_INDEX);
        DB::table('room_registrations')
            ->where('status', 'cancelled')
            ->update(['status' => 'rejected']);

        Schema::table('room_registrations', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'reviewed_at',
                'reviewed_by',
                'rejected_reason',
                'cancelled_at',
                'cancellation_reason',
            ]);
        });

        $this->replaceStatusConstraint(self::ORIGINAL_STATUSES);
    }

    /**
     * @param  list<string>  $statuses
     */
    private function replaceStatusConstraint(array $statuses): void
    {
        if (DB::getDriverName() === 'pgsql') {
            $allowed = implode(', ', array_map(
                fn (string $status): string => DB::getPdo()->quote($status),
                $statuses,
            ));

            DB::statement('ALTER TABLE room_registrations DROP CONSTRAINT IF EXISTS room_registrations_status_check');
            DB::statement("ALTER TABLE room_registrations ADD CONSTRAINT room_registrations_status_check CHECK (status IN ({$allowed}))");

            return;
        }

        Schema::table('room_registrations', function (Blueprint $table) use ($statuses) {
            $table->enum('status', $statuses)->default('pending')->change();
        });
    }
};
