<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('duration_days');
            $table->integer('max_documents')->default(1);
            $table->boolean('includes_meetings')->default(false);
            $table->integer('meetings_limit')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plans');
    }
};

