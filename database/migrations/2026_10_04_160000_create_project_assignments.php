<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('project_assignments',function(Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('advisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('project_title',160);
            $table->string('progress_status')->default('received');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('project_assignments'); }
};
