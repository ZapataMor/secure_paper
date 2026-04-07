<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_subscription_id')->constrained('user_subscriptions')->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('COP');
            $table->string('transaction_reference')->nullable();
            $table->string('proof_file')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validation_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('user_subscription_id');
            $table->index('payment_method_id');
            $table->index('validated_by');
            $table->index('status');
            $table->index('currency');
            $table->index('transaction_reference');
            $table->index(['user_id', 'status']);
            $table->index(['user_subscription_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

