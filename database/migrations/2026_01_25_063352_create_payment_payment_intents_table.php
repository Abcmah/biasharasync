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
        Schema::create('payment_intents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('reference')->unique();
            $table->string('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('KES');
            $table->foreignId('payment_provider_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_mode_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider_reference')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index( 'reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_payment_intents');
    }
};
