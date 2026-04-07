<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('document_version_id')->nullable()->constrained('document_versions')->nullOnDelete();
            $table->foreignId('reviewed_by')->constrained('users')->restrictOnDelete();
            $table->integer('review_number');
            $table->text('general_observations')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index('document_id');
            $table->index('document_version_id');
            $table->index('reviewed_by');
            $table->index('status');
            $table->index(['document_id', 'status']);
            $table->unique(['document_id', 'review_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_reviews');
    }
};

