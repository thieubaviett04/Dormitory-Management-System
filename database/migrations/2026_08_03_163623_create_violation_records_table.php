<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('violation_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // Khóa ngoại tạm thời tới students
            $table->foreignId('violation_type_id')->constrained('violation_types')->cascadeOnDelete();
            $table->date('record_date');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violation_records');
    }
};
