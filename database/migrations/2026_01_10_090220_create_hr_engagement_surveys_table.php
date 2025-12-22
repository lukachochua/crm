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
        Schema::create('engagement_surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status');
            $table->dateTime('opens_at')->nullable();
            $table->dateTime('closes_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('opens_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engagement_surveys');
    }
};
