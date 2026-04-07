<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('signature_file');
            $table->string('certificate_file')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('certificate_serial')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('certificate_serial');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_signatures');
    }
};

