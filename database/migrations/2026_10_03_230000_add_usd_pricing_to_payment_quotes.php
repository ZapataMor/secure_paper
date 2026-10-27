<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_quotes', function (Blueprint $table) {
            $table->string('additional_type')->nullable();
            $table->unsignedInteger('extra_words')->nullable();
            $table->unsignedInteger('units')->nullable();
            $table->string('billing_country', 2)->nullable();
            $table->string('tax_treatment')->nullable();
            $table->boolean('export_verified')->default(false);
            $table->unsignedBigInteger('usd_subtotal_cents')->nullable();
            $table->unsignedBigInteger('usd_tax_cents')->nullable();
            $table->unsignedBigInteger('usd_total_cents')->nullable();
            $table->unsignedInteger('tax_basis_points')->nullable();
            $table->unsignedBigInteger('cop_rate_units')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payment_quotes', function (Blueprint $table) {
            $table->dropColumn(['additional_type', 'extra_words', 'units', 'billing_country', 'tax_treatment', 'export_verified', 'usd_subtotal_cents', 'usd_tax_cents', 'usd_total_cents', 'tax_basis_points', 'cop_rate_units']);
        });
    }
};
