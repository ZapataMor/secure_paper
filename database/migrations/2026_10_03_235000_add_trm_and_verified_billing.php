<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('payment_billing_country', 2)->nullable();
            $table->unsignedSmallInteger('payment_tax_basis_points')->nullable();
            $table->boolean('payment_export_verified')->default(false);
            $table->timestamp('payment_billing_verified_at')->nullable();
        });
        Schema::table('wompi_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('usd_subtotal_cents')->nullable();
            $table->unsignedBigInteger('usd_tax_cents')->nullable();
            $table->unsignedBigInteger('usd_total_cents')->nullable();
            $table->unsignedBigInteger('cop_rate_units')->nullable();
            $table->date('trm_valid_from')->nullable();
            $table->date('trm_valid_until')->nullable();
            $table->string('trm_source')->nullable();
            $table->unsignedSmallInteger('tax_basis_points')->nullable();
            $table->string('additional_type')->nullable();
            $table->unsignedInteger('extra_words')->nullable();
            $table->unsignedInteger('additional_units')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('wompi_orders', fn (Blueprint $table) => $table->dropColumn(['usd_subtotal_cents', 'usd_tax_cents', 'usd_total_cents', 'cop_rate_units', 'trm_valid_from', 'trm_valid_until', 'trm_source', 'tax_basis_points', 'additional_type', 'extra_words', 'additional_units']));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['payment_billing_country', 'payment_tax_basis_points', 'payment_export_verified', 'payment_billing_verified_at']));
    }
};
