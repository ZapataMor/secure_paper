<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('meeting_url', 2048);
            $table->string('platform')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->string('status')->default('scheduled');
            $table->timestamps();

            $table->index('user_id');
            $table->index('admin_id');
            $table->index('document_id');
            $table->index('scheduled_at');
            $table->index('status');
            $table->index(['user_id', 'status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_links');
    }
};

