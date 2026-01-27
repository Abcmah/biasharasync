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
        Schema::create('payment_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('payment_provider_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('payment_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('provider_event')->nullable();
            $table->string('provider_reference')->nullable();
            $table->string('signature')->nullable();

            $table->string('http_method', 10)->default('POST');
            $table->string('endpoint')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->integer('http_status')->nullable();

            $table->json('headers')->nullable();
            $table->json('payload');
            $table->json('response')->nullable();
            $table->string('status')->default('received');

            $table->text('error_message')->nullable();
            $table->index(['payment_provider_id', 'provider_reference']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_payment_webhooks');
    }
};
