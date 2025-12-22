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
        Schema::create('survey_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('engagement_survey_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->dateTime('submitted_at');
            $table->timestamps();

            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_submissions');
    }
};
