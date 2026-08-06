<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var list<string>
     */
    private const ORIGINAL_STATUSES = ['pending', 'approved', 'rejected', 'waitlist', 'cancelled'];

    /**
     * @var list<string>
     */
    private const STATUSES = ['pending', 'approved', 'rejected', 'waitlist', 'cancelled', 'completed'];

    public function up(): void
    {
        $this->replaceStatusConstraint(self::STATUSES);

        Schema::table('room_registrations', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });

        $this->restoreSqliteActiveIndex();
    }

    public function down(): void
    {
        DB::table('room_registrations')
            ->where('status', 'completed')
            ->update(['status' => 'approved']);

        Schema::table('room_registrations', function (Blueprint $table) {
            $table->dropForeign(['completed_by']);
            $table->dropColumn(['completed_at', 'completed_by']);
        });

        $this->replaceStatusConstraint(self::ORIGINAL_STATUSES);
        $this->restoreSqliteActiveIndex();
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

    private function restoreSqliteActiveIndex(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS room_registrations_one_active_per_student');
        DB::statement("CREATE UNIQUE INDEX room_registrations_one_active_per_student ON room_registrations (student_id) WHERE status IN ('pending', 'waitlist', 'approved')");
    }
};
