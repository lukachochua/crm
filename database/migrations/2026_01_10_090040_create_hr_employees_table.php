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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnUpdate();
            $table->foreignId('department_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('position_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('branch_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('contract_type_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('manager_user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->date('start_date');
            $table->date('contract_end_date')->nullable();
            $table->string('status');
            $table->text('notes')->nullable();
            $table->json('feedback_summary')->nullable();
            $table->dateTime('feedback_last_calculated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('contract_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
