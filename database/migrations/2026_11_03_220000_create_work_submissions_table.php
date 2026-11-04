<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('submission_id');
            $table->string('payload_hash', 64);
            $table->timestamps();
            $table->unique(['sender_id', 'client_id', 'submission_id'], 'work_submissions_receipt_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_submissions');
    }
};
