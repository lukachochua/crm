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
        Schema::create('feedback_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_cycle_id')->constrained()->cascadeOnUpdate();
            $table->string('question_text');
            $table->decimal('weight', 5, 2)->nullable();
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
        Schema::dropIfExists('feedback_questions');
    }
};
