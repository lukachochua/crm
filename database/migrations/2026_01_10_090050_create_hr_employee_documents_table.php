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
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnUpdate();
            $table->string('document_type');
            $table->string('title');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->date('expires_on')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnUpdate();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('document_type');
            $table->index('expires_on');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
