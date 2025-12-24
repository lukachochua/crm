<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_return_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_return_id')->constrained('customer_returns')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('item_name');
            $table->unsignedInteger('quantity');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_return_items');
    }
};
