<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('package');
            $table->string('description', 500);
            $table->unsignedBigInteger('amount_in_cents');
            $table->unsignedInteger('access_days')->default(0);
            $table->timestamp('expires_at');
            $table->string('status')->default('offered');
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
        Schema::create('wompi_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('payment_quote_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('user_subscription_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('reference')->unique();
            $table->string('transaction_id')->nullable()->unique();
            $table->string('environment');
            $table->boolean('is_additional')->default(false);
            $table->timestamp('expires_at');
            $table->string('package');
            $table->string('description', 500);
            $table->unsignedBigInteger('amount_in_cents');
            $table->string('currency', 3)->default('COP');
            $table->unsignedInteger('access_days');
            $table->string('status')->default('PENDING');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wompi_orders');
        Schema::dropIfExists('payment_quotes');
    }
};
