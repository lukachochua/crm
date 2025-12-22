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
        Schema::create('feedback_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_cycle_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('employee_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('rater_user_id')->constrained('users')->cascadeOnUpdate();
            $table->string('rater_type');
            $table->string('status');
            $table->dateTime('requested_at');
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('rater_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_requests');
    }
};
