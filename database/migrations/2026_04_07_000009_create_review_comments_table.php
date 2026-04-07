<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_review_id')->constrained('document_reviews')->cascadeOnDelete();
            $table->string('section_name')->nullable();
            $table->integer('page_number')->nullable();
            $table->string('comment_type')->default('general');
            $table->text('comment_text');
            $table->string('priority')->default('medium');
            $table->timestamps();

            $table->index('document_review_id');
            $table->index('page_number');
            $table->index('comment_type');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_comments');
    }
};

