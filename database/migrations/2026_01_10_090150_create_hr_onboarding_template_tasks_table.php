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
        Schema::create('onboarding_template_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('onboarding_template_id')->constrained()->cascadeOnUpdate();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order');
            $table->unsignedInteger('default_due_days')->nullable();
            $table->timestamps();

            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_template_tasks');
    }
};
