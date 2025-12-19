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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('action_type');
            $table->foreignId('performed_by')->constrained('users')->cascadeOnUpdate();
            $table->dateTime('performed_at');
            $table->json('before_state')->nullable();
            $table->json('after_state')->nullable();
            $table->decimal('amount_before', 12, 2)->nullable();
            $table->decimal('amount_after', 12, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index('action_type');
            $table->index('performed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
