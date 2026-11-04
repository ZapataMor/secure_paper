<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('submission_id');
            $table->string('payload_hash', 64);
            $table->string('status_message');
            $table->timestamps();
            $table->unique(['user_id', 'submission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_submissions');
    }
};
