<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payment_plan_id')->constrained('payment_plans')->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->decimal('amount_paid', 12, 2)->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('payment_plan_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index(['user_id', 'status', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};

