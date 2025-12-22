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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->string('stage');
            $table->dateTime('applied_at')->nullable();
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('stage');
            $table->index('applied_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
