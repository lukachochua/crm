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
        Schema::create('kpi_cycles', function (Blueprint $table) {
            $table->id();
            $table->string('period_type');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('label');
            $table->string('status');
            $table->timestamps();

            $table->index('period_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_cycles');
    }
};
