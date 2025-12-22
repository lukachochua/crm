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
        Schema::create('kpi_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('kpi_template_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('kpi_cycle_id')->constrained()->cascadeOnUpdate();
            $table->string('status');
            $table->dateTime('self_submitted_at')->nullable();
            $table->dateTime('manager_reviewed_at')->nullable();
            $table->decimal('self_score_total', 6, 2)->nullable();
            $table->decimal('manager_score_total', 6, 2)->nullable();
            $table->decimal('computed_score', 6, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['kpi_cycle_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_reports');
    }
};
