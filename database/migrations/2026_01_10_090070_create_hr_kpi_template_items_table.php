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
        Schema::create('kpi_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_template_id')->constrained()->cascadeOnUpdate();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('weight', 5, 2);
            $table->unsignedInteger('sort_order');
            $table->timestamps();

            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_template_items');
    }
};
