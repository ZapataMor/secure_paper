<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->integer('version_number');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('document_id');
            $table->index('uploaded_by');
            $table->unique(['document_id', 'version_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};

