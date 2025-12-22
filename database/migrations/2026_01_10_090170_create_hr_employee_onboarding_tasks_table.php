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
        Schema::create('employee_onboarding_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_onboarding_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('onboarding_template_task_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->string('status');
            $table->date('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_onboarding_tasks');
    }
};
