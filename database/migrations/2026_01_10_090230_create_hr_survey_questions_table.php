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
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('engagement_survey_id')->constrained()->cascadeOnUpdate();
            $table->string('question_text');
            $table->string('question_type');
            $table->json('config')->nullable();
            $table->unsignedInteger('sort_order');
            $table->timestamps();

            $table->index('question_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};
