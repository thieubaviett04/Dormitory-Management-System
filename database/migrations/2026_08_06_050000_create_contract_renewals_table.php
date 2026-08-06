<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')
                ->constrained('contracts')
                ->restrictOnDelete();
            $table->date('previous_end_date');
            $table->date('new_end_date');
            $table->timestamp('renewed_at');
            $table->foreignId('renewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['contract_id', 'renewed_at']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE contract_renewals ADD CONSTRAINT contract_renewals_dates_check CHECK (new_end_date > previous_end_date)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_renewals');
    }
};
