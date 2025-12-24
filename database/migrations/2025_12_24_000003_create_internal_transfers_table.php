<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internal_transfers', function (Blueprint $table): void {
            $table->id();
            $table->string('reference');
            $table->string('source_location');
            $table->string('destination_location');
            $table->text('description')->nullable();
            $table->string('status');
            $table->foreignId('requested_by')->constrained('users')->cascadeOnUpdate();
            $table->dateTime('requested_at');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_transfers');
    }
};
