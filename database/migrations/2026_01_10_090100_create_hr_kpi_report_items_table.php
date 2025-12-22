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
        Schema::create('kpi_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_report_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('kpi_template_item_id')->constrained()->cascadeOnUpdate();
            $table->decimal('self_score', 6, 2)->nullable();
            $table->decimal('manager_score', 6, 2)->nullable();
            $table->decimal('computed_score', 6, 2)->nullable();
            $table->text('self_comment')->nullable();
            $table->text('manager_comment')->nullable();
            $table->timestamps();

            $table->index('kpi_report_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_report_items');
    }
};
