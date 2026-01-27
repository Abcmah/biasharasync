<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('payment_mode_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('code')->nullable();
            $table->string('display_name');
            $table->enum('env', ['sandbox', 'live'])->default('sandbox');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('priority')->default(1);

            $table->timestamps();
            $table->index(['env', 'is_active']);

            $table->unique(['company_id', 'payment_mode_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_providers');
    }
};
