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
        Schema::create('training_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('employee_id')->constrained()->cascadeOnUpdate();
            $table->string('attendance_status');
            $table->string('result_status')->nullable();
            $table->decimal('result_score', 6, 2)->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('attendance_status');
            $table->index('result_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_participants');
    }
};
